<?php
// simonkapedb/app/models/User.php

class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        $row = $this->db->single();
        return $row;
    }

    public function login($username, $password) {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind('username', $username);
        $user = $this->db->single();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    // Fungsi baru untuk menambahkan user
    public function addUser($username, $role, $specific_user_id = null): bool {
        $password_hash = password_hash(DEFAULT_USER_PASSWORD, PASSWORD_BCRYPT);

        $this->db->query('INSERT INTO users (username, password, role, user_id) VALUES (:username, :password, :role, :user_id)');
        $this->db->bind(':username', $username);
        $this->db->bind(':password', $password_hash);
        $this->db->bind(':role', $role);
        $this->db->bind(':user_id', $specific_user_id);

        return $this->db->execute();
    }

    // Fungsi baru untuk memperbarui username user
    public function updateUsername($user_id_in_users_table, $new_username) {
        $this->db->query('UPDATE users SET username = :new_username WHERE id = :id');
        $this->db->bind(':new_username', $new_username);
        $this->db->bind(':id', $user_id_in_users_table);
        return $this->db->execute();
    }

    // Fungsi baru untuk mendapatkan user berdasarkan user_id dan role
    public function getUserByUserIdAndRole($specific_user_id, $role) {
        $this->db->query('SELECT * FROM users WHERE user_id = :user_id AND role = :role');
        $this->db->bind(':user_id', $specific_user_id);
        $this->db->bind(':role', $role);
        return $this->db->single();
    }

    // Fungsi baru untuk menghapus user berdasarkan user_id dan role
    public function deleteUserByUserIdAndRole($specific_user_id, $role) {
        $this->db->query('DELETE FROM users WHERE user_id = :user_id AND role = :role');
        $this->db->bind(':user_id', $specific_user_id);
        $this->db->bind(':role', $role);
        return $this->db->execute();
    }
}
