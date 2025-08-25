# BetweenUs - Système de Gestion Automatique

## 🚀 Configuration Automatique

Le système BetweenUs a été configuré pour :
- ✅ **Démarrage automatique** au redémarrage du Raspberry Pi
- ✅ **Gestion automatique du stockage** sur disque dur externe
- ✅ **Redémarrage automatique** des services Docker
- ✅ **Montage automatique** du disque dur externe

## 📁 Structure des Données

### Stockage Actuel
- **Disque** : `/dev/sda1` (916GB)
- **Point de montage** : `/mnt/sauvegarde`
- **Uploads** : `/mnt/sauvegarde/uploads` (5.1GB utilisés)
- **Vidéos** : `/mnt/sauvegarde/videos`
- **Sauvegardes** : `/mnt/sauvegarde/backups`

### Configuration Flexible
Pour changer de disque dur, modifiez simplement la variable dans le fichier `.env` :
```bash
EXTERNAL_STORAGE_PATH=/mnt/nouveau-disque
```

## 🔧 Scripts de Gestion

### 1. Gestionnaire Principal
```bash
sudo /home/betweenus/scripts/manage-betweenus.sh
```

**Options disponibles :**
- Afficher l'état du système
- Redémarrer les services
- Nettoyer le système Docker
- Sauvegarder la base de données
- Restaurer la base de données
- Changer le disque de stockage
- Afficher les logs
- Tester le gestionnaire automatique

### 2. Gestionnaire de Stockage Automatique
```bash
sudo /home/betweenus/scripts/storage-manager.sh
```
- Détecte automatiquement les disques externes
- Configure les dossiers nécessaires
- Met à jour la configuration
- Redémarre les services si nécessaire

### 3. Configuration de Nouveau Disque
```bash
sudo /home/betweenus/scripts/setup-disk.sh
```
- Assistant interactif pour configurer un nouveau disque
- Formatage et partitionnement
- Configuration automatique du montage
- Création de la structure de dossiers

## 🐳 Services Docker

### Services Actifs
- **Nginx** : Serveur web (port 8000)
- **PHP-FPM** : Traitement PHP
- **MariaDB** : Base de données
- **phpMyAdmin** : Interface d'administration (port 8080)

### Commandes Docker
```bash
# Voir l'état des services
docker compose ps

# Redémarrer tous les services
docker compose restart

# Voir les logs
docker compose logs

# Arrêter tous les services
docker compose down

# Démarrer tous les services
docker compose up -d
```

## 🔄 Service Systemd

### Service de Démarrage Automatique
Le service `betweenus.service` démarre automatiquement au boot.

```bash
# Voir l'état du service
sudo systemctl status betweenus.service

# Redémarrer le service
sudo systemctl restart betweenus.service

# Arrêter le service
sudo systemctl stop betweenus.service

# Désactiver le démarrage automatique
sudo systemctl disable betweenus.service

# Réactiver le démarrage automatique
sudo systemctl enable betweenus.service
```

## 🌐 Accès aux Services

- **Application principale** : http://192.168.1.68:8000
- **phpMyAdmin** : http://192.168.1.68:8080

### Identifiants de Base de Données
- **Utilisateur** : `voxio`
- **Mot de passe** : `.Optile17`
- **Base de données** : `database`

## 💾 Sauvegarde et Restauration

### Sauvegarde Automatique
```bash
sudo /home/betweenus/scripts/manage-betweenus.sh backup
```
- Crée une sauvegarde complète de la base de données
- Stocke dans `/mnt/sauvegarde/backups/`
- Garde automatiquement les 7 dernières sauvegardes

### Restauration
```bash
sudo /home/betweenus/scripts/manage-betweenus.sh
# Puis choisir l'option 5 "Restaurer la base de données"
```

## 🔍 Surveillance et Maintenance

### Vérification Rapide
```bash
# État complet du système
sudo /home/betweenus/scripts/manage-betweenus.sh status

# Espace disque
df -h /mnt/sauvegarde

# Logs récents
sudo journalctl -u betweenus.service -f
```

### Nettoyage Système
```bash
# Nettoyer Docker (conteneurs, images, volumes inutilisés)
sudo /home/betweenus/scripts/manage-betweenus.sh cleanup
```

## 🚨 Dépannage

### Problèmes Courants

#### 1. Services ne démarrent pas
```bash
# Vérifier les logs
docker compose logs

# Redémarrer manuellement
sudo systemctl restart betweenus.service
```

#### 2. Disque plein
```bash
# Vérifier l'espace
df -h /mnt/sauvegarde

# Nettoyer les anciennes données
sudo /home/betweenus/scripts/manage-betweenus.sh cleanup
```

#### 3. Disque non monté
```bash
# Vérifier le montage
mount | grep sauvegarde

# Remonter manuellement
sudo mount -a
```

#### 4. Changer de disque dur
1. Connecter le nouveau disque
2. Exécuter : `sudo /home/betweenus/scripts/setup-disk.sh`
3. Suivre l'assistant de configuration
4. Ou modifier manuellement `.env` et redémarrer

## 📋 Maintenance Préventive

### Tâches Hebdomadaires
- Vérifier l'espace disque
- Créer une sauvegarde de la base de données
- Nettoyer les conteneurs Docker inutilisés

### Tâches Mensuelles
- Vérifier les logs d'erreur
- Mettre à jour les images Docker si nécessaire
- Vérifier l'intégrité du disque de stockage

## 📝 Logs Importants

- **Service systemd** : `/var/log/syslog`
- **Gestion BetweenUs** : `/var/log/betweenus-management.log`
- **Stockage automatique** : `/var/log/storage-manager.log`
- **Docker** : `docker compose logs`

---

## 🎯 Résumé de la Configuration

✅ **Système entièrement automatisé**
✅ **Démarrage automatique au boot**
✅ **Gestion flexible du stockage**
✅ **Sauvegarde automatique**
✅ **Scripts de maintenance intégrés**
✅ **Surveillance en temps réel**

Le système est maintenant prêt pour un fonctionnement autonome !
