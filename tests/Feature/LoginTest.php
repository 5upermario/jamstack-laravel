<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
