<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard password
    |--------------------------------------------------------------------------
    |
    | The admin dashboard uses a single shared password (no users table, no
    | database). Set DASHBOARD_PASSWORD in the environment — paste it into the
    | Laravel Cloud "Environment" settings in production. If it is empty, login
    | is disabled (fails closed).
    |
    */

    'password' => env('DASHBOARD_PASSWORD'),

];
