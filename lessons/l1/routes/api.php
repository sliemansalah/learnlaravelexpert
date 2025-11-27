<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;


// /api/users/posts
// /api/users/1/posts
Route::prefix('users')->group(function () {
    Route::get('', [UserController::class, 'get_all_users']);
    Route::get('posts', [UserController::class, 'get_users_posts']);
    Route::get('{user_id}/posts', [UserController::class, 'postsForUser']);
    Route::get('/{user_id}/get_profile', [UserController::class, 'get_profile']);
});

// /api/posts/posts
Route::prefix('posts')->group(function () {
    Route::get('', [PostController::class, 'get_all_posts']);
    Route::get('/user/{user_id}', [PostController::class, 'get_posts_for_user']);
    Route::get('{post_id}', [PostController::class, 'get_post_by_id']);
});

Route::prefix('profiles')->group(function () {
    Route::get('', [ProfileController::class, 'get_all_profiles']);
    Route::get('/user/{id}', [ProfileController::class, 'get_user_profile']);
    Route::get('{id}', [ProfileController::class, 'get_profile']);
});

