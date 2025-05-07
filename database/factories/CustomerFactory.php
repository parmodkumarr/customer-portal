<?php
namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $dob = $this->faker->dateTimeBetween('-80 years', '-18 years');
        $age = now()->diffInYears($dob);

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'age' => $age,
            'dob' => $dob->format('Y-m-d'),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
