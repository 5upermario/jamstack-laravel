<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginWithEmptyData()
    {
        // run
        $response = $this->post('/api/login');

        // assert
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function testLoginWithWrongEmail()
    {
        // run
        $response = $this->post('/api/login', ['email' => 'iuztghji', 'password' => 'iuztghjk']);

        // assert
        $response->assertJsonValidationErrors(['email']);
    }

    public function testLoginWithNonExistingUser()
    {
        // run
        $response = $this->post('/api/login', ['email' => 'iuztghji@uzhbnj.hu', 'password' => 'iuztghjk']);

        // assert
        $response->assertJsonValidationErrors(['email']);
    }

    public function testLoginWithWrongPassword()
    {
        // setup
        $user = factory(User::class)->create();

        // run
        $response = $this->post('/api/login', ['email' => $user->email, 'password' => 'iuztghjk']);

        // assert
        $response->assertJsonValidationErrors(['email']);
        $response->assertJsonPath('errors.email.0', 'The provided credentials are incorrect.');
    }

    public function testSuccessfulLogin()
    {
        // setup
        $user = factory(User::class)->create(['password' => Hash::make('1qa2ws3ed')]);

        // run
        $response = $this->post('/api/login', ['email' => $user->email, 'password' => '1qa2ws3ed']);

        // assert
        $response->assertOk();
        $response->assertJsonMissingValidationErrors(['email', 'password']);
        $response->assertJsonStructure(['token']);
        $this->assertIsString($response->json('token'));
        $this->assertGreaterThan(0, strlen($response->json('token')));
        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id]);
    }
}
