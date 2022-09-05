<?php

namespace Tests\Sanctum;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTokenCreateApiTest extends SanctumTestCase
{
    /**
     * Validation enabled, so correct data needs to be passed
     *
     * @test
     * @return void
     */
    public function assert_validation_enabled()
    {
        $this->postJson(route('api.auth.create'))
            ->assertStatus(422);
    }

    /**
     * Validation enabled, so correct credential needs to be passed
     *
     * @test
     * @return void
     */
    public function assert_invalid_credential()
    {
        $this->postJson(route('api.auth.create'), [
            'username' => Str::random(),
            'pass' => Str::random()
        ])
        ->assertStatus(422);
    }

    /**
     * Response should be {'access_token' => '...', 'token_type' => Bearer}
     * Second response should be {'message' => '...'}
     *
     * @test
     * @return void
     */
    public function assert_correct_json_structure_returned()
    {
        $fakeData = $this->fakeUserData();
        $this->postJson(route('api.auth.create'),
            $fakeData
        )
        ->assertStatus(200)
        ->assertJson(['token_type' => 'Bearer'])
        ->assertJsonStructure([
            'access_token',
            'token_type'
        ]);
    }

    /**
     * assert created token can be used
     *
     * @test
     * @return void
     */
    public function assert_created_token_can_be_used()
    {
        $fakeData = $this->fakeUserData();
        $this->postJson(route('api.auth.create'),
            $fakeData
        )->assertStatus(200);

        $fakeUser = $this->fakeUser();

        Sanctum::actingAs($fakeUser);

        $this->postJson(route('api.auth.logout'))
        ->assertStatus(200);
    }
}
