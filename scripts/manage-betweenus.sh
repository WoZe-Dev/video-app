#!/bin/bash

# Script de maintenance et gestion BetweenUs
# Fournit des outils de gestion pour le système BetweenUs

set -e

LOG_FILE="/var/log/betweenus-management.log"
PROJECT_DIR="/home/betweenus"

# Fonction de logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Fonction pour afficher l'état du système
show_status() {
    echo "=== ÉTAT DU SYSTÈME BETWEENUS ==="
    echo ""
    
    echo "📊 Services Docker:"
    docker compose -f "$PROJECT_DIR/docker-compose.yaml" ps
    echo ""
    
    echo "🗄️ Stockage:"
    df -h | grep -E "(Filesystem|/mnt|uploads|videos)" || df -h | head -1
    echo ""
    
    echo "📁 Espace utilisé par les uploads:"
    if [[ -d "/mnt/sauvegarde/uploads" ]]; then
        du -sh /mnt/sauvegarde/uploads 2>/dev/null || echo "N/A"
    else
        echo "Dossier uploads non trouvé"
    fi
    echo ""
    
    echo "🎥 Espace utilisé par les vidéos:"
    if [[ -d "/mnt/sauvegarde/videos" ]]; then
        du -sh /mnt/sauvegarde/videos 2>/dev/null || echo "N/A"
    else
        echo "Dossier videos non trouvé"
    fi
    echo ""
    
    echo "🌐 Services réseau:"
    echo "- Application: http://192.168.1.68:8000"
    echo "- phpMyAdmin: http://192.168.1.68:8080"
    echo ""
    
    echo "🔄 Service systemd:"
    systemctl is-active betweenus.service
    echo ""
}

# Fonction pour redémarrer les services
restart_services() {
    log "Redémarrage des services BetweenUs..."
    
    cd "$PROJECT_DIR"
    docker compose down
    sleep 3
    docker compose up -d
    
    log "Services redémarrés avec succès"
}

# Fonction pour nettoyer le système
cleanup_system() {
    log "Nettoyage du système..."
    
    echo "Nettoyage des conteneurs arrêtés..."
    docker container prune -f
    
    echo "Nettoyage des images inutilisées..."
    docker image prune -f
    
    echo "Nettoyage des volumes orphelins..."
    docker volume prune -f
    
    echo "Nettoyage des réseaux inutilisés..."
    docker network prune -f
    
    log "Nettoyage terminé"
}

# Fonction pour sauvegarder la base de données
backup_database() {
    local backup_dir="/mnt/sauvegarde/backups"
    local backup_file="$backup_dir/database_backup_$(date +%Y%m%d_%H%M%S).sql"
    
    log "Sauvegarde de la base de données..."
    
    mkdir -p "$backup_dir"
    
    docker compose -f "$PROJECT_DIR/docker-compose.yaml" exec -T mariadb mysqldump \
        -u root -p'.Optile17' --all-databases > "$backup_file"
    
    # Compression de la sauvegarde
    gzip "$backup_file"
    
    log "Sauvegarde créée: ${backup_file}.gz"
    
    # Nettoyage des anciennes sauvegardes (garde les 7 dernières)
    find "$backup_dir" -name "database_backup_*.sql.gz" -type f -mtime +7 -delete
    
    log "Anciennes sauvegardes nettoyées"
}

# Fonction pour restaurer la base de données
restore_database() {
    local backup_dir="/mnt/sauvegarde/backups"
    
    echo "Sauvegardes disponibles:"
    ls -la "$backup_dir"/database_backup_*.sql.gz 2>/dev/null || {
        echo "Aucune sauvegarde trouvée"
        return 1
    }
    
    echo ""
    read -p "Entrez le nom complet du fichier de sauvegarde: " backup_file
    
    if [[ ! -f "$backup_file" ]]; then
        echo "Fichier non trouvé: $backup_file"
        return 1
    fi
    
    echo "ATTENTION: Cette opération va écraser la base de données actuelle!"
    read -p "Êtes-vous sûr de vouloir continuer? (oui/non): " confirm
    
    if [[ "$confirm" != "oui" ]]; then
        echo "Opération annulée"
        return 1
    fi
    
    log "Restauration de la base de données depuis: $backup_file"
    
    # Décompression et restauration
    gunzip -c "$backup_file" | docker compose -f "$PROJECT_DIR/docker-compose.yaml" exec -T mariadb mysql -u root -p'.Optile17'
    
    log "Base de données restaurée avec succès"
}

