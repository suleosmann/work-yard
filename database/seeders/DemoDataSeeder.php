<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\TaskActivity;
use App\Models\User;
use App\Support\Role as RoleName;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'suleosman73@gmail.com'],
            [
                'name' => 'Suleiman Osman',
                'phone' => '254718772203',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([RoleName::ADMIN]);

        $pm = User::firstOrCreate(
            ['email' => 'ada@workyard.test'],
            ['name' => 'Ada', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $pm->syncRoles([RoleName::PM]);

        $developer = User::firstOrCreate(
            ['email' => 'musa@workyard.test'],
            ['name' => 'Musa', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $developer->syncRoles([RoleName::DEVELOPER]);

        $qa = User::firstOrCreate(
            ['email' => 'zainab@workyard.test'],
            ['name' => 'Zainab', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $qa->syncRoles([RoleName::QA]);

        $project = Project::firstOrCreate(
            ['slug' => 'claims'],
            [
                'name' => 'Claims Disbursement System',
                'description' => 'Fund distribution & batch payouts',
            ]
        );

        if ($project->tasks()->count() > 0) {
            return;
        }

        $project->resources()->createMany([
            ['title' => 'Staging API base URL', 'subtitle' => 'api-staging.claims.internal', 'type' => 'ENV'],
            ['title' => 'Payload schema notes', 'subtitle' => 'providerShare / vendorShare fields', 'type' => 'DOC'],
            ['title' => 'facility_master repo', 'subtitle' => 'github.com/org/claims-core', 'type' => 'REP'],
        ]);

        $tasks = [
            ['title' => 'Fix claims array vs object bug', 'status' => 'done', 'priority' => 'high', 'assignee_id' => $developer->id, 'due_date' => '2026-06-02', 'description' => 'Batch payload was nesting claims as an object instead of an array.'],
            ['title' => 'Add adjustmentAmount validation', 'status' => 'qa', 'priority' => 'medium', 'assignee_id' => $developer->id, 'due_date' => '2026-08-05', 'description' => 'Reject batches where adjustmentAmount is negative.'],
            ['title' => 'Batch invoice metadata export', 'status' => 'in_progress', 'priority' => 'medium', 'assignee_id' => $developer->id, 'due_date' => '2026-08-01', 'description' => 'Export batch + invoice metadata as CSV for finance.'],
            ['title' => 'Design multi-claim consolidation flow', 'status' => 'pending', 'priority' => 'high', 'assignee_id' => null, 'due_date' => '2026-08-10', 'description' => 'Combine multiple claims into a single disbursement payload.'],
            ['title' => 'Set up RabbitMQ for async batch jobs', 'status' => 'pending', 'priority' => 'medium', 'assignee_id' => null, 'due_date' => '2026-08-20', 'description' => 'Move large batch processing off the request cycle.'],
        ];

        foreach ($tasks as $data) {
            $task = $project->tasks()->create($data);

            if ($task->status->value !== 'pending') {
                TaskActivity::create([
                    'task_id' => $task->id,
                    'user_id' => $task->assignee_id ?? $admin->id,
                    'body' => 'Moved from Pending to '.$task->status->label().'.',
                    'created_at' => now(),
                ]);
            }
        }
    }
}
