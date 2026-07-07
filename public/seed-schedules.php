<?php
/**
 * Temporary schedule seeder utility for the live server.
 * Delete this file after running!
 */

use Illuminate\Contracts\Console\Kernel;
use App\Models\Setting;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$defaultTvSchedule = [
    ['time' => '06:00 - 09:00', 'title' => 'Getembe Morning Call', 'desc' => 'Breakfast news and newspaper review.', 'is_playing' => false],
    ['time' => '09:00 - 12:00', 'title' => 'Business Daily', 'desc' => 'Economic trends, stock updates, and trade discussion.', 'is_playing' => false],
    ['time' => '12:00 - 14:00', 'title' => 'News Hour Live', 'desc' => 'Midday headlines, market check, and regional briefs.', 'is_playing' => true],
    ['time' => '14:00 - 16:00', 'title' => 'Health & Sports Highlights', 'desc' => 'Wellness insights and sporting roundups.', 'is_playing' => false],
    ['time' => '16:00 - 19:00', 'title' => 'Regional News Express', 'desc' => 'Community spotlights and county assembly briefings.', 'is_playing' => false],
    ['time' => '19:00 - 21:00', 'title' => 'Evening Prime Time News', 'desc' => 'Comprehensive summary of the day\'s major events.', 'is_playing' => false],
    ['time' => '21:00 - 23:00', 'title' => 'Late Night Spotlight', 'desc' => 'Documentary film showcases and talkshows.', 'is_playing' => false]
];
Setting::set('tv_schedule', $defaultTvSchedule);

$defaultRadioSchedule = [
    ['time' => '06:00 - 10:00', 'title' => 'The Morning Drive', 'desc' => 'Kickstart the day with updates and music.', 'is_playing' => false],
    ['time' => '10:00 - 13:00', 'title' => 'Midday Request Show', 'desc' => 'Listener choices, request lines, and interviews.', 'is_playing' => false],
    ['time' => '13:00 - 16:00', 'title' => 'Getembe Express Drive', 'desc' => 'Mid-afternoon drive show with regional topics and guest experts.', 'is_playing' => true],
    ['time' => '16:00 - 20:00', 'title' => 'Evening Jam & Sports', 'desc' => 'Local sports bulletins and afternoon reviews.', 'is_playing' => false],
    ['time' => '20:00 - 00:00', 'title' => 'Late Night Soul Session', 'desc' => 'Slow jams, classic tracks, and quiet storm conversations.', 'is_playing' => false]
];
Setting::set('radio_schedule', $defaultRadioSchedule);

echo "✓ Stream schedules have been successfully seeded!";
