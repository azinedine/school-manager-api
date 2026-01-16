<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Base Controller
 *
 * Provides authorization capabilities to all controllers.
 * Uses Laravel's AuthorizesRequests trait for policy-based authorization.
 */
abstract class Controller
{
    use AuthorizesRequests;
}
