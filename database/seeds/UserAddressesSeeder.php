<?php

use Illuminate\Database\Seeder;
use App\Models\UserAddress;
use App\Models\User;

class UserAddressesSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            factory(UserAddress::class, random_int(1, 3))->create(['user_id' => $user->id]);
        });
    }
}
