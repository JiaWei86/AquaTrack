<?php

namespace App\Observers;

use App\Models\WaterSource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WaterSourceObserver
{
    public function created(WaterSource $waterSource): void
    {
        $this->log('created', $waterSource, [
            'attributes' => $waterSource->getAttributes(),
        ]);
    }

    public function updated(WaterSource $waterSource): void
    {
        // getChanges()/getOriginal() give a real before/after diff instead of
        // just "something changed" — needed to prove non-repudiation.
        $changes = $waterSource->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $before = collect($waterSource->getOriginal())
            ->only(array_keys($changes))
            ->all();

        $this->log('updated', $waterSource, [
            'before' => $before,
            'after'  => $changes,
        ]);
    }

    public function deleted(WaterSource $waterSource): void
    {
        $this->log('deleted', $waterSource, [
            'attributes' => $waterSource->getAttributes(),
        ]);
    }

    private function log(string $action, WaterSource $waterSource, array $context): void
    {
        $actor = Auth::user();

        Log::channel('admin_actions')->info("water_source.{$action}", array_merge([
            'action'          => $action,
            'water_source_id' => $waterSource->getKey(),
            'actor_id'        => $actor?->id,
            'actor_name'      => $actor?->name,
            'actor_role'      => $actor?->role,
            'ip_address'      => request()?->ip(),
        ], $context));
    }
}