<?php

namespace Tests\Feature\Site;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteSiteTest extends TestCase
{
    use RefreshDatabase;

    public function testSiteNotBelongsToAuthenticatedUser()
    {
        //setup
        Sanctum::actingAs(factory(User::class)->create());
        $site = factory(Site::class)->create();

        //run
        $response = $this->delete('/api/site/' . $site->id);

        //assert
        $response->assertNotFound();
    }

    public function testSuccessfulDelete()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);
        Sanctum::actingAs($user);

        //run
        $response = $this->delete('/api/site/' . $site->id);

        //assert
        $response->assertOk();
        $this->assertDatabaseMissing('sites', ['id' => $site->id]);
        $this->assertDatabaseMissing('user_site', ['site_id' => $site->id, 'user_id' => $user->id]);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function testOnlyOwnerCanDeleteSite()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => 'content']);
        Sanctum::actingAs($user);

        //run
        $response = $this->delete('/api/site/' . $site->id);

        //assert
        $response->assertNotFound();
    }
}
