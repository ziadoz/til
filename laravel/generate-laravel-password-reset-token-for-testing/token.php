<?php
use Illuminate\Auth\Passwords\PasswordBroker;

$token = app()->get(PasswordBroker::class)->createToken(
    User::query()->where('email', '=', 'me@example.com')->first()
);