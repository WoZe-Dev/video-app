<?php

namespace App\Models;

use App\Core\Model;

class GalleryModel extends Model
{

    /**
     *  Create a new instance of the GalleryModel
     */

    public function __construct()
    {
        parent::__construct('galleries');
    }


    /**
     * Get all the galleries of a user with the user id
     * @param int $userid
     * @return array
     * @deprecated message="This method is deprecated, use getUserGalleriesAndContent instead"
     */

    public function getGalleries(int $userid)
    {
        $sql = "SELECT * FROM $this->table WHERE created_by = :user_id  ORDER BY created_at DESC";
        $statement = $this->prepare($sql);

        $params = [
            'user_id' => $userid
        ];

        $this->execute($statement, $params);
        return $this->fetchAll($statement);

    }


    /**
     * Get a gallery by the id and the user id and its photos
     * @param int $id
     * @param int $userid
     * @return array
     */
    public function getGallery(int $galleryid, int $userid)
    {
        // Récupérer d'abord les informations de la galerie
        $sql = 'SELECT
            g.id AS gallery_id,
            g.name AS gallery_name,
            g.created_by,
            g.created_at AS gallery_created_at
            FROM galleries g
            WHERE g.id = :id';

        $statement = $this->prepare($sql);
        $params = ['id' => $galleryid];
        $this->execute($statement, $params);
        $gallery = $this->fetch($statement);

        if (!$gallery) {
            return null;
        }

        // Récupérer toutes les vidéos de cette galerie séparément
        $videosSql = 'SELECT
            v.id,
            v.user_id,
            v.video_path,
            v.caption,
            v.is_public,
            v.duration,
            v.file_size,
            v.mime_type,
            v.created_at
            FROM videos v
            WHERE v.gallery_id = :gallery_id
            ORDER BY v.created_at DESC';

        $videosStatement = $this->prepare($videosSql);
        $this->execute($videosStatement, ['gallery_id' => $galleryid]);
        $videos = $this->fetchAll($videosStatement);

        // Ajouter les vidéos à l'objet galerie
        $gallery->galleryVideos = json_encode($videos ?: []);

        return $gallery;
    }


    /**
     * Get all the galleries of a user with the user id and the photos in the gallery
     * @param int $userid
     * @return array
     */


    public function getUserGalleriesAndContent(int $userid): array
    {
        // Récupérer toutes les galeries
        $sql = "SELECT
            g.id AS gallery_id,
            g.name AS gallery_name,
            g.created_by,
            g.created_at AS gallery_created_at
        FROM galleries g
        ORDER BY g.created_at DESC";
        
        $statement = $this->prepare($sql);
        $this->execute($statement, []);
        $galleries = $this->fetchAll($statement);
        
        // Pour chaque galerie, récupérer toutes ses vidéos séparément
        foreach ($galleries as &$gallery) {
            $videosSql = 'SELECT
                v.id,
                v.user_id,
                v.video_path,
                v.caption,
                v.is_public,
                v.duration,
                v.file_size,
                v.mime_type,
                v.created_at
                FROM videos v
                WHERE v.gallery_id = :gallery_id
                ORDER BY v.created_at DESC';
                
            $videosStatement = $this->prepare($videosSql);
            $this->execute($videosStatement, ['gallery_id' => $gallery->gallery_id]);
            $videos = $this->fetchAll($videosStatement);
            
            // Encoder en JSON comme l'ancienne méthode
            $gallery->galleryVideos = json_encode($videos ?: []);
        }
        
        return $galleries;
    }

    /**
     * Create a new gallery and insert it into the database with the user id.
     * @param array $data
     * @return int
     */

    public function createGallery(array $data): bool|int
    {
        // Insert data into the database where the user_id is the current user and the name is the name of the gallery
        // and also after creating the gallery we should insert the gallery_id, user_id and default permissions thats is the permission to can_upload and can view to the gallery_users table
        $sql = "INSERT INTO $this->table (name, created_by) VALUES (:name, :created_by)";
        $statement = $this->prepare($sql);
        $this->execute($statement, $data);

        $galleryId = $this->pdo->lastInsertId();

        $sql = "INSERT INTO gallery_users (gallery_id, user_id, can_upload, can_view, is_owner) VALUES (:gallery_id, :user_id, 1, 1, 1)";
        $statement = $this->prepare($sql);
        $this->execute($statement, [
            'gallery_id' => $galleryId,
            'user_id' => $data['created_by']
        ]);

        return $galleryId;
    }


    /**
     * Delete a gallery by the id and the user id
     * @param int $id
     * @param int $userid
     * @return bool|int
     */

    public function deleteGalleryVideo(int $videoId, int $userId): bool|int
    {
        $sql = "DELETE FROM videos WHERE id = :video_id AND user_id = :user_id";
        $statement = $this->prepare($sql);
        $params = [
            'video_id' => $videoId,
            'user_id' => $userId
        ];
        $this->execute($statement, $params);
        // Check if the video was deleted
        return $statement->rowCount() > 0;
    }


    /**
     * Get a video by the video id.
     * @param int $videoId
     * @return mixed
     */
    public function getVideo(int $videoId): mixed
    {
        $sql = "SELECT * FROM videos WHERE id = :video_id";
        $statement = $this->prepare($sql);
        $params = [
            'video_id' => $videoId
        ];
        $this->execute($statement, $params);
        return $this->fetch($statement);
    }

