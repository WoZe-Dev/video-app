#!/bin/bash
echo "=== TEST DE CONNEXION ADMIN ==="

# Test avec admin depuis .env
echo "1. Connexion avec admin (depuis .env)..."
COOKIES=$(mktemp)

# Se connecter avec admin 
curl -c "$COOKIES" -X POST http://localhost:8000/login \
  -d "login=admin&password=admin123&submit=" \
  -v 2>&1 | grep -E "(Set-Cookie|Location|HTTP/)"

echo -e "\n2. Test de l'accès après connexion admin..."
curl -b "$COOKIES" -v http://localhost:8000/gallery 2>&1 | grep -E "(Location|HTTP/)" | head -3

echo -e "\n3. Test accès direct à /tv-mode avec admin..."
curl -b "$COOKIES" -v http://localhost:8000/tv-mode 2>&1 | grep -E "(Location|HTTP/)" | head -3

# Nettoyage
rm -f "$COOKIES"
echo -e "\n=== FIN DU TEST ADMIN ==="
