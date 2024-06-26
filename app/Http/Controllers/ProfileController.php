<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
       return User::where('id', $uuid)->firstOrFail();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = User::where('id', $uuid)->firstOrFail();

        return $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = User::where('id', $uuid)->firstOrFail();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return $user->delete();
    }

    /**
     * Handle an incoming registration request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response(['message' => 'No credentials'], 401);
        }

            $user = Auth::user();
            $success = $user->createToken('authToken')->plainTextToken;
            return response(['token' => $success], 200);
    }

    /**
     * Display the user's profile information.
     */
    public function userDetails()
    {
        if (Auth::check()) {

            $user = Auth::user();

            return response(['data' => $user],200);
        }

        return response(['data' => 'Unauthorized'],401);
    }

    /**
     * Logout the user.
     */
    public function logout()
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response(['data' => 'User Logout successfully.'],200);
    }


//    /**
//     * Update the user's profile information.
//     */
//    public function update(ProfileUpdateRequest $request): RedirectResponse
//    {
//        $request->user()->fill($request->validated());
//
//        if ($request->user()->isDirty('email')) {
//            $request->user()->email_verified_at = null;
//        }
//
//        $request->user()->save();
//
//        return Redirect::route('profile.edit')->with('status', 'profile-updated');
//    }
//
//    /**
//     * Delete the user's account.
//     */
//    public function destroy(Request $request): RedirectResponse
//    {
//        $request->validateWithBag('userDeletion', [
//            'password' => ['required', 'current_password'],
//        ]);
//
//        $user = $request->user();
//
//        Auth::logout();
//
//        $user->delete();
//
//        $request->session()->invalidate();
//        $request->session()->regenerateToken();
//
//        return Redirect::to('/');
//    }
}
