<?php
// Script to debug Admin Auth
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "--- Admin Debug Start ---\n";

$email = 'admin@example.com';
$password = 'password';

// 1. Check if user exists
$admin = Admin::where('email', $email)->first();

if (!$admin) {
    echo "ERROR: Admin with email '$email' NOT FOUND in database.\n";
} else {
    echo "SUCCESS: Admin found. ID: " . $admin->id . "\n";
    echo "Stored Hash: " . $admin->password . "\n";
    
    // 2. Check Password Hash
    if (Hash::check($password, $admin->password)) {
        echo "SUCCESS: Password hash matches.\n";
    } else {
        echo "ERROR: Password hash DOES NOT match.\n";
        echo "Expected Password: '$password'\n";
    }

    // 3. Attempt Login via Guard
    $credentials = ['email' => $email, 'password' => $password];
    
    try {
        if (Auth::guard('admin')->attempt($credentials)) {
            echo "SUCCESS: Auth::guard('admin')->attempt() returned TRUE.\n";
        } else {
            echo "ERROR: Auth::guard('admin')->attempt() returned FALSE.\n";
        }
    } catch (\Exception $e) {
        echo "EXCEPTION during auth attempt: " . $e->getMessage() . "\n";
    }
}

echo "--- Admin Debug End ---\n";
