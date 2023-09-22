<?php

namespace App\Http\Helpers;

/**
 * Get the currently authenticated user.
 *
 * @return \App\Models\User|null
 */
function currentUser(){
    return auth()->user();    
}