<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Log;
use Faker\Generator as Faker;

$factory->define(Log::class, function (Faker $faker) {
    return [
        'id' => 1,
        'title' => 'Titulo',
        "from" => "Auth",
        "url" => 'http://localhost/api/log',
        'extra' => '{"email":"test@asd.com","password":"*****"}',
        'ip' => '127.0.0.1',
        "level" => "debug"
    ];
});
