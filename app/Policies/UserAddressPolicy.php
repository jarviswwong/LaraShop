<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressPolicy
{
    use HandlesAuthorization;

    public function own(User $user, UserAddress $userAddress)
    {
        return $user->id == $userAddress->user_id;
    }
}
