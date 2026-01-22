<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasTeamPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();

        if (!$user || !$user->currentTeam) {
            abort(403, 'Você não pertence a nenhum team');
        }

        // Usa o método do Jetstream
        if (!$user->hasTeamPermission($user->currentTeam, $permission)) {
            abort(403, 'Você não tem permissão para realizar esta ação');
        }

        return $next($request);
    }
}
