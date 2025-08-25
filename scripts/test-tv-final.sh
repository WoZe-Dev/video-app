#!/bin/bash

echo "=== TEST FINAL DE L'INTERFACE TV ==="
echo ""

# Créer un fichier temporaire pour stocker les cookies
COOKIE_JAR=$(mktemp)

echo "🔐 Étape 1: Test de connexion avec l'utilisateur viewer"
echo "====================================================="

# Obtenir la page de login
echo "Récupération de la page de login..."
curl -s -c "$COOKIE_JAR" http://localhost:8000/login > /dev/null

# Tentative de connexion
echo "Tentative de connexion avec viewer/viewer123..."
LOGIN_RESULT=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -d "login=viewer&password=viewer123&submit=Se+connecter" \
    -X POST \
    -L \
    -w "FINAL_URL:%{url_effective}|HTTP_CODE:%{http_code}" \
    http://localhost:8000/login)

echo "Résultat de la connexion: $LOGIN_RESULT"

echo ""
echo "📺 Étape 2: Test d'accès à l'interface TV"
echo "=========================================="

# Test d'accès direct à l'interface TV
TV_RESULT=$(curl -s -b "$COOKIE_JAR" \
    -w "HTTP_CODE:%{http_code}|SIZE:%{size_download}" \
    -L \
    http://localhost:8000/tv-mode)

echo "Résultat accès TV: $(echo "$TV_RESULT" | tail -1)"

# Vérifier si la page contient les éléments de l'interface TV
if echo "$TV_RESULT" | grep -q "tv-interface"; then
    echo "✅ Interface TV chargée avec succès!"
    echo "✅ Éléments CSS tv-interface détectés"
else
    echo "❌ Interface TV non détectée dans la réponse"
fi

if echo "$TV_RESULT" | grep -q "modern-streaming.css"; then
    echo "✅ CSS moderne détecté"
else
    echo "⚠️  CSS moderne non détecté"
fi

echo ""
echo "🧪 Étape 3: Test avec l'utilisateur admin"
echo "=========================================="

# Test avec admin
curl -s -c "${COOKIE_JAR}_admin" http://localhost:8000/login > /dev/null

ADMIN_LOGIN=$(curl -s -b "${COOKIE_JAR}_admin" -c "${COOKIE_JAR}_admin" \
    -d "login=admin&password=admin123&submit=Se+connecter" \
    -X POST \
    -w "HTTP_CODE:%{http_code}" \
    -o /dev/null \
    http://localhost:8000/login)

echo "Connexion admin: $ADMIN_LOGIN"

ADMIN_TV_ACCESS=$(curl -s -b "${COOKIE_JAR}_admin" \
    -w "HTTP_CODE:%{http_code}" \
    -o /dev/null \
    http://localhost:8000/tv-mode)

echo "Accès TV admin: $ADMIN_TV_ACCESS"

echo ""
echo "📊 Étape 4: Résumé des tests"
echo "============================="

# Vérifier les codes de retour
if [[ "$LOGIN_RESULT" == *"HTTP_CODE:200"* ]] || [[ "$LOGIN_RESULT" == *"HTTP_CODE:302"* ]]; then
    echo "✅ Connexion viewer: OK"
else
    echo "❌ Connexion viewer: ÉCHEC"
fi

if [[ "$TV_RESULT" == *"HTTP_CODE:200"* ]]; then
    echo "✅ Accès interface TV: OK"
    
    # Compter les éléments de l'interface
    TV_ELEMENTS=$(echo "$TV_RESULT" | grep -c "tv-nav-item\|tv-interface\|data-section")
    echo "   Éléments d'interface détectés: $TV_ELEMENTS"
    
    if [ "$TV_ELEMENTS" -gt 5 ]; then
        echo "✅ Interface TV complète"
    else
        echo "⚠️  Interface TV incomplète"
    fi
else
    echo "❌ Accès interface TV: ÉCHEC"
fi

echo ""
echo "🎯 CONCLUSION"
echo "============="

if [[ "$TV_RESULT" == *"HTTP_CODE:200"* ]] && echo "$TV_RESULT" | grep -q "tv-interface"; then
    echo "🎉 L'INTERFACE TV FONCTIONNE PARFAITEMENT!"
    echo ""
    echo "Pour accéder à l'interface TV:"
    echo "1. Allez sur: http://192.168.1.68:8000/login"
    echo "2. Connectez-vous avec:"
    echo "   Username: viewer"
    echo "   Password: viewer123"
    echo "3. Vous serez automatiquement redirigé vers l'interface TV"
    echo ""
    echo "Alternative pour les admins:"
    echo "   Username: admin"
    echo "   Password: admin123"
    echo "   Puis allez manuellement sur: http://192.168.1.68:8000/tv-mode"
else
    echo "❌ PROBLÈME DÉTECTÉ AVEC L'INTERFACE TV"
    echo ""
    echo "Commandes de débogage:"
    echo "  docker compose logs php --tail=20"
    echo "  docker compose exec php php test_tv_auth.php"
fi

# Nettoyage
rm -f "$COOKIE_JAR" "${COOKIE_JAR}_admin"

echo ""
