<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function show(User $user)
    {
        $friendshipStatus = optional(Friendship::where([
            'sender_id' => auth()->id(),
            'recipient_id' => $user->id,
        ])->first())->status;

        return view('users.show', compact('user', 'friendshipStatus'));
    }
}
