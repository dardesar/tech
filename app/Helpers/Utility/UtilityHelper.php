<?php

const DB_REPEAT_AFTER_DEADLOCK = 5;

// Plain Utility Helper Functions

use Illuminate\Support\Str;

/*
 * Generate unique uuid
 */
if (!function_exists('generate_uuid')) {
    function generate_uuid()
    {
        return Str::uuid();
    }
}

/*
 * Generate unique uuid
 */
if (!function_exists('generate_string')) {
    function generate_string()
    {
        return mb_strtoupper(Str::random(15));
    }
}
