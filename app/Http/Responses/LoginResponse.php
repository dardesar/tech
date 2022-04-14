<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @inheritDoc
     */
    public function toResponse($request)
    {
        if($request->get('dashboard', false)) {
            return Inertia::location(route('admin.dashboard'));
        }

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : Inertia::location(config('fortify.home'));
    }
}
