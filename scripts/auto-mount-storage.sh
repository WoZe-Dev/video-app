#!/bin/bash

# Script de montage automatique pour les disques externes
# Ce script assure que le disque externe est monté avant le démarrage de Docker

LOG_FILE="/var/log/auto-mount-storage.log"
ENV_FILE="/home/video-app/.env"

# Fonction de logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Fonction pour détecter les partitions non montées
detect_unmounted_storage() {
    log "Recherche des disques non montés..."
    
    # Lister toutes les partitions disponibles
    local unmounted_drives=()
    while IFS= read -r line; do
        local device=$(echo "$line" | awk '{print $1}')
        local size=$(echo "$line" | awk '{print $4}')
        
        # Vérifier si la partition n'est pas montée et fait plus de 100GB
        if [[ -n "$device" ]] && [[ "$device" =~ ^/dev/[a-z]+[0-9]+$ ]]; then
            local size_gb=0
            if [[ $size == *"T" ]]; then
                size_gb=$(echo "$size" | sed 's/T//' | awk '{print int($1 * 1000)}')
            elif [[ $size == *"G" ]]; then
                size_gb=$(echo "$size" | sed 's/G//' | awk '{print int($1)}')
            fi
            
            # Si le disque fait plus de 100GB et n'est pas monté
            if [[ $size_gb -gt 100 ]]; then
                # Vérifier s'il n'est pas déjà monté
                if ! mount | grep -q "$device"; then
                    unmounted_drives+=("$device")
                    log "Partition non montée détectée: $device ($size)"
                fi
            fi
        fi
    done < <(lsblk -rno NAME,SIZE | grep -E '^[a-z]+[0-9]+')
    
    # Retourner la première partition trouvée
    if [[ ${#unmounted_drives[@]} -gt 0 ]]; then
        echo "${unmounted_drives[0]}"
    else
        echo ""
    fi
}

# Fonction pour détecter où est monté le disque externe
detect_external_disk_mount() {
    log "Recherche du disque externe déjà monté..."
    
    # Chercher les disques de plus de 500GB qui ne sont pas le système
    while IFS= read -r line; do
        local device=$(echo "$line" | awk '{print $1}')
        local size=$(echo "$line" | awk '{print $2}')
        local mountpoint=$(echo "$line" | awk '{print $6}')
        
        # Convertir la taille en Go
        local size_gb=0
        if [[ $size == *"T" ]]; then
            size_gb=$(echo "$size" | sed 's/T//' | awk '{print int($1 * 1000)}')
        elif [[ $size == *"G" ]]; then
            size_gb=$(echo "$size" | sed 's/G//' | awk '{print int($1)}')
        fi
        
        # Si c'est un gros disque externe (pas le système)
        if [[ $size_gb -gt 500 ]] && [[ "$mountpoint" != "/" ]] && [[ "$mountpoint" != "/boot" ]] && [[ "$mountpoint" != "/boot/firmware" ]]; then
            log "Disque externe trouvé: $device ($size) monté sur $mountpoint"
            echo "$mountpoint"
            return 0
        fi
    done < <(df -h | grep "^/dev/")
    
    echo ""
}

# Fonction pour monter une partition
mount_storage_device() {
    local device="$1"
    local mount_point="/mnt/betweenus-storage"
    
    log "Tentative de montage de $device vers $mount_point"
    
    # Créer le point de montage s'il n'existe pas
    sudo mkdir -p "$mount_point"
    
    # Tenter le montage
    if sudo mount "$device" "$mount_point" 2>/dev/null; then
        log "Montage réussi de $device vers $mount_point"
        
        # Créer les dossiers nécessaires
        sudo mkdir -p "$mount_point/uploads"
        sudo mkdir -p "$mount_point/videos"
        
        # Définir les permissions appropriées
        sudo chmod 755 "$mount_point/uploads"
        sudo chmod 755 "$mount_point/videos"
        sudo chown -R 1000:1000 "$mount_point/uploads"
        sudo chown -R 1000:1000 "$mount_point/videos"
        
        # Mettre à jour le fichier .env
        update_env_file "$mount_point"
        
        return 0
    else
        log "ERREUR: Impossible de monter $device"
        return 1
    fi
}

# Fonction pour mettre à jour le fichier .env
update_env_file() {
    local new_path="$1"
    
    log "Mise à jour du chemin de stockage vers: $new_path"
    
    # Créer une sauvegarde
    if [[ -f "$ENV_FILE" ]]; then
        cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
    fi
    
    # Mettre à jour ou ajouter la variable EXTERNAL_STORAGE_PATH
    if [[ -f "$ENV_FILE" ]] && grep -q "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE"; then
        sed -i "s|^EXTERNAL_STORAGE_PATH=.*|EXTERNAL_STORAGE_PATH=$new_path|" "$ENV_FILE"
    else
        echo "EXTERNAL_STORAGE_PATH=$new_path" >> "$ENV_FILE"
    fi
}

# Fonction pour vérifier l'état du stockage actuel
check_current_storage() {
    local current_path=""
    
    # Lire le chemin actuel depuis .env
    if [[ -f "$ENV_FILE" ]] && grep -q "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE"; then
        current_path=$(grep "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE" | cut -d'=' -f2)
    fi
    
    # Vérifier si le chemin existe et est accessible
    if [[ -n "$current_path" ]] && [[ -d "$current_path" ]] && [[ -w "$current_path" ]]; then
        log "Stockage actuel fonctionnel: $current_path"
        return 0
    else
        log "Problème avec le stockage actuel: $current_path"
        return 1
    fi
}

# Fonction principale
main() {
    log "=== Démarrage du montage automatique ==="
    
    # Attendre que tous les périphériques soient détectés
    sleep 5
    
    # D'abord, chercher si le disque externe est déjà monté quelque part
    local existing_mount=$(detect_external_disk_mount)
    
    if [[ -n "$existing_mount" ]]; then
        log "Disque externe trouvé monté sur: $existing_mount"
        
        # Vérifier si les dossiers uploads et videos existent
        if [[ -d "$existing_mount/uploads" ]] && [[ -d "$existing_mount/videos" ]]; then
            log "Structure de dossiers trouvée dans $existing_mount"
            
            # Mettre à jour le fichier .env avec le bon chemin
            update_env_file "$existing_mount"
            log "Configuration mise à jour pour utiliser: $existing_mount"
            
            # Vérifier si le stockage actuel fonctionne maintenant
            if check_current_storage; then
                log "Le stockage est maintenant fonctionnel"
                exit 0
            fi
        else
            log "Structure de dossiers manquante, création des dossiers..."
            create_storage_directories "$existing_mount"
            update_env_file "$existing_mount"
            log "Stockage configuré sur: $existing_mount"
            exit 0
        fi
    fi
    
    # Si aucun disque monté trouvé, chercher des disques non montés
    local unmounted_device=$(detect_unmounted_storage)
    
    if [[ -n "$unmounted_device" ]]; then
        log "Tentative de montage du disque non monté: $unmounted_device"
        
        if mount_storage_device "$unmounted_device"; then
            log "Montage automatique réussi"
        else
            log "ERREUR: Échec du montage automatique"
            exit 1
        fi
    else
        log "Aucun disque externe trouvé"
        
        # Utiliser le stockage par défaut
        local default_storage="/mnt/betweenus-storage"
        sudo mkdir -p "$default_storage/uploads"
        sudo mkdir -p "$default_storage/videos"
        sudo chmod 755 "$default_storage/uploads" "$default_storage/videos"
        sudo chown -R 1000:1000 "$default_storage"
        
        update_env_file "$default_storage"
        log "Utilisation du stockage par défaut: $default_storage"
    fi
    
    log "=== Fin du montage automatique ==="
}

# Exécuter le script principal
main "$@"
