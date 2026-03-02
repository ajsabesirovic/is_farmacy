<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowed = array_map(fn($r) => UserType::from($r), $roles);

        if (!in_array($user->tip, $allowed)) {
            abort(403, 'Nemate pristup ovoj stranici.');
        }

        return $next($request);
    }
}
