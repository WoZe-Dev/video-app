<?php
/**
 * Script de test pour la gestion hiérarchique des galeries vidéo
 */

require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Models/GalleryModel.php';

use App\Models\GalleryModel;

echo "=== Test de la gestion hiérarchique des galeries ===\n\n";

$galleryModel = new GalleryModel();

try {
    // Test 1: Créer une galerie principale
    echo "1. Test création galerie principale...\n";
    $mainGalleryId = $galleryModel->createGallery([
        'name' => 'Galerie Test Principal',
        'parent_id' => null,
        'created_by' => 1
    ]);
    echo "✓ Galerie principale créée avec ID: {$mainGalleryId}\n\n";

    // Test 2: Créer une sous-galerie
    echo "2. Test création sous-galerie...\n";
    $subGalleryId = $galleryModel->createGallery([
        'name' => 'Sous-Galerie Test',
        'parent_id' => $mainGalleryId,
        'created_by' => 1
    ]);
    echo "✓ Sous-galerie créée avec ID: {$subGalleryId}\n\n";

    // Test 3: Vérifier l'unicité des noms
    echo "3. Test unicité des noms...\n";
    $isUnique = $galleryModel->isGalleryNameUnique('Galerie Test Principal', null);
    echo $isUnique ? "✗ Erreur: nom déjà utilisé détecté comme unique\n" : "✓ Unicité des noms fonctionne\n";

    $isUniqueAtLevel = $galleryModel->isGalleryNameUnique('Nouvelle Galerie', $mainGalleryId);
    echo $isUniqueAtLevel ? "✓ Nom unique à ce niveau détecté correctement\n\n" : "✗ Erreur dans la détection d'unicité\n\n";

    // Test 4: Récupérer le fil d'Ariane
    echo "4. Test fil d'Ariane...\n";
    $breadcrumb = $galleryModel->getGalleryBreadcrumb($subGalleryId);
    echo "Fil d'Ariane pour sous-galerie:\n";
    foreach ($breadcrumb as $item) {
        echo "  - {$item['name']} (ID: {$item['id']})\n";
    }
    echo "\n";

    // Test 5: Récupérer les galeries principales
    echo "5. Test récupération galeries principales...\n";
    $mainGalleries = $galleryModel->getMainGalleries();
    echo "Nombre de galeries principales: " . count($mainGalleries) . "\n";
    foreach ($mainGalleries as $gallery) {
        echo "  - {$gallery->gallery_name} (sous-galeries: {$gallery->subgalleries_count}, vidéos: {$gallery->videos_count})\n";
    }
    echo "\n";

    // Test 6: Récupérer le contenu d'une galerie
    echo "6. Test récupération contenu galerie...\n";
    $content = $galleryModel->getGalleryContent($mainGalleryId);
    echo "Sous-galeries dans galerie principale: " . count($content['subgalleries']) . "\n";
    echo "Vidéos dans galerie principale: " . count($content['videos']) . "\n";
    
    foreach ($content['subgalleries'] as $subgallery) {
        echo "  - Sous-galerie: {$subgallery->gallery_name}\n";
    }
    echo "\n";

    // Test 7: Vérifier les enfants
    echo "7. Test détection enfants...\n";
    $hasChildren = $galleryModel->hasChildren($mainGalleryId);
    echo $hasChildren ? "✓ Galerie principale a des enfants\n" : "✗ Erreur: enfants non détectés\n";

    $hasChildrenSub = $galleryModel->hasChildren($subGalleryId);
    echo $hasChildrenSub ? "✗ Erreur: sous-galerie vide détectée avec enfants\n" : "✓ Sous-galerie vide correctement détectée\n\n";

    // Nettoyage
    echo "8. Nettoyage...\n";
    if (!$galleryModel->hasChildren($subGalleryId)) {
        $galleryModel->deleteGalleryById($subGalleryId);
        echo "✓ Sous-galerie supprimée\n";
    }
    
    if (!$galleryModel->hasChildren($mainGalleryId)) {
        $galleryModel->deleteGalleryById($mainGalleryId);
        echo "✓ Galerie principale supprimée\n";
    }

    echo "\n=== Tests terminés avec succès! ===\n";

} catch (Exception $e) {
    echo "Erreur lors des tests: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
