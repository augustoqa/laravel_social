<?php

namespace Tests\Browser;

use App\Models\Status;
use App\User;
use Illuminate\Notifications\DatabaseNotification;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UsersCanGetTheirNotificationsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @return void
     */
    function users_can_see_their_notifications_in_the_navbar()
    {
        $user = factory(User::class)->create();
        $status = factory(Status::class)->create();

        $notification = factory(DatabaseNotification::class)->create([
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Haz recibido un like',
                'link' => route('statuses.show', $status),
            ]
        ]);

        $this->browse(function (Browser $browser) use ($user, $notification, $status) {
            $browser->loginAs($user)
                ->visit('/')
                ->click('@notifications')
                ->waitForText('Haz recibido un like')
                ->assertSee('Haz recibido un like')
                ->click("@{$notification->id}")
                ->assertUrlIs($status->path())

                ->click('@notifications')
                ->pause(1000)
                ->press("@mark-as-read-{$notification->id}")
                ->waitFor("@mark-as-unread-{$notification->id}")
                ->assertMissing("@mak-as-read-{$notification->id}")

                ->press("@mark-as-unread-{$notification->id}")
                ->waitFor("@mark-as-read-{$notification->id}")
                ->assertMissing("@mak-as-unread-{$notification->id}");
        });
    }

    /** @test */
    function users_can_see_their_like_notifications_in_real_time()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $status = factory(Status::class)->create(['user_id' => $user1->id]);

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user1, $user2, $status) {
            $browser1->loginAs($user1)->visit('/');

            $browser2->loginAs($user2)
                ->visit('/')
                ->waitFor('@like-btn')
                ->press('@like-btn')
                ->pause(1500);

            $browser1->assertSeeIn('@notifications-count', 1);
        });
    }

    /** @test */
    function users_can_see_their_comment_notifications_in_real_time()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $status = factory(Status::class)->create(['user_id' => $user1->id]);

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user1, $user2, $status) {
            $browser1->loginAs($user1)->visit('/');

            $browser2->loginAs($user2)
                ->visit('/')
                ->waitFor('@comment-btn')
                ->type('comment', 'Mi comentario')
                ->press('@comment-btn')
                ->pause(1500);

            $browser1->assertSeeIn('@notifications-count', 1);
        });
    }
}
