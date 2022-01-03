<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Subject;

class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category' => $this->faker->word,
            'code' => $this->faker->word,
            'label' => $this->faker->word,
            'units' => $this->faker->randomFloat(2, 0, 999999.99),
            'professor' => $this->faker->word,
        ];
    }
}
