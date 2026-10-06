<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement([
                'Plan Básico', 'Plan Estándar', 'Plan Premium', 'Plan Anual', 'Plan Empresarial',
            ]) . ' ' . $this->faker->unique()->numberBetween(100, 999),
            'descripcion' => $this->faker->sentence(12),
            'precio' => $this->faker->randomFloat(2, 9.99, 499.99),
            'duracion_dias' => $this->faker->randomElement([7, 30, 90, 180, 365]),
            'activo' => $this->faker->boolean(80),
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}