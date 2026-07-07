<?php
/**
 * Temporary polls & quizzes seeder utility for the live server.
 * Delete this file after running!
 */

use Illuminate\Contracts\Console\Kernel;
use App\Models\Setting;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$demoPolls = [
    [
        'id' => 'poll_1',
        'question' => 'Who will win the 2026 World Cup?',
        'options' => ['Argentina', 'France', 'Brazil', 'England', 'Other'],
        'created_at' => now()->format('Y-m-d H:i:s')
    ],
    [
        'id' => 'poll_2',
        'question' => 'What should Getembe County prioritize in the next budget cycle?',
        'options' => ['Road Networks', 'Youth Tech Hubs', 'Agriculture', 'Healthcare'],
        'created_at' => now()->subDays(2)->format('Y-m-d H:i:s')
    ],
    [
        'id' => 'poll_3',
        'question' => 'Do you support the proposed tax reform bill?',
        'options' => ['Yes, completely', 'No, reject it', 'Needs major revisions', 'Undecided'],
        'created_at' => now()->subDays(5)->format('Y-m-d H:i:s')
    ]
];
Setting::set('simulated_polls', json_encode($demoPolls));

$demoQuizzes = [
    [
        'id' => 'quiz_1',
        'title' => 'Getembe County History & Culture Trivia',
        'questions_count' => 3,
        'created_at' => now()->format('Y-m-d H:i:s')
    ],
    [
        'id' => 'quiz_2',
        'title' => 'Weekly News General Trivia - July 2026',
        'questions_count' => 5,
        'created_at' => now()->subDays(3)->format('Y-m-d H:i:s')
    ]
];
Setting::set('simulated_quizzes', json_encode($demoQuizzes));

echo "✓ Demo polls and quizzes have been successfully seeded!";
