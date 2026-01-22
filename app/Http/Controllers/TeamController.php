<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Team;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userTeams = $user->allTeams()->pluck('id');

        return inertia('teamsAndMembers/Index', [
            'teams' => Team::query()
                ->whereIn('id', $userTeams)
                ->orderByDesc('id')
                ->paginate(5)
                ->onEachSide(2)
                ->through(fn ($team) => [
                    'id' => $team->id,
                    'user' => $team->user,
                    'name' => $team->name,
                    'personal_team' => $team->personal_team,
                    'can_delete' => $team->user_id === $user->id,
                    'can_update' => $team->user_id === $user->id,
                ])
                ->withQueryString(),
            'users' => User::where('role_id', '!=', 1)->get()
        ]);
        /* return inertia('teamsAndMembers/Index', [
            'teams' => Team::query()
                ->orderByDesc('id')
                ->paginate(5)
                ->onEachSide(2)
                ->through(fn ($team) => [
                    'id' => $team->id,
                    'user' => User::find($team->user_id),
                    'name' => $team->name,
                    'personal_team' => $team->personal_team,                    
                ]) 
                ->withQueryString(),
                'users' => User::where('role_id', '!=', 1)->get()
        ]); */
    }
    
    public function store(StoreTeamRequest $request)
    {
        $validated = $request->validated();
        
        $validated['personal_team'] = true;

        $team = Team::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Team criado com sucesso!');
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $validated = $request->validated();
        
        $validated['personal'] = true;

        $team->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Team atualizado com sucesso!');
    }

    public function destroy(Team $team)
    {
        if ($team->personal) {
            return redirect()
                ->back()
                ->with('error', 'Não é possível deletar um team pessoal!');
        }

        $team->delete();

        return redirect()
            ->back()
            ->with('success', 'Team deletado com sucesso!');
    }
}
