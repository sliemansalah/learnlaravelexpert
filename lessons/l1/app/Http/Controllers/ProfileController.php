<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // جلب جميع البروفايلات
    public function get_all_profiles(){
        $users = User::with('profile')->get();

        return response()->json([
            'success' => true,
            'data' => $users->map(function($user){
                return[
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'email_verified_at'=>$user->email_verified_at->format('Y-m-d H:i'),
                    'bio' => $user->profile?->bio ?? '',
                    'website' => $user->profile?->website ?? '',
                    'created_at' => $user->created_at->format('Y-m-d H:i'),
                ];
            })
        ]);
    }

    // جلب بروفايل مستخدم معين
    public function get_user_profile($id, Request $request){
        $user = User::with('profile')->find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at?->format('Y-m-d H:i'),
                'profile' => $user->profile ? [
                    'bio' => $user->profile->bio,
                    'website' => $user->profile?->website ?? '',
                    'created_at'=> $user->profile?->created_at->format('Y-m-d H:i'),
                ]: null,
            ]
        ]);
    }

    public function get_profile($id, Request $request){
        $profile= Profile::with('user')->find($id);
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'user_name'=> $profile->user->name,
                'user_email'=> $profile->user->email,
                'bio' => $profile->bio,
                'website' => $profile->website,
            ]
        ]);
    }
}
