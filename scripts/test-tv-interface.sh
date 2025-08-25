#!/bin/bash

echo "=== TEST DE L'INTERFACE TV BETWEENUS ==="
echo ""

echo "1. Test de l'accès à la page TV sans authentification :"
echo "URL: http://localhost:8000/tv-mode"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/tv-mode)
echo "Code de réponse: $HTTP_CODE"

if [ "$HTTP_CODE" = "302" ]; then
    echo "✅ Redirection correcte (non authentifié)"
elif [ "$HTTP_CODE" = "200" ]; then
    echo "⚠️  Page accessible sans authentification"
else
    echo "❌ Erreur inattendue"
fi

echo ""
echo "2. Utilisateurs disponibles pour les tests :"
echo "   - Admin: admin / admin123"
echo "   - Viewer: viewer / viewer123"
echo "   - User: tv / (mot de passe à définir)"

echo ""
echo "3. URLs de test :"
echo "   - Page de connexion: http://192.168.1.68:8000/login"
echo "   - Interface TV: http://192.168.1.68:8000/tv-mode"
echo "   - Interface galeries: http://192.168.1.68:8000/gallery"

echo ""
echo "4. Test de la connectivité des services :"

# Test nginx
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000)
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
    echo "✅ Nginx: Opérationnel"
else
    echo "❌ Nginx: Problème (code $HTTP_CODE)"
fi

# Test phpMyAdmin
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080)
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ phpMyAdmin: Opérationnel"
else
    echo "❌ phpMyAdmin: Problème (code $HTTP_CODE)"
fi

echo ""
echo "5. État des conteneurs Docker :"
docker compose ps

echo ""
echo "=== INSTRUCTIONS POUR TESTER L'INTERFACE TV ==="
echo ""
echo "1. Ouvrez votre navigateur et allez sur: http://192.168.1.68:8000/login"
echo "2. Connectez-vous avec les identifiants viewer:"
echo "   Username: viewer"
echo "   Password: viewer123"
echo "3. Vous devriez être automatiquement redirigé vers l'interface TV"
echo "4. Si ce n'est pas le cas, allez manuellement sur: http://192.168.1.68:8000/tv-mode"
echo ""
echo "PROBLÈMES POTENTIELS :"
echo "- Si la page TV ne s'affiche pas, vérifiez les logs PHP avec:"
echo "  docker compose logs php --tail=20"
echo "- Si les CSS ne se chargent pas, vérifiez les permissions des fichiers assets"
echo ""
