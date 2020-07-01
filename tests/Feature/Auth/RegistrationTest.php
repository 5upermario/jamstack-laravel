<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationWithEmptyData()
    {
        //run
        $response = $this->post('/api/registration');

        //assert
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function testRegistrationWithWrongEmail()
    {
        //run
        $response = $this->post('/api/registration', ['email' => 'kiuhghjk', 'password' => 'iuzhghjkio', 'password_confirmation' => 'iuzhghjkio']);

        //assert
        $response->assertJsonValidationErrors(['email']);
    }

    public function testRegistrationWithEmptyPasswordConfirmation()
    {
        //run
        $response = $this->post('/api/registration', ['email' => 'kiuhghjk', 'password' => 'iuzhghjkio']);

        //assert
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function testRegistrationWithWrongPasswordConfirmation()
    {
        //run
        $response = $this->post('/api/registration', ['email' => 'kiuhghjk', 'password' => 'iuzhghjkio', 'password_confirmation' => 'iuzhghjkiio']);

        //assert
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function testSuccessfulRegistration()
    {
        //run
        $response = $this->post('/api/registration', ['email' => 'kiuhghjk@asdf.hu', 'password' => 'iuzhghjkio', 'password_confirmation' => 'iuzhghjkio']);

        //assert
        $response->assertOk();
        $this->assertIsString($response->json('token'));
        $this->assertDatabaseHas('users', ['email' => 'kiuhghjk@asdf.hu']);
        $this->assertDatabaseMissing('users', ['passowrd' => 'iuzhghjkio']);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'login']);
    }

    public function testNoEmailDuplication()
    {
        //setup
        $this->post('/api/registration', ['email' => 'kiuhghjk@asdf.hu', 'password' => 'iuzhghjkio', 'password_confirmation' => 'iuzhghjkio']);

        //run
        $response = $this->post('/api/registration', ['email' => 'kiuhghjk@asdf.hu', 'password' => 'iuzhghjkio', 'password_confirmation' => 'iuzhghjkio']);

        //assert
        $response->assertJsonValidationErrors(['credentials']);
    }
}
