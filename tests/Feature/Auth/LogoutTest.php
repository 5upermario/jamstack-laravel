<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccessfulLogout()
    {
        // setup
        $user     = factory(User::class)->create();
        $response = $this->post('/api/login', ['email' => $user->email, 'password' => 'password']);
        $token    = $response->json('token');

        // run
        $response = $this->get('/api/logout', ['Authorization' => 'Bearer ' . $token]);

        // asssert
        $response->assertOk();
        $response->assertExactJson(['success' => true]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
    }
}
