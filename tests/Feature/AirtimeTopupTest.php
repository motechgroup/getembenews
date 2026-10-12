<?php

namespace Tests\Feature;

use App\Models\AirtimeTopup;
use App\Models\Setting;
use App\Models\User;
use App\Support\Airtime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AirtimeTopupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('airtime_mode', 'mock');
        Setting::set('airtime_at_username', '');
        Setting::set('airtime_at_api_key', '');
        Setting::set('sms_at_username', '');
        Setting::set('sms_at_api_key', '');
    }

    public function test_phone_number_formatting(): void
    {
        $this->assertEquals('+254712345678', Airtime::formatE164('0712345678'));
        $this->assertEquals('+254112345678', Airtime::formatE164('0112345678'));
        $this->assertEquals('+254712345678', Airtime::formatE164('254712345678'));
        $this->assertEquals('+254712345678', Airtime::formatE164('+254712345678'));
        $this->assertEquals('+254712345678', Airtime::formatE164('712345678'));

        $this->assertEquals('0712345678', Airtime::formatLocal('+254712345678'));
        $this->assertEquals('0112345678', Airtime::formatLocal('+254112345678'));
    }

    public function test_airtime_send_in_mock_mode_marks_user(): void
    {
        $user = User::factory()->create([
            'phone' => '0712345678',
            'mpesa_phone' => '0712345678',
        ]);

        $this->assertFalse($user->hasReceivedAirtime());

        $result = Airtime::send('0712345678', 20.0, $user);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('airtime_topups', [
            'user_id' => $user->id,
            'phone' => '0712345678',
            'phone_e164' => '+254712345678',
            'amount' => 20.0,
            'status' => 'sent',
        ]);

        $user->refresh();
        $this->assertTrue($user->hasReceivedAirtime());
        $this->assertEquals(20.0, $user->totalAirtimeReceived());
    }

    public function test_airtime_send_with_africas_talking_api(): void
    {
        Setting::set('airtime_at_username', 'my_app_user');
        Setting::set('airtime_at_api_key', 'test_key_123');
        Setting::set('airtime_mode', 'live');

        Http::fake([
            'https://api.africastalking.com/version1/airtime/send' => Http::response([
                'errorMessage' => 'None',
                'numSent' => 1,
                'totalAmount' => 'KES 50.0000',
                'totalDiscount' => 'KES 2.0000',
                'responses' => [
                    [
                        'phoneNumber' => '+254712345678',
                        'amount' => 'KES 50.0000',
                        'status' => 'Sent',
                        'requestId' => 'ATQid_TEST_12345',
                        'discount' => 'KES 2.0000',
                        'errorMessage' => 'None'
                    ]
                ]
            ], 200),
        ]);

        $user = User::factory()->create([
            'phone' => '0712345678',
        ]);

        $result = Airtime::send('0712345678', 50.0, $user);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('airtime_topups', [
            'user_id' => $user->id,
            'phone_e164' => '+254712345678',
            'amount' => 50.0,
            'status' => 'sent',
            'request_id' => 'ATQid_TEST_12345',
            'discount' => 2.0,
        ]);
    }

    public function test_welcome_airtime_on_signup_when_enabled(): void
    {
        Setting::set('airtime_auto_on_signup', true);
        Setting::set('airtime_auto_amount', 15);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Kenyan',
            'phone' => '0799887766',
            'password' => 'secret1234',
        ]);

        $response->assertStatus(201);

        $user = User::where('phone', '0799887766')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasReceivedAirtime());
        $this->assertEquals(15.0, $user->totalAirtimeReceived());
    }

    public function test_admin_users_manager_can_dispatch_airtime(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $targetUser = User::factory()->create([
            'name' => 'Recipient Guy',
            'phone' => '0711223344',
            'role' => 'user',
        ]);

        $component = Volt::actingAs($admin)
            ->test('admin-users-manager')
            ->call('openAirtimeModal', $targetUser->id)
            ->assertSet('isAirtimeModalOpen', true)
            ->assertSet('airtimeTargetPhone', '0711223344')
            ->set('airtimeAmount', 30)
            ->call('sendAirtime')
            ->assertSet('isAirtimeModalOpen', false);

        $targetUser->refresh();
        $this->assertTrue($targetUser->hasReceivedAirtime());
        $this->assertEquals(30.0, $targetUser->totalAirtimeReceived());
    }
}
