#!/bin/bash

# Script de diagnostic pour le stockage externe
# Ce script vérifie l'état du montage et de la configuration

LOG_FILE="/var/log/storage-diagnostic.log"
ENV_FILE="/home/video-app/.env"

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction de logging avec couleur
log() {
    local color="$1"
    local message="$2"
    echo -e "${color}$(date '+%Y-%m-%d %H:%M:%S') - $message${NC}" | tee -a "$LOG_FILE"
}

# Fonction pour vérifier les disques montés
check_mounted_disks() {
    log "$YELLOW" "=== ÉTAT DES DISQUES MONTÉS ==="
    
    echo "Disques actuellement montés :"
    df -h | grep -E "^/dev/" | while read line; do
        echo "  $line"
    done
    
    echo ""
    echo "Tous les périphériques de stockage :"
    lsblk -o NAME,SIZE,TYPE,MOUNTPOINT,FSTYPE
}

# Fonction pour vérifier la configuration
check_configuration() {
    log "$YELLOW" "=== CONFIGURATION ACTUELLE ==="
    
    if [[ -f "$ENV_FILE" ]]; then
        echo "Fichier .env trouvé :"
        if grep -q "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE"; then
            local storage_path=$(grep "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE" | cut -d'=' -f2)
            echo "  EXTERNAL_STORAGE_PATH=$storage_path"
            
            # Vérifier si le chemin existe
            if [[ -d "$storage_path" ]]; then
                log "$GREEN" "  ✓ Le répertoire existe"
                
                # Vérifier les permissions
                if [[ -w "$storage_path" ]]; then
                    log "$GREEN" "  ✓ Le répertoire est accessible en écriture"
                else
                    log "$RED" "  ✗ Problème de permissions sur le répertoire"
                fi
                
                # Vérifier les sous-dossiers
                if [[ -d "$storage_path/uploads" ]] && [[ -d "$storage_path/videos" ]]; then
                    log "$GREEN" "  ✓ Dossiers uploads et videos présents"
                else
                    log "$RED" "  ✗ Dossiers uploads ou videos manquants"
                fi
            else
                log "$RED" "  ✗ Le répertoire n'existe pas"
            fi
        else
            log "$RED" "  ✗ Variable EXTERNAL_STORAGE_PATH non définie"
        fi
    else
        log "$RED" "  ✗ Fichier .env introuvable"
    fi
}

# Fonction pour vérifier Docker
check_docker_status() {
    log "$YELLOW" "=== ÉTAT DE DOCKER ==="
    
    if systemctl is-active --quiet docker; then
        log "$GREEN" "✓ Service Docker actif"
    else
        log "$RED" "✗ Service Docker inactif"
    fi
    
    echo ""
    echo "Conteneurs Docker :"
    docker ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
}

# Fonction pour vérifier les logs récents
check_recent_logs() {
    log "$YELLOW" "=== LOGS RÉCENTS ==="
    
    echo "Dernières entrées du log de montage automatique :"
    if [[ -f "/var/log/auto-mount-storage.log" ]]; then
        tail -10 "/var/log/auto-mount-storage.log"
    else
        echo "  Aucun log de montage automatique trouvé"
    fi
    
    echo ""
    echo "Dernières entrées du log du gestionnaire de stockage :"
    if [[ -f "/var/log/storage-manager.log" ]]; then
        tail -10 "/var/log/storage-manager.log"
    else
        echo "  Aucun log du gestionnaire de stockage trouvé"
    fi
}

# Fonction pour proposer des solutions
suggest_solutions() {
    log "$YELLOW" "=== SUGGESTIONS DE DÉPANNAGE ==="
    
    echo "Si vous rencontrez des problèmes :"
    echo ""
    echo "1. Redémarrer le service de montage automatique :"
    echo "   sudo /home/video-app/scripts/auto-mount-storage.sh"
    echo ""
    echo "2. Vérifier manuellement les disques disponibles :"
    echo "   lsblk -f"
    echo ""
    echo "3. Redémarrer les services Docker :"
    echo "   sudo systemctl restart betweenus"
    echo ""
    echo "4. Montage manuel d'urgence (remplacez /dev/sdX1 par votre disque) :"
    echo "   sudo mkdir -p /mnt/betweenus-storage"
    echo "   sudo mount /dev/sdX1 /mnt/betweenus-storage"
    echo ""
}

# Fonction principale
main() {
    log "$GREEN" "=== DIAGNOSTIC DU STOCKAGE BETWEENUS ==="
    
    check_mounted_disks
    echo ""
    check_configuration
    echo ""
    check_docker_status
    echo ""
    check_recent_logs
    echo ""
    suggest_solutions
    
    log "$GREEN" "=== FIN DU DIAGNOSTIC ==="
}

# Exécuter le diagnostic
main "$@"
