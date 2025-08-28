#!/bin/bash

# Script de synchronisation automatique du stockage
# Ce script vérifie et corrige automatiquement la configuration du stockage

LOG_FILE="/var/log/storage-sync.log"
ENV_FILE="/home/video-app/.env"

# Fonction de logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Fonction pour détecter le vrai emplacement du disque externe
detect_actual_external_storage() {
    log "Détection du disque externe..."
    
    # Chercher les disques de plus de 500GB avec des dossiers uploads/videos
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
        
        # Si c'est un gros disque externe
        if [[ $size_gb -gt 500 ]] && [[ "$mountpoint" != "/" ]] && [[ "$mountpoint" != "/boot" ]] && [[ "$mountpoint" != "/boot/firmware" ]]; then
            # Vérifier si les dossiers uploads et videos existent
            if [[ -d "$mountpoint/uploads" ]] && [[ -d "$mountpoint/videos" ]]; then
                log "Disque externe avec stockage trouvé: $mountpoint"
                echo "$mountpoint"
                return 0
            fi
        fi
    done < <(df -h | grep "^/dev/")
    
    echo ""
}

# Fonction pour lire la configuration actuelle
get_current_storage_path() {
    if [[ -f "$ENV_FILE" ]] && grep -q "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE"; then
        grep "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE" | cut -d'=' -f2
    else
        echo ""
    fi
}

# Fonction pour mettre à jour la configuration
update_storage_config() {
    local new_path="$1"
    
    log "Mise à jour de la configuration vers: $new_path"
    
    # Créer une sauvegarde
    if [[ -f "$ENV_FILE" ]]; then
        cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
    fi
    
    # Mettre à jour ou ajouter la variable
    if [[ -f "$ENV_FILE" ]] && grep -q "^EXTERNAL_STORAGE_PATH=" "$ENV_FILE"; then
        sed -i "s|^EXTERNAL_STORAGE_PATH=.*|EXTERNAL_STORAGE_PATH=$new_path|" "$ENV_FILE"
    else
        echo "EXTERNAL_STORAGE_PATH=$new_path" >> "$ENV_FILE"
    fi
}

# Fonction pour redémarrer Docker si nécessaire
restart_docker_if_needed() {
    local should_restart="$1"
    
    if [[ "$should_restart" == "true" ]]; then
        log "Redémarrage des conteneurs Docker..."
        cd /home/video-app
        docker compose down
        sleep 3
        docker compose up -d
        log "Conteneurs redémarrés"
    fi
}

# Fonction pour vérifier l'accès aux fichiers dans Docker
test_docker_access() {
    local test_path="$1"
    
    log "Test d'accès Docker au chemin: $test_path"
    
    # Attendre que les conteneurs soient prêts
    sleep 5
    
    # Tester l'accès depuis le conteneur PHP
    if docker exec video-app-php-1 test -d "/home/php/uploads" 2>/dev/null; then
        log "✅ Accès Docker fonctionnel"
        return 0
    else
        log "❌ Accès Docker non fonctionnel"
        return 1
    fi
}

# Fonction principale
main() {
    log "=== SYNCHRONISATION DU STOCKAGE ==="
    
    # Détecter où est réellement le disque externe
    local actual_storage=$(detect_actual_external_storage)
    local current_config=$(get_current_storage_path)
    
    log "Stockage détecté: $actual_storage"
    log "Configuration actuelle: $current_config"
    
    if [[ -z "$actual_storage" ]]; then
        log "⚠️ Aucun disque externe trouvé"
        exit 1
    fi
    
    local needs_restart=false
    
    # Comparer avec la configuration actuelle
    if [[ "$actual_storage" != "$current_config" ]]; then
        log "🔧 Correction nécessaire: $current_config -> $actual_storage"
        update_storage_config "$actual_storage"
        needs_restart=true
    else
        log "✅ Configuration déjà correcte"
    fi
    
    # Redémarrer Docker si nécessaire
    if [[ "$needs_restart" == "true" ]]; then
        restart_docker_if_needed true
        
        # Tester l'accès après redémarrage
        if test_docker_access "$actual_storage"; then
            log "✅ Synchronisation réussie"
        else
            log "❌ Problème après synchronisation"
            exit 1
        fi
    else
        # Tester l'accès actuel
        if ! test_docker_access "$current_config"; then
            log "🔧 Problème d'accès détecté, redémarrage forcé"
            restart_docker_if_needed true
            
            if test_docker_access "$actual_storage"; then
                log "✅ Problème résolu après redémarrage"
            else
                log "❌ Problème persiste"
                exit 1
            fi
        fi
    fi
    
    log "=== FIN DE LA SYNCHRONISATION ==="
}

# Exécuter le script principal
main "$@"
