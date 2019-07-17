<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(UsersSeeder::class);
        Model::reguard();
    }
}
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for ($i=0; $i < 10; $i++) {
            \DB::table('User')->insert(array(
                'name' => $faker->firstName,
                'lastname' => $faker->lastName,
                'email' => $faker->email,
                'email_verified_at' => date('Y-m-d H:m:s'),
                'role'  => $faker->randomElement(['Administrador', 'Solicitante', 'Consultor']),
                'password' => $faker->password,
                'created_at' => date('Y-m-d H:m:s'),
                'updated_at' => date('Y-m-d H:m:s')
            ));
        }
    }
}
