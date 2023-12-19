<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(\Illuminate\Notifications\DatabaseNotification::class, function (Faker $faker) {
    return [
        'id' => \Illuminate\Support\Str::uuid()->toString(),
        'type' => 'App\Notifications\ExampleNotification',
        'notifiable_type' => 'App\User',
        'notifiable_id' => factory(User::class)->create(),
        'data' => [
            'link' => url('/'),
            'message' => 'Mensaje de la notificación',
        ],
        'read_at' => null,
    ];
});
