<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;


// /api/users/posts
// /api/users/1/posts
Route::prefix('users')->group(function () {
    Route::get('', [UserController::class, 'get_all_users']);
    Route::get('posts', [UserController::class, 'get_users_posts']);
    Route::get('{user_id}/posts', [UserController::class, 'postsForUser']);
});

// /api/posts/posts
Route::prefix('posts')->group(function () {
    Route::get('', [PostController::class, 'get_all_posts']);
    Route::get('/user/{user_id}', [PostController::class, 'get_posts_for_user']);
    Route::get('{post_id}', [PostController::class, 'get_post_by_id']);
});
