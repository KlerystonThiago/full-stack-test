<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function index()
    {
        $team = auth()->user()->currentTeam;

        $members = $team->users()
            ->with('role')
            ->get()
            ->map(function ($member) use ($team) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'role' => $member->membership->role, // role na pivot
                    'is_owner' => $member->id === $team->user_id,
                ];
            });
            
        $availableUsers = User::whereNotIn('id', $team->users->pluck('id'))
            ->where('id', '!=', $team->user_id)
            ->select('id', 'name', 'email')
            ->get();

        return Inertia::render('Admin/TeamMembers/Index', [
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'owner_id' => $team->user_id,
            ],
            'members' => $members,
            'availableUsers' => $availableUsers,
            'roles' => $this->getAvailableRoles(),
        ]);
    }

    public function store(Request $request, AddTeamMember $addTeamMember)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:admin,member,viewer',
        ]);

        $team = auth()->user()->currentTeam;
        
        $addTeamMember->add(
            auth()->user(),
            $team,
            $validated['email'],
            $validated['role']
        );

        return redirect()
            ->back()
            ->with('success', 'Membro adicionado ao team com sucesso!');
    }

    public function update(Request $request, $userId)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,member,viewer',
        ]);

        $team = auth()->user()->currentTeam;
        
        $team->users()->updateExistingPivot($userId, [
            'role' => $validated['role'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Role atualizada com sucesso!');
    }

    public function destroy($userId, RemoveTeamMember $removeTeamMember)
    {
        $team = auth()->user()->currentTeam;
        $member = User::findOrFail($userId);
        
        $removeTeamMember->remove(auth()->user(), $team, $member);

        return redirect()
            ->back()
            ->with('success', 'Membro removido do team com sucesso!');
    }

    private function getAvailableRoles(): array
    {
        return [
            ['value' => 'admin', 'label' => 'Administrador'],
            ['value' => 'member', 'label' => 'Membro'],
            ['value' => 'viewer', 'label' => 'Visualizador'],
        ];
    }
}
