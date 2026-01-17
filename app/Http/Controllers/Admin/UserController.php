<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Actions\Jetstream\AddTeamMember;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        return inertia('admin/users/Index', [
            'users' => User::query()
                ->with('role:id,name')
                ->orderByDesc('id')
                ->paginate(5)
                ->onEachSide(2)
                ->withQueryString(),
                'roles' => Role::select('id', 'name')
            ->get()
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => $data['role_id'],
        ]);

        return redirect()->back()->with('success', 'Usuário criado com sucesso');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);

        return redirect()->back();
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->back()
            ->with('success', 'Usuário removido com sucesso.');
    }
}
