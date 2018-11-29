<?php

/**
 * PHP Version 7.2
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CheckEmailAvailabilityTest.php
 */

namespace FaiscaCriativa\LaravelExtensions\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * Test email async validation.
 *
 * @category Tests
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/CheckEmailAvailabilityTest.php
 */
class CheckEmailAvailabilityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Setup the test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Test email check with an empty input.
     *
     * @return void
     */
    public function testCheckEmailAvailabilityEmptyEmail()
    {
        $email    = $this->faker()->email();
        $response = $this->getJson('api/check-email-availability?email=');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['error', 'message'])
            ->assertJson(
                [
                    'error' => true,
                    'message' => trans(
                        'validation.required',
                        ['attribute' => 'email']
                    )
                ]
            );
    }

    /**
     * Test email check with an invalid input.
     *
     * @return void
     */
    public function testCheckEmailAvailabilityInvalidEmail()
    {
        $email    = 'not.a.@validemail.com';
        $response = $this->getJson(sprintf('api/check-email-availability?email=%s', $email));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['error', 'message'])
            ->assertJson(
                [
                    'error' => true,
                    'message' => trans(
                        'validation.email',
                        ['attribute' => 'email']
                    )
                ]
            );
    }

    /**
     * Test email check with an already registered email.
     *
     * @return void
     */
    public function testCheckEmailAvailabilityAlreadyRegisteredEmail()
    {
        $email    = $this->faker()->email;

        DB::insert(
            'insert into users (id, email, password) values (?, ?, ?)',
            [
                1,
                $email,
                $this->faker()->password
            ]
        );

        $response = $this->getJson(sprintf('api/check-email-availability?email=%s', $email));

        $response->assertOk()
            ->assertJsonStructure(['error', 'data'])
            ->assertJson(
                [
                    'error' => false,
                    'data'  => [
                        'available' => false
                    ]
                ]
            );

        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    /**
     * Test email check with an unregistered email.
     *
     * @return void
     */
    public function testCheckEmailAvailabilityNotRegisteredEmail()
    {
        $email    = $this->faker()->email();
        $response = $this->getJson(sprintf('api/check-email-availability?email=%s', $email));

        $response->assertOk()
            ->assertJsonStructure(['error', 'data'])
            ->assertJson(
                [
                    'error' => false,
                    'data'  => [
                        'available' => true
                    ]
                ]
            );

        $this->assertDatabaseMissing('users', ['email' => $email]);
    }
}