    /**
     * Add a video info in the database with the gallery id, user id, video path, caption and is public.
     * @param array $data
     * @return bool|string
     */
    public function createVideo(array $data): int
    {
        $sql = "INSERT INTO videos (gallery_id, user_id, video_path, caption, is_public, duration, file_size, mime_type) VALUES (:gallery_id, :user_id, :video_path, :caption, :is_public, :duration, :file_size, :mime_type)";
        $statement = $this->prepare($sql);
        $params = [
            'gallery_id' => $data['gallery_id'],
            'user_id' => $data['user_id'],
            'video_path' => $data['video_path'],
            'caption' => $data['caption'],
            'is_public' => $data['is_public'],
            'duration' => $data['duration'] ?? null,
            'file_size' => $data['file_size'] ?? null,
            'mime_type' => $data['mime_type'] ?? null
        ];
        $this->execute($statement, $params);
        return $this->pdo->lastInsertId();
    }


    /**
     * Get the gallery users by the gallery id
     * @param int $galleryId
     */
    public function getGalleryUsers(int $galleryId)
    {
        $sql = " SELECT *
        FROM gallery_users gu
                LEFT JOIN users u ON u.id = gu.user_id
                WHERE gallery_id = :gallery_id
        ";
        $statement = $this->prepare($sql);
        $this->execute($statement, ['gallery_id' => $galleryId]);
        return $this->fetchAll($statement);
    }

    /**
     * Get the user by email/username not in the gallery by the gallery id and user id
     * @param int $galleryId
     */
    public function getUsersNotInGallery(int $galleryId, string $email)
    {
        $sql = "SELECT * FROM users u WHERE (email = :email OR username = :email) AND u.id NOT IN (SELECT user_id FROM gallery_users WHERE gallery_id = :gallery_id)";
        $statement = $this->prepare($sql);
        $params = [
            "gallery_id" => $galleryId,
            "email" => $email
        ];

        $this->execute($statement, $params);
        return $this->fetch($statement);
    }
    /**
     * Get the connected user role by the user id and the gallery id
     * @param int $userId
     * @param int $galleryId
     */
    public function getConnectedUserRole(int $userId, int $galleryId)
    {
        $sql = "SELECT * FROM gallery_users WHERE user_id = :user_id AND gallery_id = :gallery_id";
        $statement = $this->prepare($sql);
        $this->execute($statement, ['user_id' => $userId, 'gallery_id' => $galleryId]);
        return $this->fetch($statement);
    }

    /**
     * Add a user in the gallery by the user id and the gallery id
     * @param int $userId
     * @param mixed $galleryId
     * @return bool|string
     */
    public function addUsersinGalleryById(int $userId, $galleryId): bool|string
    {
        $sql = "
            INSERT INTO gallery_users (gallery_id, user_id, can_upload, can_view, is_owner) VALUES (:gallery_id, :user_id, 1, 1, 0)
        ";
        $statement = $this->prepare($sql);
        $this->execute($statement, ['gallery_id' => $galleryId, 'user_id' => $userId]);
        return $this->pdo->lastInsertId();
    }


    /**
     * Remove a user from the gallery by the user id and the gallery id
     * @param int $userId
     * @param int $galleryId
     * @return bool|string
     */
    public function removeUserFromGalleryById(int $userId, int $galleryId): bool|string
    {
        $sql = "
            DELETE FROM gallery_users WHERE gallery_id = :gallery_id AND user_id = :user_id
        ";
        $statement = $this->prepare($sql);
        $this->execute($statement, [
            "user_id" => $userId,
            "gallery_id" => $galleryId
        ]);
        return $statement->rowCount() > 0;
    }



    /**
     * Remove all the videos from the gallery by the gallery id.
     * @param int $galleryId
     * @return bool|string
     */

    public function emptyGallery(int $galleryId): bool
    {
        $sql = 'DELETE FROM videos WHERE gallery_id = :gallery_id ';
        $statement = $this->prepare($sql);

        $params = [
            'gallery_id' => $galleryId
        ];

        $this->execute($statement, $params);
        return $statement->rowCount() > 0;
    }

    /**
     * Delete a gallery by the gallery id.
     * @param int $galleryId
     * @return bool|string
     */
    public function deleteGalleryById(int $galleryId): bool
    {
        $sql = 'DELETE FROM galleries WHERE id = :gallery_id';
        $statement = $this->prepare($sql);
        $this->execute($statement, [':gallery_id' => $galleryId]);
        return $statement->rowCount() > 0;
    }

    /**
     * Check if the user is the owner of the gallery.
     * @param int $userId
     * @return bool
     */

    public function checkOwner(int $userId): bool
    {
        $sql = 'SELECT * FROM gallery_users WHERE user_id = :user_id AND is_owner = 1';
        $statement = $this->prepare($sql);

        $this->execute($statement, ['user_id' => $userId]);
        $result = $this->fetch($statement);
        return $result !== false;
    }

    /**
     * Get a video by its ID
     * @param int $videoId
     * @return object|null
     */
    public function getVideoById(int $videoId)
    {
        $sql = "SELECT * FROM videos WHERE id = :video_id";
        $statement = $this->prepare($sql);
        
        $params = ['video_id' => $videoId];
        $this->execute($statement, $params);
        
        return $this->fetch($statement);
    }
}
