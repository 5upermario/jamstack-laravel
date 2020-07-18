<?php

namespace Tests\Unit;

use App\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCannotGetOwner()
    {
        //setup
        $role = User::SITE_ROLE_OWNER;

        //run
        $result = User::getRole($role);

        //assert
        $this->assertEquals($result, User::SITE_ROLE_CREATOR);
    }

    public function testGetUserRoleAsAdmin()
    {
        //setup
        $role = User::SITE_ROLE_ADMIN;

        //run
        $result = User::getRole($role);

        //assert
        $this->assertEquals($result, $role);
    }

    public function testGetUserRoleAsCreator()
    {
        //setup
        $role = User::SITE_ROLE_CREATOR;

        //run
        $result = User::getRole($role);

        //assert
        $this->assertEquals($result, $role);
    }

    public function testGetUserRoleFailsOnOtherRole()
    {
        //setup
        $role = 'random_role';

        //run
        $result = User::getRole($role);

        //assert
        $this->assertEquals($result, User::SITE_ROLE_CREATOR);
    }
}
