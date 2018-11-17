<?php

use Illuminate\Database\Seeder;
use App\Models\UserAddress;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();
        $faker = app(Faker\Generator::class);

        $user_addresses = factory(UserAddress::class)->times(5)->make()
            ->each(function ($address) use ($user_ids, $faker) {
                $address->user_id = $faker->randomElement($user_ids);
            });

        UserAddress::insert($user_addresses->toArray());
    }
}
