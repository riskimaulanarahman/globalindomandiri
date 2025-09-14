<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->log($model, 'CREATE', $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $this->log($model, 'UPDATE', ['changes' => $changes, 'original' => $model->getOriginal()]);

        if (array_key_exists('status', $changes)) {
            $this->log($model, 'STATUS_CHANGE', ['from' => $model->getOriginal('status'), 'to' => $model->status]);
        }
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'DELETE', $model->getOriginal());
    }

    protected function log(Model $model, string $action, $payload): void
    {
        AuditLog::create([
            'entity' => class_basename($model),
            'entity_id' => (int) ($model->getKey() ?? 0),
            'action' => $action,
            'payload_json' => json_encode($payload),
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
    }
}

