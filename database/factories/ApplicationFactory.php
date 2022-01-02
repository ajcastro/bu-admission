<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Models\User;

class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'last_name' => $this->faker->lastName,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->word,
            'birthdate' => $this->faker->date(),
            'gender' => $this->faker->word,
            'email' => $this->faker->safeEmail,
            'mobile_number' => $this->faker->word,
            'phone_number' => $this->faker->phoneNumber,
            'work_number' => $this->faker->word,
            'company' => $this->faker->company,
            'residence_address_line_1' => $this->faker->word,
            'residence_address_line_2' => $this->faker->word,
            'residence_municipality' => $this->faker->word,
            'residence_province' => $this->faker->word,
            'residence_zip_code' => $this->faker->word,
            'residence_country' => $this->faker->word,
            'same_with_residence_address' => $this->faker->boolean,
            'permanent_address_line_1' => $this->faker->word,
            'permanent_address_line_2' => $this->faker->word,
            'permanent_municipality' => $this->faker->word,
            'permanent_province' => $this->faker->word,
            'permanent_zip_code' => $this->faker->word,
            'permanent_country' => $this->faker->word,
            'status' => $this->faker->word,
            'total_units' => $this->faker->randomFloat(2, 0, 999999.99),
        ];
    }
}
