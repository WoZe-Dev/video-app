#!/bin/bash

# Script de gestion automatique du stockage externe
# Ce script détecte automatiquement les disques externes et configure le stockage

LOG_FILE="/var/log/storage-manager.log"
ENV_FILE="/home/betweenus/.env"
COMPOSE_FILE="/home/betweenus/docker-compose.yaml"

# Fonction de logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Fonction pour détecter les disques externes
detect_external_storage() {
    log "Détection des disques externes..."
    
    # Recherche de disques de grande capacité (>500GB)
    local external_drives=()
    while IFS= read -r line; do
        local device=$(echo "$line" | awk '{print $1}')
        local size=$(echo "$line" | awk '{print $4}')
        local mountpoint=$(echo "$line" | awk '{print $6}')
        
        # Convertir la taille en Go pour comparaison
        local size_gb=0
        if [[ $size == *"T" ]]; then
            size_gb=$(echo "$size" | sed 's/T//' | awk '{print int($1 * 1000)}')
        elif [[ $size == *"G" ]]; then
            size_gb=$(echo "$size" | sed 's/G//' | awk '{print int($1)}')
        fi
        
        # Si le disque fait plus de 500GB et n'est pas le disque système
        if [[ $size_gb -gt 500 ]] && [[ "$mountpoint" != "/" ]] && [[ "$mountpoint" != "/boot" ]]; then
            external_drives+=("$mountpoint")
            log "Disque externe détecté: $device ($size) monté sur $mountpoint"
        fi
    done < <(df -h | grep "^/dev/")
    
    # Retourner le premier disque externe trouvé (le plus gros généralement)
    if [[ ${#external_drives[@]} -gt 0 ]]; then
        echo "${external_drives[0]}"
    else
        echo ""
    fi
}

# Fonction pour créer les dossiers nécessaires
create_storage_directories() {
    local storage_path="$1"
    
    log "Création des dossiers de stockage dans $storage_path"
    
    mkdir -p "$storage_path/uploads"
    mkdir -p "$storage_path/videos"
    
    # Définir les permissions appropriées
    chmod 755 "$storage_path/uploads"
    chmod 755 "$storage_path/videos"
    
    # Changer le propriétaire pour l'utilisateur PHP (si nécessaire)
    chown -R 1000:1000 "$storage_path/uploads"
    chown -R 1000:1000 "$storage_path/videos"
}

# Fonction pour mettre à jour le fichier .env
update_env_file() {
    local new_path="$1"
    
    log "Mise à jour du chemin de stockage vers: $new_path"
    
    # Sauvegarder l'ancien fichier .env
    cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Mettre à jour la variable EXTERNAL_STORAGE_PATH
    if grep -q "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE"; then
        sed -i "s|^EXTERNAL_STORAGE_PATH=.*|EXTERNAL_STORAGE_PATH=$new_path|" "$ENV_FILE"
    else
        echo "EXTERNAL_STORAGE_PATH=$new_path" >> "$ENV_FILE"
    fi
}

# Fonction pour redémarrer les services Docker
restart_docker_services() {
    log "Redémarrage des services Docker..."
    
    cd /home/betweenus
    
    # Arrêter les services
    docker compose down
    
    # Attendre un peu
    sleep 5
    
    # Redémarrer les services
    docker compose up -d
    
    log "Services Docker redémarrés"
}

# Fonction principale
main() {
    log "=== Démarrage du gestionnaire de stockage ==="
    
    # Détecter le stockage externe
    local detected_storage=$(detect_external_storage)
    
    if [[ -z "$detected_storage" ]]; then
        log "ATTENTION: Aucun disque externe détecté, utilisation du stockage par défaut"
        detected_storage="/mnt/betweenus-storage"
    fi
    
    # Vérifier le chemin actuel dans .env
    local current_path=""
    if [[ -f "$ENV_FILE" ]] && grep -q "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE"; then
        current_path=$(grep "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE" | cut -d'=' -f2)
    fi
    
    # Si le chemin a changé ou n'existe pas
    if [[ "$current_path" != "$detected_storage" ]] || [[ ! -d "$detected_storage" ]]; then
        log "Changement de stockage détecté: $current_path -> $detected_storage"
        
        # Créer les dossiers nécessaires
        create_storage_directories "$detected_storage"
        
        # Mettre à jour le fichier .env
        update_env_file "$detected_storage"
        
        # Redémarrer les services Docker
        restart_docker_services
        
        log "Migration du stockage terminée avec succès"
    else
        log "Aucun changement de stockage détecté"
    fi
    
    log "=== Fin du gestionnaire de stockage ==="
}

# Exécuter le script principal
main "$@"
