<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class User
{
    /**
     * @return array{id: int, name: string, email: string, password_hash: string, is_active: bool}|null
     */
    public static function findForAuthentication(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, email, password_hash, is_active
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if (!is_array($user)) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
            'password_hash' => (string) $user['password_hash'],
            'is_active' => (bool) $user['is_active'],
        ];
    }

    /**
     * @return array{id: int, name: string, email: string}|null
     */
    public static function findActiveById(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, email
             FROM users
             WHERE id = :id AND is_active = 1
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        if (!is_array($user)) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
        ];
    }
}
