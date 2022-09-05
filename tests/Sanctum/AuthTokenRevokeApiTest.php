<?php

namespace Tests\Sanctum;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTokenRevokeApiTest extends SanctumTestCase
{
    /**
     * assert guest cannot logout
     *
     * @test
     * @return void
     */
    public function assert_guest_cannot_logout()
    {
        $this->postJson(route('api.auth.logout'))
            ->assertStatus(401);
    }

    /**
     * assert authorized user can logout
     *
     * @test
     * @return void
     */
    public function assert_authorized_user_can_logout()
    {
        $this->createFakeUser();
        $this->postJson(route('api.auth.logout'), [], [
            'Authorization' => $this->getBearerToken($this->fakeUser())
        ])
        ->assertStatus(200);
    }
}
