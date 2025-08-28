#!/bin/bash

# Script de gestion rapide du stockage BetweenUs
# Usage: ./storage-fix.sh [command]

case "$1" in
    "check")
        echo "🔍 Vérification de l'état du stockage..."
        sudo /home/video-app/scripts/diagnose-storage.sh
        ;;
    "fix")
        echo "🔧 Correction automatique du stockage..."
        sudo /home/video-app/scripts/storage-sync.sh
        ;;
    "restart")
        echo "🔄 Redémarrage des services Docker..."
        cd /home/video-app
        docker compose down
        sleep 3
        docker compose up -d
        echo "✅ Services redémarrés"
        ;;
    "status")
        echo "📊 État des services:"
        echo ""
        echo "=== Services systemd ==="
        systemctl is-active betweenus
        systemctl is-active betweenus-storage-sync.timer
        echo ""
        echo "=== Conteneurs Docker ==="
        docker ps --format "table {{.Names}}\t{{.Status}}"
        echo ""
        echo "=== Configuration actuelle ==="
        if [[ -f "/home/video-app/.env" ]]; then
            grep "EXTERNAL_STORAGE_PATH" /home/video-app/.env || echo "EXTERNAL_STORAGE_PATH non défini"
        else
            echo "Fichier .env introuvable"
        fi
        echo ""
        echo "=== Montages disque ==="
        df -h | grep -E "^/dev/" | grep -v "boot"
        ;;
    "test")
        echo "🧪 Test d'accès aux vidéos..."
        echo "Test 1: Accès aux fichiers sur l'hôte"
        if find /mnt/*/uploads -name "*.mp4" 2>/dev/null | head -3; then
            echo "✅ Fichiers trouvés sur l'hôte"
        else
            echo "❌ Aucun fichier trouvé sur l'hôte"
        fi
        
        echo ""
        echo "Test 2: Accès depuis Docker"
        if docker exec video-app-php-1 find /home/php/uploads -name "*.mp4" 2>/dev/null | head -3; then
            echo "✅ Fichiers accessibles depuis Docker"
        else
            echo "❌ Fichiers inaccessibles depuis Docker"
        fi
        
        echo ""
        echo "Test 3: Accès HTTP"
        test_video=$(docker exec video-app-php-1 find /home/php/uploads -name "*.mp4" 2>/dev/null | head -1 | sed 's|/home/php||')
        if [[ -n "$test_video" ]]; then
            if curl -s -I "http://localhost:8000$test_video" | grep -q "200 OK"; then
                echo "✅ Accès HTTP fonctionnel"
            else
                echo "❌ Accès HTTP non fonctionnel"
            fi
        else
            echo "❌ Aucune vidéo trouvée pour le test HTTP"
        fi
        ;;
    *)
        echo "🛠️  Script de gestion du stockage BetweenUs"
        echo ""
        echo "Usage: $0 [command]"
        echo ""
        echo "Commandes disponibles:"
        echo "  check    - Diagnostic complet de l'état du stockage"
        echo "  fix      - Correction automatique des problèmes"
        echo "  restart  - Redémarrage des services Docker"
        echo "  status   - Affichage de l'état des services"
        echo "  test     - Test d'accès aux vidéos"
        echo ""
        echo "Exemples:"
        echo "  $0 check   # Diagnostic"
        echo "  $0 fix     # Correction automatique"
        echo "  $0 test    # Test d'accès"
        ;;
esac
