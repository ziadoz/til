<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/user', function() {
    $csrf = csrf_field();

    return <<<FORM
    <form method="post" action="/user" enctype="multipart/form-data">
        <input type="file" name="avatar">
        {$csrf}
        <button>Save</button>
    </form>
    FORM;
}); 

Route::post('/user', function() {
    $user = User::query()->where('email', '=', 'foo@bar.com')->first();
    $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');

    return redirect()->route('user.show', $user);
});

Route::get('/user/{user}', function () {
    $user = User::query()->where('email', '=', 'foo@bar.com')->first();

    return <<<HTML
    <p>{$user->name}</p>
    <p><img src="{$user->getFirstMedia('avatar')->getUrl('thumb')}"></p>
    HTML;
})->name('user.show');