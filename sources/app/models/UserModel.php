<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model {
    public string $table = 'users';

    public function __construct() {
        parent::__construct('users');
    }

    /**
     * Get user by username
     * @param string $username
     * @return object|false
     */
    public function getUserByUsername(string $username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username AND is_verified = 1 LIMIT 1";
        $statement = $this->prepare($sql);
        $this->execute($statement, ['username' => $username]);
        return $this->fetch($statement);
    }

    /**
     * Get user by email
     * @param string $email
     * @return object|false
     */
    public function getUserByEmail(string $email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND is_verified = 1 LIMIT 1";
        $statement = $this->prepare($sql);
        $this->execute($statement, ['email' => $email]);
        return $this->fetch($statement);
    }

    /**
     * Get user by ID
     * @param int $id
     * @return object|false
     */
    public function getUserById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $statement = $this->prepare($sql);
        $this->execute($statement, ['id' => $id]);
        return $this->fetch($statement);
    }
}
