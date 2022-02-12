<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Program;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'program_id' => Program::inRandomOrder()->value('id'),
            'term_id' => Term::inRandomOrder()->value('id'),
            'user_id' => User::factory()->state(['role' => UserRole::Applicant]),
            'last_name' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->word(),
            'birthdate' => $this->faker->date(),
            'gender' => $this->faker->randomElement([
                Gender::Male,
                Gender::Female,
            ]),
            'email' => $this->faker->safeEmail(),
            'mobile_number' => $this->faker->phoneNumber(),
            'phone_number' => $this->faker->phoneNumber(),
            'work_number' => $this->faker->phoneNumber(),
            'company' => $this->faker->company(),
            'residence_address_line_1' => $this->faker->word(),
            'residence_address_line_2' => $this->faker->word(),
            'residence_municipality' => $this->faker->word(),
            'residence_province' => $this->faker->word(),
            'residence_zip_code' => $this->faker->word(),
            'residence_country' => $this->faker->country(),
            'same_with_residence_address' => $this->faker->boolean(),
            'permanent_address_line_1' => $this->faker->word(),
            'permanent_address_line_2' => $this->faker->word(),
            'permanent_municipality' => $this->faker->word(),
            'permanent_province' => $this->faker->word(),
            'permanent_zip_code' => $this->faker->word(),
            'permanent_country' => $this->faker->country(),
            'status' => ApplicationStatus::PENDING,
            'total_units' => $this->faker->randomFloat(2, 0, 999999.99),
        ];
    }
}
