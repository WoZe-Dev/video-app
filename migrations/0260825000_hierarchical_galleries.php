<?php
use App\Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $sql = "
        -- Ajouter le champ parent_id à la table galleries pour la hiérarchie
        ALTER TABLE galleries ADD COLUMN parent_id INT NULL AFTER name;
        
        -- Ajouter une contrainte de clé étrangère pour parent_id
        ALTER TABLE galleries ADD CONSTRAINT fk_galleries_parent 
            FOREIGN KEY (parent_id) REFERENCES galleries(id) ON DELETE CASCADE;
            
        -- Ajouter un index pour améliorer les performances des requêtes hiérarchiques
        ALTER TABLE galleries ADD INDEX idx_parent_id (parent_id);
        
        -- Ajouter un champ path pour stocker le chemin complet (pour optimisation)
        ALTER TABLE videos ADD COLUMN full_path VARCHAR(500) NULL AFTER video_path;
        
        -- Ajouter un champ filename original pour conserver le nom d'origine
        ALTER TABLE videos ADD COLUMN original_filename VARCHAR(255) NULL AFTER full_path;
        ";

        $this->pdo->exec($sql);
    }

    public function down(): void
    {
        $sql = "
        -- Supprimer les nouveaux champs et contraintes
        ALTER TABLE videos DROP COLUMN original_filename;
        ALTER TABLE videos DROP COLUMN full_path;
        ALTER TABLE galleries DROP INDEX idx_parent_id;
        ALTER TABLE galleries DROP FOREIGN KEY fk_galleries_parent;
        ALTER TABLE galleries DROP COLUMN parent_id;
        ";

        $this->pdo->exec($sql);
    }
};
