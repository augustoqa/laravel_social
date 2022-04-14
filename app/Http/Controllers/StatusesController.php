<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusesController extends Controller
{
    public function store()
    {
        Status::create([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);
    }
}
