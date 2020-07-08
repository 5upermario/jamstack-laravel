<?php

namespace Tests\Feature\Site;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RemoveUserFromSiteTest extends TestCase
{
    use RefreshDatabase;

    /** @var User $currentUser */
    private $currentUser;
    /** @var User $userToRemove */
    private $userToRemove;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUser  = factory(User::class)->create();
        $this->userToRemove = factory(User::class)->create();

        Sanctum::actingAs($this->currentUser);
    }

    public function testRemoveNonExistingUser()
    {
        //setup
        $site = $this->currentUser->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);

        //run
        $response = $this->delete('/api/site/' . $site->id . '/user/88');

        //assert
        $response->assertOk();
    }

    public function testRemoveNonAssignedUser()
    {
        //setup
        $site = $this->currentUser->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);

        //run
        $response = $this->delete('/api/site/' . $site->id . '/user/' . $this->userToRemove->id);

        //assert
        $response->assertOk();
    }

    public function testOwnerCanRemoveAdmin()
    {
        //setup
        $site = $this->currentUser->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);
        $site->users()->attach($this->userToRemove->id, ['role' => 'admin']);

        //run
        $response = $this->delete('/api/site/' . $site->id . '/user/' . $this->userToRemove->id);

        //assert
        $response->assertOk();
        $this->assertDatabaseMissing('user_site', ['site_id' => $site->id, 'user_id' => $this->userToRemove->id, 'role' => 'admin']);
    }

    public function testOwnerCanRemoveCreator()
    {
        //setup
        $site = $this->currentUser->sites()->save(factory(Site::class)->make(), ['role' => 'owner']);
        $site->users()->attach($this->userToRemove->id, ['role' => 'creator']);

        //run
        $response = $this->delete('/api/site/' . $site->id . '/user/' . $this->userToRemove->id);

        //assert
        $response->assertOk();
        $this->assertDatabaseMissing('user_site', ['site_id' => $site->id, 'user_id' => $this->userToRemove->id, 'role' => 'creator']);
    }

    public function testAdminCanRemoveAdmin()
    {
        //setup
        $site = $this->currentUser->sites()->save(factory(Site::class)->make(), ['role' => 'admin']);
        $site->users()->attach($this->userToRemove->id, ['role' => 'admin']);

        //run
        $response = $this->delete('/api/site/' . $site->id . '/user/' . $this->userToRemove->id);

        //assert
        $response->assertOk();
        $this->assertDatabaseMissing('user_site', ['site_id' => $site->id, 'user_id' => $this->userToRemove->id, 'role' => 'admin']);
    }

    public function testAdminCanRemoveCreator()
    {
        //setup
        $site = $this->currentUser->sites()->save(factory(Site::class)->make(), ['role' => 'admin']);
        $site->users()->attach($this->userToRemove->id, ['role' => 'creator']);

        //run
        $response = $this->delete('/api/site/' . $site->id . '/user/' . $this->userToRemove->id);

        //assert
        $response->assertOk();
        $this->assertDatabaseMissing('user_site', ['site_id' => $site->id, 'user_id' => $this->userToRemove->id, 'role' => 'creator']);
    }

    public function testCreatorCannotRemoveUser()
    {
        //setup
        $site = $this->currentUser->sites()->save(factory(Site::class)->make(), ['role' => 'creator']);
        $site->users()->attach($this->userToRemove->id, ['role' => 'admin']);

        //run
        $response = $this->delete('/api/site/' . $site->id . '/user/' . $this->userToRemove->id);

        //assert
        $response->assertNotFound();
    }
}
