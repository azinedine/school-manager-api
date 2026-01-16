<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Strictly update only passed data
            $user->fill($data);

            // If dirty, save
            if ($user->isDirty()) {
                $user->save();
            }

            return $user->refresh()->load('institution');
        });
    }
}
