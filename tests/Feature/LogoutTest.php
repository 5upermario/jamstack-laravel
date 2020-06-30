<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccessfulLogout()
    {
        // setup
        $user     = factory(User::class)->create(['password' => Hash::make('1qa2ws3ed')]);
        $response = $this->post('/api/login', ['email' => $user->email, 'password' => '1qa2ws3ed']);
        $token    = $response->json('token');

        // run
        $response = $this->get('/api/logout', ['Authorization' => 'Bearer ' . $token]);

        // asssert
        $response->assertOk();
        $response->assertExactJson(['success' => true]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
    }
}
