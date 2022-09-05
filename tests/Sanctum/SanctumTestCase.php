<?php

namespace Tests\Sanctum;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SanctumTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * refresh seeders since every time tests are running database is clearing up
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');
    }

    protected string $fakeEmail = 'test@test.com';

    /**
     * Return the Fake User instance
     *
     * @return User
     */
    protected function fakeUser(): User
    {
        return User::whereEmail($this->fakeEmail)->first();
    }

    /**
     * Create fake User
     */
    protected function createFakeUser(): void
    {
        User::create($this->fakeUserData());
    }

    /**
     * Return the array for creating User instance
     *
     * @return array
     */
    protected function fakeUserData(): array
    {
        return [
            'email' => $this->fakeEmail,
            'name' => fake()->name(),
            'password' => Hash::make('password')
        ];
    }

    /**
     * get bearer token for given user
     *
     * @param User $user
     * @return string
     */
    protected function getBearerToken(User $user): string
    {
        return 'Bearer ' . $user->createToken('auth_token')->plainTextToken;
    }
}
