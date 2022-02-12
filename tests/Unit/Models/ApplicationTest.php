<?php

namespace Tests\Unit\Models;

use App\Enums\ApproverAction;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Program;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PharIo\Manifest\ApplicationName;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_CreateApplicationApprovers_job()
    {
        Program::importFromJson(database_path('seeders/data/programs.json'));
        User::importFromJson(database_path('seeders/data/users.json'));
        Term::importFromJson(database_path('seeders/data/terms.json'));

        $program = Program::find(1);

        $application = Application::factory()->create([
            'program_id' => $program->id,
            'user_id' => User::where('role', UserRole::Applicant)->firstOrFail()->id,
        ]);

        $this->assertEquals(
            $program->recommending_user_id,
            $application->approvers->where('action', ApproverAction::RECOMMEND)->first()->user_id
        );

        $this->assertEquals(
            $program->admitting_user_id,
            $application->approvers->where('action', ApproverAction::ADMIT)->first()->user_id
        );

        $this->assertEquals(
            $program->processing_user_id,
            $application->approvers->where('action', ApproverAction::PROCESS)->first()->user_id
        );
    }
}
