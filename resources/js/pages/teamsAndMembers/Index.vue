<script setup lang="ts">
  import { ref } from 'vue';
  import AppLayout from '@/layouts/AppLayout.vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import { Button } from '@/components/ui/button';
  import Pagination from '@/components/Pagination.vue';
  import TeamFormModal from '@/components/TeamFormModal.vue';
  import Swal from 'sweetalert2';
  import {
      Table,
      TableBody,
      TableCell,
      TableHead,
      TableHeader,
      TableRow,
  } from '@/components/ui/table';
  import type { RouteParams } from 'vendor/tightenco/ziggy/src/js';
  import { usePermissions } from '@/composables/usePermissions'

  const { can } = usePermissions()
  
  interface User {
      id: number;
      name: string;
  }
  
  interface Team {
      id: number;
      user: User[];
      name: string;
      personal_team: Boolean;
  }

  const props = defineProps<{
    users: User
    teams: Team
  }>()

  const showCreateModal = ref(false)
  const selectedTeam = ref()

  const editTeam = (team: Team) => {
    console.log('edit do team: ', team)
    selectedTeam.value = team
    showCreateModal.value = true
  }

  const createTeam = () => {
    selectedTeam.value = null
    showCreateModal.value = true
  }

  const deleteTeam = async (team: { name: String; id: RouteParams<"teams.destroy"> | undefined; }) => {
    const result = await Swal.fire({
      title: 'Excluir Time?',
      text: `O time ${team.name} será removido`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Cancelar',
    })

    if (result.isConfirmed) {
      router.delete(route('teams.destroy', team.id), {
        onSuccess: () => {
          Swal.fire('Excluído!', 'Time removido com sucesso.', 'success')
        },
      })
    }
  }
</script>

<template>
  <Head title="Users" />

  <AppLayout>
    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Times</h1>
            <Button 
              v-if="can('team:create')"
              class="cursor-pointer" 
              @click="createTeam"
            >
                Cadastrar Time
            </Button>
        </div>

        <div class="rounded-md border">
          <Table>
              <TableHeader>
                  <TableRow>
                      <TableHead>Name</TableHead>
                      <TableHead>Admin</TableHead>
                      <TableHead>Time Pessoal</TableHead>
                      <TableHead class="text-center">Actions</TableHead>
                  </TableRow>
              </TableHeader>
              <TableBody>
                  <TableRow v-for="team in teams.data" :key="team.id">
                      <TableCell>{{ team.name }}</TableCell>
                      <TableCell>{{ team.user.name }}</TableCell>
                      <TableCell>{{ team.personal_team }}</TableCell>
                      <TableCell class="text-center">
                          <div class="flex justify-center gap-2">                              
                              <Button 
                                v-if="can('team:update')"
                                variant="outline" 
                                size="sm" 
                                @click="editTeam(team)" class="cursor-pointer"
                              >
                                  Edit
                              </Button>
                              <Button
                                v-if="team.can_delete"
                                variant="destructive" 
                                size="sm" 
                                @click="deleteTeam(team)" 
                                class="cursor-pointer"
                              >
                                  Delete
                              </Button>
                          </div>
                      </TableCell>
                  </TableRow>
              </TableBody>
          </Table>
        </div>
        <Pagination :links="teams.links"/>
    </div>
    <TeamFormModal
      :key="selectedTeam?.id ?? 'create'"
      @update:modalValue="showCreateModal = false"
      :modalValue="showCreateModal"
      :team="selectedTeam"
      :users="users"
    />

  </AppLayout>
</template>