# Fonction pour changer le disque de stockage
change_storage_disk() {
    echo "=== CHANGEMENT DE DISQUE DE STOCKAGE ==="
    echo ""
    
    echo "Disques disponibles:"
    lsblk -o NAME,SIZE,TYPE,MOUNTPOINT
    echo ""
    
    read -p "Entrez le nouveau chemin de montage (ex: /mnt/nouveau-disque): " new_path
    
    if [[ ! -d "$new_path" ]]; then
        echo "Le chemin spécifié n'existe pas ou n'est pas un dossier"
        return 1
    fi
    
    echo "Changement du stockage vers: $new_path"
    echo "Cela va:"
    echo "1. Arrêter les services"
    echo "2. Modifier la configuration"
    echo "3. Créer les dossiers nécessaires"
    echo "4. Redémarrer les services"
    echo ""
    
    read -p "Continuer? (oui/non): " confirm
    if [[ "$confirm" != "oui" ]]; then
        echo "Opération annulée"
        return 1
    fi
    
    log "Changement de stockage vers: $new_path"
    
    # Arrêter les services
    cd "$PROJECT_DIR"
    docker compose down
    
    # Modifier le fichier .env
    sed -i "s|^EXTERNAL_STORAGE_PATH=.*|EXTERNAL_STORAGE_PATH=$new_path|" "$PROJECT_DIR/.env"
    
    # Créer les dossiers nécessaires
    mkdir -p "$new_path/uploads"
    mkdir -p "$new_path/videos"
    mkdir -p "$new_path/backups"
    
    # Définir les permissions
    chown -R 1000:1000 "$new_path/uploads"
    chown -R 1000:1000 "$new_path/videos"
    chown -R toto:toto "$new_path/backups"
    
    # Redémarrer les services
    docker compose up -d
    
    log "Changement de stockage terminé avec succès"
}

# Fonction pour afficher les logs
show_logs() {
    echo "=== LOGS DES SERVICES ==="
    echo ""
    
    echo "1. Logs de tous les services"
    echo "2. Logs de nginx"
    echo "3. Logs de php"
    echo "4. Logs de mariadb"
    echo "5. Logs de phpmyadmin"
    echo "6. Logs de gestion BetweenUs"
    echo ""
    
    read -p "Choisissez une option (1-6): " choice
    
    case $choice in
        1)
            docker compose -f "$PROJECT_DIR/docker-compose.yaml" logs --tail=50
            ;;
        2)
            docker compose -f "$PROJECT_DIR/docker-compose.yaml" logs nginx --tail=50
            ;;
        3)
            docker compose -f "$PROJECT_DIR/docker-compose.yaml" logs php --tail=50
            ;;
        4)
            docker compose -f "$PROJECT_DIR/docker-compose.yaml" logs mariadb --tail=50
            ;;
        5)
            docker compose -f "$PROJECT_DIR/docker-compose.yaml" logs phpmyadmin --tail=50
            ;;
        6)
            tail -50 "$LOG_FILE"
            ;;
        *)
            echo "Option invalide"
            ;;
    esac
}

# Menu principal
show_menu() {
    echo "=== GESTIONNAIRE BETWEENUS ==="
    echo "1. Afficher l'état du système"
    echo "2. Redémarrer les services"
    echo "3. Nettoyer le système Docker"
    echo "4. Sauvegarder la base de données"
    echo "5. Restaurer la base de données"
    echo "6. Changer le disque de stockage"
    echo "7. Afficher les logs"
    echo "8. Tester le gestionnaire de stockage automatique"
    echo "9. Quitter"
    echo ""
}

# Fonction principale
main() {
    if [[ $EUID -ne 0 ]] && [[ "$1" != "status" ]]; then
        echo "Ce script nécessite les privilèges sudo pour certaines opérations."
        echo "Exécutez avec: sudo $0"
        exit 1
    fi
    
    log "=== Démarrage du gestionnaire BetweenUs ==="
    
    # Si un argument est passé, exécuter directement
    case "$1" in
        "status")
            show_status
            exit 0
            ;;
        "restart")
            restart_services
            exit 0
            ;;
        "backup")
            backup_database
            exit 0
            ;;
        "cleanup")
            cleanup_system
            exit 0
            ;;
    esac
    
    while true; do
        show_menu
        read -p "Choisissez une option (1-9): " choice
        
        case $choice in
            1)
                show_status
                ;;
            2)
                restart_services
                ;;
            3)
                cleanup_system
                ;;
            4)
                backup_database
                ;;
            5)
                restore_database
                ;;
            6)
                change_storage_disk
                ;;
            7)
                show_logs
                ;;
            8)
                "$PROJECT_DIR/scripts/storage-manager.sh"
                ;;
            9)
                log "Fin du gestionnaire BetweenUs"
                exit 0
                ;;
            *)
                echo "Option invalide"
                ;;
        esac
        
        echo ""
        read -p "Appuyez sur Entrée pour continuer..."
        echo ""
    done
}

# Exécuter le script principal
main "$@"
