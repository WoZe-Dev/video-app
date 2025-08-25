#!/bin/bash
echo "=== TEST DE CONNEXION TV ==="

# Test 1: Connexion avec l'utilisateur viewer
echo "1. Connexion avec l'utilisateur 'viewer'..."
COOKIES=$(mktemp)

# Se connecter
curl -c "$COOKIES" -X POST http://localhost:8000/login \
  -d "login=viewer&password=viewer123&submit=" \
  -v 2>&1 | grep -E "(Set-Cookie|Location|HTTP/)"

echo -e "\n2. Test de l'accès à /tv-mode après connexion..."
# Accéder à /tv-mode avec les cookies de session
curl -b "$COOKIES" -v http://localhost:8000/tv-mode 2>&1 | grep -E "(Location|HTTP/)" | head -5

echo -e "\n3. Suivi des redirections multiples..."
# Suivre toutes les redirections
curl -b "$COOKIES" -L -v http://localhost:8000/tv-mode 2>&1 | grep -E "(Location|HTTP/)" | head -10

# Nettoyage
rm -f "$COOKIES"
echo -e "\n=== FIN DU TEST ==="
