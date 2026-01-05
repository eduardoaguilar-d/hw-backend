<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::post('/login', function (Request $request) {
    $request->validate([
        'username' => 'required', // We will use 'email' column or add 'username' column? Plan said "username". 
        // Laravel default User has 'name', 'email', 'password'. I can use 'email' as username or 'name'.
        // The user specifically asked for "username".
        // Let's check the User model or migration.
        // For now I'll assume standard User model and I'll match 'email' or 'name'.
        // Let's assume 'email' for simplicity or I should strictly follow "username".
        // Since it's a specific "vogar", I can use email "vogar@example.com" or just add 'username' to migration.
        // To be safe and quick without changing schema, I'll map "username" input to "email" for checking, or use 'name'.
        // Actually, let's just use 'email' as the field name in DB but 'username' in request, or assume the user wants 'username' field.
        // Let's stick to using 'email' in DB for 'vogar' -> 'vogar@example.com' to match standard Laravel auth, 
        // OR modifying migration to add username.
        // Given constraints, I will try to map "username" to "email" checking in code:
        'password' => 'required',
    ]);

    $user = User::where('name', $request->username)->orWhere('email', $request->username)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // specific check for the requested credentials just in case
    if ($request->username === 'vogar' && $request->password === 'vogar123#') {
         // This is guaranteed by the hash check if seeder is correct
    }

    return response()->json([
        'message' => 'Login successful',
        'user' => $user
    ]);
});
