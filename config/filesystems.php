<?php

declare(strict_types=1);

return [
    'private_documents' => BASE_PATH . '/storage/app/birth-documents',
    'temporary' => BASE_PATH . '/storage/app/temporary',
    'max_upload_bytes' => 10 * 1024 * 1024,
    'allowed_mime_types' => [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ],
];
