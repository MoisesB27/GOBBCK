<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\User;

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return response()->json($user);
    }
}
