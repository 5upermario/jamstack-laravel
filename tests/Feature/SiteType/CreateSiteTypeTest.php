<?php

namespace Tests\Feature\SiteType;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateSiteTypeTest extends TestCase
{
    use RefreshDatabase;

    public function testEmptyPostData()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/type');

        //assert
        $response->assertJsonValidationErrors(['name', 'api_name']);
    }

    public function testShortNames()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/type', ['name' => 'as', 'api_name' => 'ss']);

        //assert
        $response->assertJsonValidationErrors(['name', 'api_name']);
    }

    public function testSiteOwnerCreatesSiteTypeSuccessfully()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/type', ['name' => 'asd', 'api_name' => 'sss']);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('site_types', ['name' => 'asd', 'api_name' => 'sss', 'description' => '', 'site_id' => $site->id]);
    }

    public function testSiteAdminCreatesSiteTypeSuccessfully()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => 'admin']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/type', ['name' => 'asd', 'api_name' => 'sss']);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('site_types', ['name' => 'asd', 'api_name' => 'sss', 'description' => '', 'site_id' => $site->id]);
    }

    public function testOtherRoleCannotCreateSiteType()
    {
        //setup
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => 'manager']);
        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/type', ['name' => 'asd', 'api_name' => 'sss']);

        //assert
        $response->assertNotFound();
    }
}
