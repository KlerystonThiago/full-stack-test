<script setup lang="ts">
  import { ref } from 'vue';
  import AdminLayout from '@/layouts/AdminLayout.vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import { Button } from '@/components/ui/button';
  import Pagination from '@/components/Pagination.vue';
  import UserFormModal from '@/components/UserFormModal.vue';
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

  interface User {
      id: number;
      name: string;
      email: string;
      role_id: number;
  }

  interface Role {
      id: number;
      name: string;
  }

  const props = defineProps<{
    users: any
    roles: Role[]
  }>()

  const showCreateModal = ref(false)
  const selectedUser = ref()

  const editUser = (user: User) => {
    selectedUser.value = user
    showCreateModal.value = true
  }

  const createUser = () => {
    selectedUser.value = null
    showCreateModal.value = true
  }

  const deleteUser = async (user: { name: any; id: RouteParams<"admin.users.destroy"> | undefined; }) => {
    const result = await Swal.fire({
      title: 'Excluir usuário?',
      text: `O usuário ${user.name} será removido`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Cancelar',
    })

    if (result.isConfirmed) {
      router.delete(route('admin.users.destroy', user.id), {
        onSuccess: () => {
          Swal.fire('Excluído!', 'Usuário removido com sucesso.', 'success')
        },
      })
    }
  }
</script>

<template>
  <Head title="Users" />

  <AdminLayout>
    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Usuários</h1>
            <Button class="cursor-pointer" @click="createUser">
                Cadastrar Usuário
            </Button>
        </div>

        <div class="rounded-md border">
          <Table>
              <TableHeader>
                  <TableRow>
                      <TableHead>Name</TableHead>
                      <TableHead>Email</TableHead>
                      <TableHead>Tipo de Usuário</TableHead>
                      <TableHead class="text-center">Actions</TableHead>
                  </TableRow>
              </TableHeader>
              <TableBody>
                  <TableRow v-for="user in users.data" :key="user.id">
                      <TableCell>{{ user.name }}</TableCell>
                      <TableCell>{{ user.email }}</TableCell>
                      <TableCell>{{ user.role.name }}</TableCell>
                      <TableCell class="text-center">
                          <div class="flex justify-center gap-2">                              
                              <Button variant="outline" size="sm" @click="editUser(user)" class="cursor-pointer">
                                  Edit
                              </Button>
                              <Button variant="destructive" size="sm" @click="deleteUser(user)" class="cursor-pointer">
                                  Delete
                              </Button>
                          </div>
                      </TableCell>
                  </TableRow>
              </TableBody>
          </Table>
        </div>
        <Pagination :links="users.links"/>
    </div>
    <UserFormModal
      :key="selectedUser?.id ?? 'create'"
      :roles="roles"
      @update:modalValue="showCreateModal = false"
      :modalValue="showCreateModal"
      :user="selectedUser"
    />

  </AdminLayout>
</template>
