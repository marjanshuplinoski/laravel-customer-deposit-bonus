<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = $this->faker->randomElement(['male','female']);

        if ($gender == 'male')
            $firstname = $this->faker->firstnameMale;
        else
            $firstname = $this->faker->firstNameFemale;

        return [
            'first_name' => $firstname,
            'last_name' => $firstname,
            'gender' => substr($gender,0,1),
            'country' => $this->faker->countryCode,
            'bonus' => rand(5,20),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
