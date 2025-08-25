#!/bin/bash

# Script d'installation et configuration du disque dur externe
# Ce script aide à configurer un nouveau disque dur externe

set -e

LOG_FILE="/var/log/disk-setup.log"

# Fonction de logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Fonction pour lister les disques disponibles
list_available_disks() {
    log "=== Disques disponibles ==="
    lsblk -o NAME,SIZE,TYPE,MOUNTPOINT
    echo ""
    log "=== Informations détaillées ==="
    sudo fdisk -l | grep "Disk /dev/"
}

# Fonction pour formater un disque
format_disk() {
    local device="$1"
    local label="$2"
    
    log "ATTENTION: Formatage du disque $device avec le label '$label'"
    log "Toutes les données sur ce disque seront perdues!"
    
    read -p "Êtes-vous sûr de vouloir continuer? (oui/non): " confirm
    if [[ "$confirm" != "oui" ]]; then
        log "Opération annulée"
        exit 1
    fi
    
    log "Formatage en cours..."
    
    # Démontage du disque s'il est monté
    sudo umount "${device}"* 2>/dev/null || true
    
    # Création d'une nouvelle table de partition
    sudo parted "$device" --script mklabel gpt
    
    # Création d'une partition qui prend tout l'espace
    sudo parted "$device" --script mkpart primary ext4 0% 100%
    
    # Attendre que la partition soit créée
    sleep 2
    
    # Formatage en ext4
    sudo mkfs.ext4 -L "$label" "${device}1"
    
    log "Formatage terminé"
}

# Fonction pour monter le disque
mount_disk() {
    local device="$1"
    local mount_point="$2"
    local label="$3"
    
    log "Montage du disque ${device}1 sur $mount_point"
    
    # Créer le point de montage
    sudo mkdir -p "$mount_point"
    
    # Monter le disque
    sudo mount "${device}1" "$mount_point"
    
    # Obtenir l'UUID du disque
    local uuid=$(sudo blkid -s UUID -o value "${device}1")
    
    # Ajouter au fstab pour le montage automatique
    local fstab_entry="UUID=$uuid $mount_point ext4 defaults,nofail 0 2"
    
    # Vérifier si l'entrée existe déjà
    if ! grep -q "$mount_point" /etc/fstab; then
        echo "$fstab_entry" | sudo tee -a /etc/fstab
        log "Entrée ajoutée au fstab pour le montage automatique"
    else
        log "Entrée fstab déjà existante"
    fi
    
    # Définir les permissions
    sudo chown -R toto:toto "$mount_point"
    sudo chmod 755 "$mount_point"
    
    log "Disque monté avec succès sur $mount_point"
}

# Fonction pour configurer le stockage BetweenUs
setup_betweenus_storage() {
    local mount_point="$1"
    
    log "Configuration du stockage BetweenUs sur $mount_point"
    
    # Créer les dossiers nécessaires
    mkdir -p "$mount_point/uploads"
    mkdir -p "$mount_point/videos"
    mkdir -p "$mount_point/backups"
    
    # Définir les permissions
    sudo chown -R 1000:1000 "$mount_point/uploads"
    sudo chown -R 1000:1000 "$mount_point/videos"
    sudo chown -R toto:toto "$mount_point/backups"
    
    chmod 755 "$mount_point/uploads"
    chmod 755 "$mount_point/videos"
    chmod 755 "$mount_point/backups"
    
    log "Structure de stockage créée"
}

# Menu principal
show_menu() {
    echo "=== Configuration du disque dur externe pour BetweenUs ==="
    echo "1. Lister les disques disponibles"
    echo "2. Formater et configurer un nouveau disque"
    echo "3. Monter un disque existant"
    echo "4. Configurer le stockage BetweenUs sur un disque monté"
    echo "5. Tester le gestionnaire de stockage automatique"
    echo "6. Quitter"
    echo ""
}

# Fonction principale
main() {
    log "=== Démarrage du script de configuration de disque ==="
    
    while true; do
        show_menu
        read -p "Choisissez une option (1-6): " choice
        
        case $choice in
            1)
                list_available_disks
                ;;
            2)
                list_available_disks
                echo ""
                read -p "Entrez le chemin du disque à formater (ex: /dev/sda): " device
                read -p "Entrez un label pour le disque (ex: betweenus-storage): " label
                read -p "Entrez le point de montage (ex: /mnt/betweenus-storage): " mount_point
                
                format_disk "$device" "$label"
                mount_disk "$device" "$mount_point" "$label"
                setup_betweenus_storage "$mount_point"
                
                echo ""
                log "Configuration terminée! Le disque est prêt à être utilisé."
                ;;
            3)
                list_available_disks
                echo ""
                read -p "Entrez le chemin du disque à monter (ex: /dev/sda): " device
                read -p "Entrez le point de montage (ex: /mnt/betweenus-storage): " mount_point
                
                mount_disk "$device" "$mount_point" "betweenus-storage"
                setup_betweenus_storage "$mount_point"
                ;;
            4)
                read -p "Entrez le point de montage du disque (ex: /mnt/betweenus-storage): " mount_point
                setup_betweenus_storage "$mount_point"
                ;;
            5)
                log "Test du gestionnaire de stockage automatique..."
                /home/betweenus/scripts/storage-manager.sh
                ;;
            6)
                log "Fin du script"
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

# Vérifier les permissions root pour certaines opérations
if [[ $EUID -ne 0 ]] && [[ "$1" != "--help" ]]; then
    echo "Ce script nécessite les privilèges sudo pour certaines opérations."
    echo "Exécutez avec: sudo $0"
    exit 1
fi

# Exécuter le menu principal
main "$@"
