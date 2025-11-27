<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    public  function get_all_users()
    {
        $users = User::with('posts')->get();
        $result =  $users->map(function($user) {
            return [
                'id' => $user->id,
                'username' => $user->name,
                'email' => $user->email,
                'posts_count' => $user->posts->count(),
            ];
        });
        return response()->json($result);
    }
    // user posts
    public function get_users_posts() {
        $users = User::with('posts')->get();

        $result = $users->map(function($user) {
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'posts_count' => $user->posts->count(),
                'posts' => $user->posts->map(function($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'body' => $post->body,
//                        'date_created'=> $post->created_at->diffForHumans(),
                        'date'=> $post->created_at->format('Y-m-d'),
                        'time'=> $post->created_at->format('H:i'),
                        'created_at' => $post->created_at->format('Y-m-d H:i'),

                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
    public function postsForUser($user_id, Request $request) {
        // التحقق من وجود المستخدم
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود'
            ], 404);
        }

        // جلب منشورات المستخدم
        $posts = $user->posts;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'posts' => $posts
        ]);
    }
}
