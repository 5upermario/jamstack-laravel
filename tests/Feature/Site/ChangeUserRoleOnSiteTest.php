<?php

namespace Tests\Feature\Site;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangeUserRoleOnSiteTest extends TestCase
{
    use RefreshDatabase;

    public function testOwnerChangeRoleFromCreatorToAdmin()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        Sanctum::actingAs($user);
        $userToChange = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_CREATOR]);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $userToChange->id . '/role/' . User::SITE_ROLE_ADMIN);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToChange->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_ADMIN]);
    }

    public function testOwnerChangeRoleFromAdminToCreator()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        Sanctum::actingAs($user);
        $userToChange = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $userToChange->id . '/role/' . User::SITE_ROLE_CREATOR);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToChange->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_CREATOR]);
    }

    public function testAdminChangeRoleFromCreatorToAdmin()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);
        Sanctum::actingAs($user);
        $userToChange = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_CREATOR]);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $userToChange->id . '/role/' . User::SITE_ROLE_ADMIN);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToChange->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_ADMIN]);
    }

    public function testAdminChangeRoleFromAdminToCreator()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);
        Sanctum::actingAs($user);
        $userToChange = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $userToChange->id . '/role/' . User::SITE_ROLE_CREATOR);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToChange->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_CREATOR]);
    }

    public function testCannotChangeToOtherRole()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        Sanctum::actingAs($user);
        $userToChange = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $userToChange->id . '/role/manager');

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToChange->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_CREATOR]);
    }

    public function testOnlyOwnerOrAdminCanChangeRole()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Site $site */
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_CREATOR]);
        Sanctum::actingAs($user);
        $userToChange = $site->users()->save(factory(User::class)->make(), ['role' => User::SITE_ROLE_CREATOR]);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $userToChange->id . '/role/' . User::SITE_ROLE_ADMIN);

        //assert
        $response->assertNotFound();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToChange->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_CREATOR]);
    }

    public function testUserCannotChangeItsOwnRole()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Site $site */
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        Sanctum::actingAs($user);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $user->id . '/role/' . User::SITE_ROLE_ADMIN);

        //assert
        $response->assertNotFound();
        $this->assertDatabaseHas('user_site', ['user_id' => $user->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_OWNER]);
    }

    public function testWithNonExistingUser()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Site $site */
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        Sanctum::actingAs($user);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/88/role/' . User::SITE_ROLE_ADMIN);

        //assert
        $response->assertNotFound();
    }

    public function testWithNonExistingSite()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var User $userToChange */
        $userToChange = factory(User::class)->create();
        Sanctum::actingAs($user);

        //run
        $response = $this->get('/api/site/99/user/' . $userToChange->id . '/role/' . User::SITE_ROLE_ADMIN);

        //assert
        $response->assertNotFound();
    }

    public function testUserNotBelongsToSite()
    {
        //setup
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Site $site */
        $site = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        /** @var User $userToChange */
        $userToChange = factory(User::class)->create();
        Sanctum::actingAs($user);

        //run
        $response = $this->get('/api/site/' . $site->id . '/user/' . $userToChange->id . '/role/' . User::SITE_ROLE_ADMIN);

        //assert
        $response->assertNotFound();
        $this->assertDatabaseMissing('user_site', ['user_id' => $userToChange->id, 'site_id' => $site->id, 'role' => User::SITE_ROLE_OWNER]);
    }
}
