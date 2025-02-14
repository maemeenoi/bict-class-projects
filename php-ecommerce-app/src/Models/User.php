<?php

namespace Agora\Models;

use Agora\Core\AbstractModel;
use Agora\Core\Exceptions\InvalidDataException;

class User extends AbstractModel
{
    public function authenticate($email, $password)
    {
        // Get user by email
        $sql = "SELECT * FROM User WHERE email = ? AND is_active = 1 LIMIT 1";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Update last login time
        $sql = "UPDATE User SET updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$user['user_id']]);

        return $user;
    }

    public function create($data)
    {
        // Validate required fields
        $error = self::errorInRequiredField('User Name', $data['user_name'], 50);
        self::assertNoError($error);

        $error = self::errorInRequiredField('Email', $data['email'], 100);
        self::assertNoError($error);

        $error = self::errorInRequiredField('Password', $data['password'], 255);
        self::assertNoError($error);

        // Check if email already exists
        $sql = "SELECT COUNT(*) as count FROM User WHERE email = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$data['email']]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            throw new InvalidDataException('Email already exists');
        }

        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO User (business_id, user_name, email, address, phone, password_hash, role, bio, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([
            $data['business_id'] ?? null,
            $data['user_name'],
            $data['email'],
            $data['address'] ?? null,   
            $data['phone'] ?? null,      
            $passwordHash,
            $data['role'],
            $data['bio'] ?? null
        ]);

        return $this->getDB()->lastInsertId();
    }

    public function updateProfile($data)
    {
        // Validate required fields
        $error = self::errorInRequiredField('User Name', $data['user_name'], 50);
        self::assertNoError($error);

        $error = self::errorInRequiredField('Email', $data['email'], 100);
        self::assertNoError($error);

        // Check if email is already taken by another user
        $sql = "SELECT COUNT(*) as count FROM User WHERE email = ? AND user_id != ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$data['email'], $data['user_id']]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            throw new InvalidDataException('Email is already taken by another user');
        }

        // Update user profile
        $sql = "UPDATE User SET 
            user_name = ?,
            email = ?,
            phone = ?,
            address = ?,
            bio = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE user_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([
            $data['user_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['bio'] ?? null,
            $data['user_id']
        ]);
    }

    public function updatePassword($userId, $currentPassword, $newPassword)
    {
        // Get current user data
        $sql = "SELECT password_hash FROM User WHERE user_id = ?";
        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!password_verify($currentPassword, $user['password_hash'])) {
            throw new InvalidDataException('Current password is incorrect');
        }

        // Hash and update new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE User SET 
            password_hash = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE user_id = ?";

        $stmt = $this->getDB()->prepare($sql);
        $stmt->execute([$newPasswordHash, $userId]);
    }
}
