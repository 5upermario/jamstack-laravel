<?php

namespace Tests\Feature\Site;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddAdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function testAddExistingUserAsOwner()
    {
        //setup
        /** @var User $user */
        $user      = factory(User::class)->create();
        /** @var User $userToAdd */
        $userToAdd = factory(User::class)->create();
        /** @var Site $site */
        $site      = $user->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);

        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/user', ['email' => $userToAdd->email, 'role' => 'admin']);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToAdd->id, 'site_id' => $site->id, 'role' => 'admin']);
    }

    public function testAddNonExistingUser()
    {
        //setup
        /** @var User $user */
        $user      = factory(User::class)->create();
        /** @var Site $site */
        $site      = $user->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);

        Sanctum::actingAs($user);

        //run

        //assert
    }

    public function testAddExistingUserAsAdmin()
    {
        //setup
        /** @var User $user */
        $user      = factory(User::class)->create();
        /** @var User $userToAdd */
        $userToAdd = factory(User::class)->create();
        /** @var Site $site */
        $site      = $user->sites()->save(factory(Site::class)->make(), ['role' => 'admin']);

        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/user', ['email' => $userToAdd->email, 'role' => 'admin']);

        //assert
        $response->assertOk();
        $this->assertDatabaseHas('user_site', ['user_id' => $userToAdd->id, 'site_id' => $site->id, 'role' => 'admin']);
    }

    public function testOtherRoleCannotAddUser()
    {
        //setup
        /** @var User $user */
        $user      = factory(User::class)->create();
        /** @var User $userToAdd */
        $userToAdd = factory(User::class)->create();
        /** @var Site $site */
        $site      = $user->sites()->save(factory(Site::class)->make(), ['role' => 'creator']);

        Sanctum::actingAs($user);

        //run
        $response = $this->post('/api/site/' . $site->id . '/user', ['email' => $userToAdd->email, 'role' => 'admin']);

        //assert
        $response->assertNotFound();
        $this->assertDatabaseMissing('user_site', ['user_id' => $userToAdd->id, 'site_id' => $site->id, 'role' => 'admin']);
    }
}
