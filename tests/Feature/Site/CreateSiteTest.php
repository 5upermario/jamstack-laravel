<?php

namespace Tests\Feature\Site;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateSiteTest extends TestCase
{
    use RefreshDatabase;

    public function testWithoutName()
    {
        //setup
        Sanctum::actingAs(factory(User::class)->create());

        //run
        $response = $this->post('/api/site');

        //assert
        $response->assertJsonValidationErrors(['name']);
    }

    public function testShortName()
    {
        //setup
        Sanctum::actingAs(factory(User::class)->create());

        //run
        $response = $this->post('/api/site', ['name' => 'as']);

        //assert
        $response->assertJsonValidationErrors(['name']);
    }

    public function testSuccessfulCreation()
    {
        //setup
        $user = factory(User::class)->create();
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site', ['name' => 'Test']);

        //assert
        $response->assertOk();
        $this->assertTrue($response->json('success'));
        $this->assertIsInt($response->json('id'));
        $this->assertDatabaseHas('sites', ['id' => $response->json('id'), 'name' => 'Test']);
        $this->assertDatabaseHas('user_site', ['user_id' => $user->id, 'site_id' => $response->json('id'), 'role' => 'owner']);
    }
}
