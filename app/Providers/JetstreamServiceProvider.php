<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrador', [
            'invoice:create', 'invoice:read', 'invoice:update', 'invoice:delete',
            'customer:create', 'customer:read', 'customer:update', 'customer:delete',
            'product:create', 'product:read', 'product:update', 'product:delete',
            'team:create', 'team:read', 'team:update', 'team:delete',
        ])->description('Administradores podem realizar qualquer ação no team.');

        Jetstream::role('member', 'Membro', [
            'invoice:create', 'invoice:read', 'invoice:update',
            'customer:create', 'customer:read', 'customer:update',
            'team:read',
            'product:read',
        ])->description('Membros podem criar e editar faturas e clientes.');

        Jetstream::role('viewer', 'Visualizador', [
            'invoice:read',
            'customer:read',
            'product:read',
            'team:red'
        ])->description('Visualizadores só podem ver dados.');
    }
}
