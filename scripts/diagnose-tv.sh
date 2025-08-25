#!/bin/bash

echo "=== DIAGNOSTIC COMPLET DE L'INTERFACE TV ==="
echo ""

echo "📊 ÉTAPE 1: Vérification des services Docker"
echo "============================================="
docker compose ps
echo ""

echo "📂 ÉTAPE 2: Vérification des fichiers de l'interface TV"
echo "======================================================="
echo "✓ Vérification du contrôleur TV:"
if [ -f "/home/betweenus/sources/app/Controllers/TVController.php" ]; then
    echo "  ✅ TVController.php existe"
else
    echo "  ❌ TVController.php manquant"
fi

echo "✓ Vérification de la vue TV:"
if [ -f "/home/betweenus/sources/app/views/tv/index.php" ]; then
    echo "  ✅ Vue TV existe"
else
    echo "  ❌ Vue TV manquante"
fi

echo "✓ Vérification des assets CSS:"
if [ -f "/home/betweenus/sources/assets/css/modern-streaming.css" ]; then
    echo "  ✅ modern-streaming.css existe"
else
    echo "  ❌ modern-streaming.css manquant"
fi

if [ -f "/home/betweenus/sources/assets/css/tv-interface.css" ]; then
    echo "  ✅ tv-interface.css existe"
else
    echo "  ❌ tv-interface.css manquant"
fi

echo "✓ Vérification du JavaScript:"
if [ -f "/home/betweenus/sources/assets/js/tv-interface.js" ]; then
    echo "  ✅ tv-interface.js existe"
else
    echo "  ❌ tv-interface.js manquant"
fi

echo ""
echo "👥 ÉTAPE 3: Vérification des utilisateurs"
echo "=========================================="
docker compose exec php php -r "
require_once '/home/php/app/Core/Model.php';
\$pdo = new PDO('mysql:host=mariadb;dbname=database', 'voxio', '.Optile17');
\$stmt = \$pdo->query('SELECT username, role FROM users');
while (\$user = \$stmt->fetch()) {
    echo '  User: ' . \$user['username'] . ' - Role: ' . \$user['role'] . PHP_EOL;
}
"

echo ""
echo "🔗 ÉTAPE 4: Test des routes"
echo "============================"
echo "✓ Test route de connexion (/login):"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/login)
echo "  Code: $HTTP_CODE"

echo "✓ Test route TV sans auth (/tv-mode):"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/tv-mode)
echo "  Code: $HTTP_CODE (devrait être 302 pour redirection)"

echo ""
echo "📜 ÉTAPE 5: Logs récents PHP"
echo "============================="
echo "Dernières erreurs PHP:"
docker compose logs php --tail=10 | grep -i error || echo "  ✅ Aucune erreur récente"

echo ""
echo "🧪 ÉTAPE 6: Test de connexion automatique"
echo "=========================================="

# Test de connexion automatique avec l'utilisateur viewer
echo "Test de connexion viewer..."
COOKIE_JAR=$(mktemp)

# Première requête pour obtenir la page de login
curl -s -c "$COOKIE_JAR" http://localhost:8000/login > /dev/null

# Tentative de connexion
LOGIN_RESPONSE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -d "login=viewer&password=viewer123&submit=Se+connecter" \
    -X POST \
    -w "%{http_code}" \
    http://localhost:8000/login)

echo "  Code de réponse connexion: $LOGIN_RESPONSE"

# Test d'accès à la page TV avec les cookies
TV_RESPONSE=$(curl -s -b "$COOKIE_JAR" \
    -w "%{http_code}" \
    -o /dev/null \
    http://localhost:8000/tv-mode)

echo "  Code de réponse page TV: $TV_RESPONSE"

# Nettoyage
rm -f "$COOKIE_JAR"

echo ""
echo "📋 ÉTAPE 7: Résumé et recommandations"
echo "====================================="

if [ "$TV_RESPONSE" = "200" ]; then
    echo "✅ L'interface TV fonctionne correctement!"
    echo ""
    echo "ACCÈS VIEWER:"
    echo "  URL: http://192.168.1.68:8000/login"
    echo "  Username: viewer"
    echo "  Password: viewer123"
    echo ""
    echo "Après connexion, vous devriez être automatiquement redirigé vers l'interface TV."
elif [ "$LOGIN_RESPONSE" != "302" ] && [ "$LOGIN_RESPONSE" != "200" ]; then
    echo "❌ Problème de connexion détecté"
    echo "Vérifiez les logs avec: docker compose logs php --tail=20"
else
    echo "⚠️  Connexion OK mais accès TV problématique"
    echo "Vérifiez la session et les permissions"
fi

echo ""
echo "COMMANDES DE DÉBOGAGE UTILES:"
echo "  docker compose logs php --tail=20      # Logs PHP"
echo "  docker compose logs nginx --tail=20    # Logs Nginx"
echo "  docker compose exec php ls -la /home/php/app/views/tv/  # Vérifier les fichiers"
echo "
