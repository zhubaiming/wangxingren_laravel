<?php

namespace App\Observers;

use App\Models\UserRole;
use Illuminate\Support\Facades\Log;

class UserRoleObserver
{
    /**
     * Handle the UserRole "created" event.
     */
    public function created(UserRole $userRole): void
    {
        //
        Log::channel('test')->info('执行了 ' . self::class . ' 类的 ' . __FUNCTION__ . ' 方法');
    }

    /**
     * Handle the UserRole "updated" event.
     */
    public function updated(UserRole $userRole): void
    {
        //
        Log::channel('test')->info('执行了 ' . self::class . ' 类的 ' . __FUNCTION__ . ' 方法');
    }

    /**
     * Handle the UserRole "deleted" event.
     */
    public function deleted(UserRole $userRole): void
    {
        //
        Log::channel('test')->info('执行了 ' . self::class . ' 类的 ' . __FUNCTION__ . ' 方法');
    }

    /**
     * Handle the UserRole "restored" event.
     */
    public function restored(UserRole $userRole): void
    {
        //
        Log::channel('test')->info('执行了 ' . self::class . ' 类的 ' . __FUNCTION__ . ' 方法');
    }

    /**
     * Handle the UserRole "force deleted" event.
     */
    public function forceDeleted(UserRole $userRole): void
    {
        //
        Log::channel('test')->info('执行了 ' . self::class . ' 类的 ' . __FUNCTION__ . ' 方法');
    }
}
