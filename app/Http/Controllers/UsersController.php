<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    //

    public function index()
    {
        $users = \App\Models\User::where('id', '!=', auth()->id())->get();
        return view('users.index', compact('users'));
    }
}
