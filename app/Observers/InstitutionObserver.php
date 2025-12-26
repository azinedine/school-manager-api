<?php

namespace App\Observers;

use App\Models\Institution;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstitutionObserver
{
    /**
     * Handle the Institution "created" event.
     */
    public function created(Institution $institution): void
    {
        Log::channel('audit')->info('Institution created', [
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Institution "updated" event.
     */
    public function updated(Institution $institution): void
    {
        $changes = $institution->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        Log::channel('audit')->info('Institution updated', [
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'changes' => $changes,
            'original' => array_intersect_key($institution->getOriginal(), $changes),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the Institution "deleted" event.
     */
    public function deleted(Institution $institution): void
    {
        Log::channel('audit')->warning('Institution deleted', [
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'soft_delete' => !$institution->isForceDeleting(),
        ]);
    }

    /**
     * Handle the Institution "restored" event.
     */
    public function restored(Institution $institution): void
    {
        Log::channel('audit')->info('Institution restored', [
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the Institution "force deleted" event.
     */
    public function forceDeleted(Institution $institution): void
    {
        Log::channel('audit')->critical('Institution permanently deleted', [
            'institution_id' => $institution->id,
            'institution_name' => $institution->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
        ]);
    }
}
