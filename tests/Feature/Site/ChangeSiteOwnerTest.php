<?php

declare(strict_types=1);

namespace Tests\Feature\Site;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangeSiteOwnerTest extends TestCase
{
    use RefreshDatabase;

    public function testChangeSiteOwnerSuccessfully()
    {
        //setup
        /** @var User $owner */
        $owner = factory(User::class)->create();
        /** @var Site $site */
        $site  = $owner->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        /** @var User $admin */
        $admin = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);
        Sanctum::actingAs($owner);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $admin->id . '/owner');

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $owner->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_ADMIN]);
        $this->assertDatabaseHas('user_site', ['user_id' => $admin->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_OWNER]);
    }

    public function testCreatorCannotBeOwner()
    {
        //setup
        /** @var User $owner */
        $owner = factory(User::class)->create();
        /** @var Site $site */
        $site  = $owner->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        /** @var User $creator */
        $creator = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_CREATOR]);
        Sanctum::actingAs($owner);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $creator->id . '/owner');

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $owner->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_OWNER]);
        $this->assertDatabaseHas('user_site', ['user_id' => $creator->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_CREATOR]);
    }

    public function testWithNonExistingUser()
    {
        //setup
        /** @var User $owner */
        $owner = factory(User::class)->create();
        /** @var Site $site */
        $site  = $owner->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        Sanctum::actingAs($owner);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/77/owner');

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $owner->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_OWNER]);
    }

    public function testWithNonExistingSite()
    {
        //setup
        /** @var User $owner */
        $owner = factory(User::class)->create();
        /** @var User $owner */
        $randomUser = factory(User::class)->create();
        Sanctum::actingAs($owner);

        //run
        $response = $this->get('/api/site/66/user/' . $randomUser->id . '/owner');

        //assert
        $response->assertNotFound();
    }

    public function testOnlyOwnerCanChangeSiteOwner()
    {
        //setup
        /** @var User $owner */
        $owner = factory(User::class)->create();
        /** @var Site $site */
        $site  = $owner->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        /** @var User $admin */
        $admin = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);
        Sanctum::actingAs($admin);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $admin->id . '/owner');

        //assert
        $response->assertNotFound();
        $this->assertDatabaseHas('user_site', ['user_id' => $owner->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_OWNER]);
        $this->assertDatabaseHas('user_site', ['user_id' => $admin->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_ADMIN]);
    }
}
