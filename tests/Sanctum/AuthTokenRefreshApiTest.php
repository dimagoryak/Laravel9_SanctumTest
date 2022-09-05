<?php

namespace Tests\Sanctum;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTokenRefreshApiTest extends SanctumTestCase
{
    /**
     * assert guest cannot refresh token
     *
     * @test
     * @return void
     */
    public function assert_guest_cannot_refresh_token()
    {
        $this->postJson(route('api.auth.refresh'))
            ->assertStatus(401);
    }

    /**
     * assert only authorized user can refresh token
     *
     * @test
     * @return void
     */
    public function assert_authorized_user_can_refresh_token()
    {
        $this->createFakeUser();
        $this->postJson(route('api.auth.refresh'), [], [
                'Authorization' => $this->getBearerToken($this->fakeUser())
            ]
        )->assertStatus(200);
    }

    /**
     * assert expired token can be refreshed
     *
     * @test
     * @return void
     */
    public function assert_expired_token_cannot_be_refreshed()
    {
        $this->createFakeUser();
        $user = $this->fakeUser();
        $token = $this->getBearerToken($user);
        $expirationTime = config('sanctum.expiration');
        $user->tokens()
            ->update(
                ['created_at' => now()->addMinutes($expirationTime + 1)]
            );
        $response = $this->postJson(
            route('api.auth.refresh'),
            [],
            ['Authorization' => $token]
        )
        ->assertStatus(200)
        ->assertJsonStructure(['access_token']);

        $this->postJson(
            route('api.auth.logout'),
            ['Authorization' => 'Bearer '. $response['access_token']]
        )
        ->assertStatus(200);
    }

    /**
     * assert expired token cannot be used
     *
     * @test
     * @return void
     */
    public function assert_expired_token_cannot_be_used()
    {
        $this->createFakeUser();
        $user = $this->fakeUser();
        $token = $this->getBearerToken($user);
        $expirationTime = config('sanctum.expiration');
        $user->tokens()
        ->update(
            ['created_at' => now()->addMinutes($expirationTime + 1)]
        );
        $this->postJson(
            route('api.auth.logout'),
            ['Authorization' => $token]
        )->assertStatus(401);
    }
}
