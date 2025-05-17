<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\ProfileRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\Api\V1\RegisterResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\UserResource;
use App\Models\Admin\UserLog;
use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    use HttpResponses;
    use ImageUpload;

    private const PLAYER_ROLE = 4;

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('user_name', 'password');

        $user = User::where('user_name', $request->user_name)->first();

        if (! Auth::attempt($credentials)) {
            return $this->error('', [
                'user_name' => 'Credentials do not match!',
            ], 422);
        }

        if (Auth::user()->status == 0) {
            return $this->error('', [
                'user_name' => 'Your account has been banned. Please contact your agent.',
            ], 422);
        }

        $user = User::where('user_name', $request->user_name)->first();
        if (! $user->hasRole('Player')) {
            return $this->error('', [
                'user_name' => 'You are not a player. Please contact your agent or owner.',
            ], 422);
        }

        // Revoke all previous tokens for the user
        $user->tokens()->delete();

        // don't use users table session_id column in confirm version
        // // Check if the user is already logged in from another device
        // if ($user->session_id) {
        //     // Invalidate the previous session
        //     $previousSession = Session::find($user->session_id);
        //     if ($previousSession) {
        //         $previousSession->delete(); // Delete the previous session
        //     }
        // }

        // // Store the new session ID
        // $user->session_id = session()->getId();
        // $user->save();

        // Log the user's login activity
        UserLog::create([
            'ip_address' => $request->ip(),
            'user_id' => $user->id,
            'user_agent' => $request->userAgent(),
        ]);

        return $this->success(new UserResource($user), 'User login successfully.');
    }

    // public function logout(Request $request)
    // {
    //     $user = Auth::user();

    //     if (! $user) {
    //         return $this->error('', 'User not authenticated.', 401);
    //     }

    //     // Clear session ID from the users table
    //     $user->session_id = null;
    //     $user->save();

    //     // Invalidate all tokens (for API authentication using Laravel Sanctum)
    //     if ($request->user()->currentAccessToken()) {
    //         $request->user()->currentAccessToken()->delete();
    //     }

    //     // Log the user out from the session (for web authentication)
    //     Auth::logout();

    //     return $this->success([], 'User logged out successfully.');
    // }

    // public function login(LoginRequest $request)
    // {
    //     $credentials = $request->only('user_name', 'password');

    //     $user = User::where('user_name', $request->user_name)->first();

    //     if (! Auth::attempt($credentials)) {
    //         return $this->error('', [
    //             'user_name' => 'Credentials do not match!',
    //         ], 422);
    //         // return $this->error('', 'Credentials do not match!', 401);
    //     }
    //     if (Auth::user()->status == 0) {
    //         return $this->error('', [
    //             'user_name' => 'Your account has benn banned. Please contact your agent.',
    //         ], 422);
    //     }

    //     $user = User::where('user_name', $request->user_name)->first();
    //     if (! $user->hasRole('Player')) {
    //         return $this->error('', [
    //             'user_name' => 'You are not a player. Please contact your agent.',
    //         ], 422);
    //     }

    //     UserLog::create([
    //         'ip_address' => $request->ip(),
    //         'user_id' => $user->id,
    //         'user_agent' => $request->userAgent(),
    //     ]);

    //     return $this->success(new UserResource($user), 'User login successfully.');
    // }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function getUser()
    {
        return $this->success(new PlayerResource(Auth::user()), 'User Success');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $player = Auth::user();
        if (Hash::check($request->current_password, $player->password)) {
            $player->update([
                'password' => $request->password,
                'status' => 1,
            ]);
        } else {
            return $this->error('', ['current_password' => 'Old Passowrd is incorrect'], 422);
        }

        return $this->success($player, 'Password has been changed successfully.');
    }

    public function profile(ProfileRequest $request)
    {

        $player = Auth::user();
        $player->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return $this->success(new PlayerResource($player), 'Update profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $this->handleImageDelete(Auth::user()->profile, 'player_profile');
        $player = Auth::user();
        $player->update([
            'profile' => $this->handleImageUpload($request->file('profile'), 'player_profile'),
        ]);

        return $this->success(new PlayerResource($player), 'Updated profile');
    }

    public function register(RegisterRequest $request)
    {
        if ($request->referral_code) {
            $agent = User::where('referral_code', $request->referral_code)->first();

            if (! $agent) {
                return $this->error('', 'Not Found Agent', 401);
            }

            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name,
                'user_name' => $this->generateRandomString(),
                'password' => Hash::make($request->password),
                'agent_id' => $agent->id,
                'type' => UserType::Player,
            ]);
        } else {
            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name,
                'user_name' => $this->generateRandomString(),
                'password' => Hash::make($request->password),
                'agent_id' => 4,
                'type' => UserType::Player,
            ]);
        }

        $user->roles()->sync(self::PLAYER_ROLE);

        UserLog::create([
            'ip_address' => $request->ip(),
            'register_ip' => $request->ip(),
            'user_id' => $user->id,
            'user_agent' => $request->userAgent(),
        ]);

        return $this->success(new RegisterResource($user), 'User register successfully.');
    }

    private function generateRandomString()
    {
        $latestPlayer = User::where('type', UserType::Player)->latest('id')->first();

        $nextNumber = $latestPlayer ? intval(substr($latestPlayer->user_name, 3)) + 1 : 1;

        return 'SPM'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
