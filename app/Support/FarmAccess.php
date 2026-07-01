<?php

namespace App\Support;

use App\Models\Farm;
use App\Models\FeedReceipt;
use App\Models\Flock;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class FarmAccess
{
    public static function farmsQuery(User $user): Builder
    {
        return Farm::query()
            ->when(! $user->isSuperAdmin(), fn (Builder $query) => $query->where('id', $user->farm_id));
    }

    public static function flocksQuery(User $user): Builder
    {
        return Flock::query()
            ->when(! $user->isSuperAdmin(), fn (Builder $query) => $query->where('farm_id', $user->farm_id));
    }

    public static function activeFlockFor(User $user): ?Flock
    {
        return self::flocksQuery($user)
            ->where('status', 'active')
            ->latest('start_date')
            ->latest('id')
            ->first();
    }

    public static function ensureFarm(User $user, Farm|int $farm): void
    {
        $farmId = $farm instanceof Farm ? $farm->id : $farm;

        abort_unless($user->canAccessFarm((int) $farmId), 403);
    }

    public static function ensureFlock(User $user, Flock $flock): void
    {
        self::ensureFarm($user, (int) $flock->farm_id);
    }

    public static function ensureFeedReceipt(User $user, FeedReceipt $feedReceipt): void
    {
        self::ensureFarm($user, (int) $feedReceipt->farm_id);
    }

    public static function ensureSuperAdmin(User $user): void
    {
        abort_unless($user->isSuperAdmin(), 403);
    }
}
