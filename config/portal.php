<?php

return [
    // Internal systems should provision production accounts through an administrator.
    'registration_enabled' => env('PORTAL_REGISTRATION_ENABLED', env('APP_ENV', 'production') !== 'production'),
];
