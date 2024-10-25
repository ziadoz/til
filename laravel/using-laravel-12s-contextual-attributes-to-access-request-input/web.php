<?php

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;
use Illuminate\Support\Facades\Route;

/**
 * Create a simple data object that accepts some parameters.
 *
 * Tell Laravel which fields in the POST data they should be pulled from via the contextual attribute.
 */
class MyUserData
{
    public function __construct(
        #[Post('name')] public string $name,
        #[Post('age')] public int $age,
        #[Post('hobbies')] public array $hobbies = [],
    ) {
    }
}

/**
 * Create a route that returns a form.
 */
Route::get('/user', function () {
    $token = csrf_token();

    return <<<HTML
    <form method="post">
        Name: <input name="name">
        Age: <input name="age">
        Hobbies: <input name="hobbies[]"><input name="hobbies[]">
        <input name="_token" type="hidden" value="$token">
        <button>Save</button>
    </form>
    HTML;
});

/**
 * Create a route that accepts the data object, and dump it and the request out for comparison.
 *
 * In a real application you'd have a form validator to ensure the data is valid.
 */
Route::post('/user', function (MyUserData $data) {
    dump(request()->post(), $data);
});
