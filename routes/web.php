<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MustBeGuest;
use App\Http\Middleware\MustBeLoggedIn;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Gate;

Route::get('/admins-only', function () {
    // if (Gate::allows('visitAdminPages')) {
    return 'This is admins only thank you for this';
    // }
    // return 'You cannot do this';
})->middleware("can:visitAdminPages");

// User Routes

Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');

Route::post('/register', [UserController::class, 'register'])->middleware(MustBeGuest::class);
Route::post('/login', [UserController::class, 'login'])->middleware(MustBeGuest::class);
Route::post('/logout', [UserController::class, 'logout'])->middleware(MustBeLoggedIn::class);


// Post Routes
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware(MustBeLoggedIn::class);
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware(MustBeLoggedIn::class);
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware("can:delete,post");
Route::put('/post/{post}', [PostController::class, 'update'])->middleware("can:delete,post");

Route::get('/post/{post}', [PostController::class, 'showSinglePostView'])->middleware(MustBeLoggedIn::class);
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware("can:delete,post");

// Profile Routes
Route::get('/profile/{user:username}', [UserController::class, 'showProfileView']);
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware(MustBeLoggedIn::class);
Route::post('/manage-avatar', [UserController::class, 'storeNewAvatar'])->middleware(MustBeLoggedIn::class);
// ->middleware(MustBeLoggedIn::class);
