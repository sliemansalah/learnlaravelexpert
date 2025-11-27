<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function get_all_posts(){
        $posts = Post::with('user')->get();
        $result = $posts->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'user' => $post->user->name,
            ];
        });
        return response()->json($result);
}
    // Post user
    public function get_posts_for_user($user_id, Request $request) {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $posts = Post::with('user')->where('user_id', $user_id)->get();

        $result = $posts->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'user' => $post->user->name,
                'user_email' => $post->user->email,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function get_post_by_id($post_id, Request $request) {
        $posts = Post::with('user')->where('id', $post_id)->get();
        $result = $posts->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'user' => $post->user->name,
                'user_email' => $post->user->email,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
