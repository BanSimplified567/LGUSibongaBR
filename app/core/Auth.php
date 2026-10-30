<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    /**
     * @return array{id: int, name: string, email: string}|null
     */
    public static function user(): ?array
    {
        $id = Session::get('user_id');
        if (!is_int($id) || $id < 1) {
            return null;
        }

        $user = User::findActiveById($id);
        if ($user === null) {
            Session::invalidate();
            return null;
        }

        return $user;
    }

    public static function checkPermission(int $userId, string $permission): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT 1
             FROM user_roles ur
             INNER JOIN role_permissions rp ON rp.role_id = ur.role_id
             INNER JOIN permissions p ON p.id = rp.permission_id
             INNER JOIN roles r ON r.id = ur.role_id
             WHERE ur.user_id = :user_id
               AND p.name = :permission
               AND r.is_active = 1
             LIMIT 1'
        );
        $statement->execute([
            'user_id' => $userId,
            'permission' => $permission,
        ]);

        return $statement->fetchColumn() !== false;
    }
}
