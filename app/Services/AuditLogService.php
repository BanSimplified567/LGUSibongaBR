<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AuditLogService
{
    public static function record(?int $userId, string $action, string $result): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO audit_logs
                (user_id, action, target_type, target_id, result, request_id, created_at)
             VALUES
                (:user_id, :action, NULL, NULL, :result, :request_id, CURRENT_TIMESTAMP)'
        );
        $requestId = $_SERVER['APP_REQUEST_ID'] ?? null;
        $statement->execute([
            'user_id' => $userId,
            'action' => $action,
            'result' => $result,
            'request_id' => is_string($requestId) ? $requestId : null,
        ]);
    }
}
