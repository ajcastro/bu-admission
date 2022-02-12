<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::importFromJson(database_path('seeders/data/users.json'));
        Term::importFromJson(database_path('seeders/data/terms.json'));
        Program::importFromJson(database_path('seeders/data/programs.json'));
        Subject::importFromJson(database_path('seeders/data/subjects.json'));
    }
}
