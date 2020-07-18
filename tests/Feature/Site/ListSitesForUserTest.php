<?php

declare(strict_types=1);

namespace Tests\Feature\Site;

use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListSitesForUserTest extends TestCase
{
    use RefreshDatabase;

    public function testListing()
    {
        //setup
        /** @var User $user */
        $user  = factory(User::class)->create();
        /** @var Site $site1 */
        $site1 = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_OWNER]);
        /** @var Site $site2 */
        $site2 = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_ADMIN]);
        /** @var Site $site3 */
        $site3 = $user->sites()->save(factory(Site::class)->make(), ['role' => User::SITE_ROLE_CREATOR]);
        factory(Site::class, 8)->create();
        Sanctum::actingAs($user);

        //run
        $response = $this->get('/api/sites');

        //assert
        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
        $this->assertEquals($site1->name, $response->json('data')[0]['name']);
        $this->assertEquals($site2->name, $response->json('data')[1]['name']);
        $this->assertEquals($site3->name, $response->json('data')[2]['name']);
        $this->assertEquals(User::SITE_ROLE_OWNER, $response->json('data')[0]['role']);
        $this->assertEquals(User::SITE_ROLE_ADMIN, $response->json('data')[1]['role']);
        $this->assertEquals(User::SITE_ROLE_CREATOR, $response->json('data')[2]['role']);
        $this->assertEquals(['id', 'name', 'created_at', 'updated_at', 'role'], array_keys($response->json('data')[0]));
    }
}
