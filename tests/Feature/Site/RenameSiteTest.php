<?php

namespace Tests\Feature\Site;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RenameSiteTest extends TestCase
{
    use RefreshDatabase;

    public function testOnlyOwnerCanRenameSite()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(['name' => 'ASDF']), ['role' => 'manager']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/name', ['name' => 'renamed']);

        //assert
        $response->assertNotFound();
    }

    public function testSiteNameShouldHaveMinimumLength()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(['name' => 'ASDF']), ['role' => 'owner']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/name', ['name' => 're']);

        //assert
        $response->assertJsonValidationErrors(['name']);
    }

    public function testSuccessfulRename()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(['name' => 'ASDF']), ['role' => 'owner']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/name', ['name' => 'renamed']);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('sites', ['name' => 'renamed']);
        $this->assertDatabaseCount('sites', 1);
    }
}
