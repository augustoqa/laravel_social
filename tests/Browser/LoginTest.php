<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function registered_users_can_login()
    {
        factory(User::class)->create(['email' => 'cesar@mail.com']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'cesar@mail.com')
                    ->type('password', 'secret')
                    ->press('#login-btn')
                    ->assertPathIs('/')
                    ->assertAuthenticated();
        });
    }
}
