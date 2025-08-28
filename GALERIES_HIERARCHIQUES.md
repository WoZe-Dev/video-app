# Gestion de galeries vidéo hiérarchiques

## Vue d'ensemble

Ce système implémente une gestion de galeries vidéo avec une structure hiérarchique à deux niveaux :
- **Galeries principales** (niveau racine)
- **Galeries secondaires** (sous-galeries)
- **Vidéos** (dans n'importe quel niveau)

## Structure des données

### Modèle Gallery
```sql
galleries {
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    parent_id INT NULL REFERENCES galleries(id),
    created_by INT REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
}
```

### Modèle Video (mis à jour)
```sql
videos {
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT REFERENCES users(id),
    gallery_id INT REFERENCES galleries(id),
    video_path VARCHAR(255) NOT NULL,
    full_path VARCHAR(500) NULL,
    original_filename VARCHAR(255) NULL,
    caption TEXT,
    is_public BOOLEAN DEFAULT 0,
    duration INT DEFAULT NULL,
    file_size BIGINT DEFAULT NULL,
    mime_type VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
}
```

## Fonctionnalités implémentées

### Pour les administrateurs (ADMIN)

#### Navigation hiérarchique
- **Accueil** : Affiche uniquement les galeries principales
- **Galerie principale** : Affiche ses sous-galeries et vidéos directes
- **Galerie secondaire** : Affiche uniquement ses vidéos (style gestionnaire de fichiers)

#### Actions de gestion
- **Créer une galerie principale** (nom unique au niveau racine)
- **Créer une galerie secondaire** (nom unique dans la galerie parent)
- **Importer une vidéo** dans la galerie courante
- **Supprimer une galerie** (uniquement si vide)
- **Supprimer une vidéo**

#### Validations
- **Noms uniques** : Vérification au même niveau de hiérarchie
- **Caractères interdits** : / \ : * ? " < > |
- **Extensions autorisées** : .mp4, .mov, .avi
- **Gestion d'erreurs** : Messages en français

### Pour les utilisateurs TV (USER)
- **Mode lecture seule** : Même interface mais sans actions de modification
- **Navigation complète** : Accès à toute la structure hiérarchique
- **Lecture vidéo** : Toutes les fonctionnalités de lecture conservées

## Interface utilisateur

### Design conservé
- **Aucune modification** des couleurs, styles ou disposition
- **Réutilisation stricte** des composants existants
- **Icônes cohérentes** : Dossiers pour galeries, play pour vidéos

### Fil d'Ariane
```
Accueil / Galerie X / Sous-galerie Y
```

### Affichage des éléments
- **Galeries** : Icône dossier + nom + nombre d'éléments
- **Vidéos** : Titre + chemin complet + métadonnées + actions admin

## API TV (mode lecture)

### Endpoints mis à jour
- `GET /api/galleries` : Retourne la structure hiérarchique complète
- `GET /api/gallery/{id}/videos` : Vidéos d'une galerie spécifique
- `GET /api/video/{id}` : Détails d'une vidéo

### Structure JSON
```json
{
  "galleries": [
    {
      "gallery_id": 1,
      "gallery_name": "Galerie principale",
      "subgalleries_count": 2,
      "videos_count": 3,
      "total_videos": 8,
      "videos": [
        {
          "id": 1,
          "caption": "Ma vidéo",
          "full_path": "/Galerie principale/Sous-galerie/mavideo.mp4",
          "gallery_path": "Galerie principale/Sous-galerie"
        }
      ]
    }
  ]
}
```

## Routes ajoutées

```php
// Galeries hiérarchiques
$router->get('/gallery', ['GalleryController', 'index']);
$router->get('/gallery/create', ['GalleryController', 'createGallery']);
$router->post('/gallery/create', ['GalleryController', 'storeGallery']);
$router->get('/gallery/{id}', ['GalleryController', 'showGallery']);

// Upload et suppression
$router->get('/gallery/upload/{id}', ['GalleryController', 'uploadVideoForm']);
$router->post('/gallery/upload/{id}', ['GalleryController', 'storeVideo']);
$router->get('/gallery/delete-video/{id}', ['GalleryController', 'deleteVideo']);
$router->get('/gallery/delete/{galleryId}', ['GalleryController', 'deleteGallery']);
```

## Méthodes du modèle

### GalleryModel (nouvelles méthodes)
- `getMainGalleries()` : Galeries de niveau racine
- `getGalleryContent($id)` : Sous-galeries + vidéos d'une galerie
- `getGalleryBreadcrumb($id)` : Fil d'Ariane
- `isGalleryNameUnique($name, $parentId)` : Validation unicité
- `hasChildren($id)` : Vérification avant suppression

## Migration

La migration `0260825000_hierarchical_galleries.php` ajoute :
- Colonne `parent_id` dans `galleries`
- Contrainte de clé étrangère pour la hiérarchie
- Index pour les performances
- Colonnes `full_path` et `original_filename` dans `videos`

## Messages utilisateur

Tous les messages sont en français :
- Succès : "Galerie créée avec succès"
- Erreurs : "Une galerie avec ce nom existe déjà à ce niveau"
- Validations : "Extension non autorisée. Seuls les fichiers .mp4, .mov et .avi sont acceptés"

## Utilisation

1. **Connexion admin** : Accès complet aux fonctions de gestion
2. **Navigation** : Clic sur galeries pour descendre dans l'arborescence
3. **Création** : Boutons contextuels selon le niveau
4. **Upload** : Glisser-déposer ou sélection de fichiers
5. **Mode TV** : Interface identique en lecture seule

Le système est maintenant prêt pour une utilisation en production avec toutes les fonctionnalités demandées.
