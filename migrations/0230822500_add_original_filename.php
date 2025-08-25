<?php
use App\Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $sql = "
        -- Ajouter un champ pour le nom de fichier original
        ALTER TABLE videos ADD COLUMN original_filename VARCHAR(255) DEFAULT NULL COMMENT 'Nom du fichier original';
        ";

        $this->pdo->exec($sql);
    }

    public function down(): void
    {
        $sql = "
        -- Supprimer le champ original_filename
        ALTER TABLE videos DROP COLUMN original_filename;
        ";

        $this->pdo->exec($sql);
    }
};
