<?php

return [

    'secret' => env('JWT_SECRET', env('APP_KEY')),

    /*
    | Minutes until token expires when "Remember me" is unchecked.
    */
    'ttl' => (int) env('JWT_TTL', 120),

    /*
    | Minutes until token expires when "Remember me" is checked (30 days).
    */
    'remember_ttl' => (int) env('JWT_REMEMBER_TTL', 43200),

    'algo' => env('JWT_ALGO', 'HS256'),

    'cookie' => env('JWT_COOKIE', 'auth_token'),

];
