<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanSeeProfilesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_see_profiles_tests()
    {
        $this->withoutExceptionHandling();

        factory(User::class)->create(['name' => 'Cesar']);

        $this->get('@Cesar')->assertSee('Cesar');

    }
}
