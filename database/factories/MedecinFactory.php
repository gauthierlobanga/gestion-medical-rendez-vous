<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medecin>
 */
class MedecinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialite' => $this->faker->word(),
            'numero_ordre' => $this->faker->unique()->numerify('ORD-####'),
            'tarif_consultation' => $this->faker->randomFloat(2, 5000, 50000),
            'annees_experience' => $this->faker->numberBetween(0, 30),
            'diplomes' => $this->faker->sentence(),
            'is_active' => true,
            'service_id' => Service::factory(),
        ];
    }
}
