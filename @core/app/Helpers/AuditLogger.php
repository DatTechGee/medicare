<?php

namespace App\Helpers;

use App\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Accountability trail: every admin action (verify, release, refund, review…)
 * is recorded with who / when / what / from which IP.
 */
class AuditLogger
{
    public static function record(
        string $action,
        string $model,
        $modelId = null,
        array $newValues = [],
        array $oldValues = []
    ): ?AuditLog {
        try {
            $userType = 'system';
            $userId = null;

            if (Auth::guard('admin')->check()) {
                $userType = 'admin';
                $userId = Auth::guard('admin')->id();
            } elseif (Auth::check()) {
                $userType = 'user';
                $userId = Auth::id();
            }

            return AuditLog::create([
                'user_id' => $userId,
                'user_type' => $userType,
                'action' => $action,
                'model' => $model,
                'model_id' => $modelId,
                'new_values' => $newValues ?: null,
                'old_values' => $oldValues ?: null,
                'ip_address' => Request::ip(),
                'user_agent' => substr((string) Request::userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Audit log failed: ' . $e->getMessage());

            return null;
        }
    }

    /** Recent entries with actor names resolved for display */
    public static function recent(int $limit = 100)
    {
        return AuditLog::orderByDesc('id')->limit($limit)->get();
    }

    public function actorName(AuditLog $log): string
    {
        if ($log->user_type === 'admin') {
            return optional(\App\Admin::find($log->user_id))->username ?? "admin#{$log->user_id}";
        }
        if ($log->user_type === 'user') {
            return optional(\App\User::find($log->user_id))->name ?? "user#{$log->user_id}";
        }

        return 'system';
    }
}
