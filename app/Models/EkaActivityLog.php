<?php

namespace App\Models;

use Core\EkaModel;

class EkaActivityLog extends EkaModel
{
    protected string $table = 'activity_logs';

    public function log(int $tenantId, ?int $userId, string $action, string $details = ''): void
    {
        $this->create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'details' => $details
        ]);
    }
}
