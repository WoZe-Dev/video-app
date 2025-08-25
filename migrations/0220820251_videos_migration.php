<?php
use App\Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $sql = "
        -- Renommer la table photos en videos
        RENAME TABLE photos TO videos;
        
        -- Modifier le champ image_path en video_path
        ALTER TABLE videos CHANGE COLUMN image_path video_path VARCHAR(255) NOT NULL;
        
        -- Ajouter un champ pour la durée de la vidéo (optionnel)
        ALTER TABLE videos ADD COLUMN duration INT DEFAULT NULL COMMENT 'Durée en secondes';
        
        -- Ajouter un champ pour la taille du fichier
        ALTER TABLE videos ADD COLUMN file_size BIGINT DEFAULT NULL COMMENT 'Taille en octets';
        
        -- Ajouter un champ pour le type MIME
        ALTER TABLE videos ADD COLUMN mime_type VARCHAR(100) DEFAULT NULL;
        ";

        $this->pdo->exec($sql);
    }

    public function down(): void
    {
        $sql = "
        -- Supprimer les nouveaux champs
        ALTER TABLE videos DROP COLUMN duration;
        ALTER TABLE videos DROP COLUMN file_size;
        ALTER TABLE videos DROP COLUMN mime_type;
        
        -- Renommer le champ video_path en image_path
        ALTER TABLE videos CHANGE COLUMN video_path image_path VARCHAR(255) NOT NULL;
        
        -- Renommer la table videos en photos
        RENAME TABLE videos TO photos;
        ";

        $this->pdo->exec($sql);
    }
};
