<script setup lang="ts">
  import { ref } from 'vue';
  import AdminLayout from '@/layouts/AdminLayout.vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import { Button } from '@/components/ui/button';
  import Pagination from '@/components/Pagination.vue';
  import CustomerFormModal from '@/components/CustomerFormModal.vue';
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

  interface Customer {
    id: number,
    name: string,
    email: string,
    phone: string,
    address: string,
    document: string,
  }

  const props = defineProps<{
    customers: any
    teams: any
  }>()

  const showCreateModal = ref(false)
  const selectedCustomer = ref()

  const editCustomer = (customer: Customer) => {
    selectedCustomer.value = customer
    showCreateModal.value = true
  }

  const createCustomer = () => {
    selectedCustomer.value = null
    showCreateModal.value = true
  }

  const deleteCustomer = async (customer: { name: any; id: RouteParams<"admin.customers.destroy"> | undefined; }) => {
    const result = await Swal.fire({
      title: 'Excluir cliente?',
      text: `O cliente ${customer.name} será removido`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Cancelar',
    })

    if (result.isConfirmed) {
      router.delete(route('admin.customers.destroy', customer.id), {
        onSuccess: () => {
          Swal.fire('Excluído!', 'Cliente removido com sucesso.', 'success')
        },
      })
    }
  }
</script>

<template>
  <Head title="Clientes" />

  <AdminLayout>
    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Clientes</h1>
            <Button class="cursor-pointer" @click="createCustomer">
                Cadastrar Cliente
            </Button>
        </div>

        <div class="rounded-md border">
          <Table>
              <TableHeader>
                  <TableRow>
                      <TableHead class="pl-5">Nome</TableHead>
                      <TableHead>E-Mail</TableHead>
                      <TableHead>Telefone</TableHead>
                      <TableHead>Documento</TableHead>
                      <TableHead>Faturas</TableHead>
                      <TableHead>Criado</TableHead>
                      <TableHead class="text-center">Actions</TableHead>
                  </TableRow>
              </TableHeader>
              <TableBody>
                  <TableRow v-for="customer in customers.data" :key="customer.id">
                      <TableCell class="pl-5">{{ customer.name }}</TableCell>
                      <TableCell>{{ customer.email }}</TableCell>
                      <TableCell>{{ customer.phone || '-' }}</TableCell>
                      <TableCell>{{ customer.document || '-' }}</TableCell>
                      <TableCell>{{ customer.invoices_count }}</TableCell>
                      <TableCell>{{ customer.created_at }}</TableCell>
                      <TableCell class="text-center">
                          <div class="flex justify-center gap-2">
                              <Button variant="outline" size="sm" @click="editCustomer(customer)" class="cursor-pointer">
                                  Edit
                              </Button>
                              <Button
                                  variant="destructive"
                                  size="sm"
                                  @click="deleteCustomer(customer)"
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
        <Pagination :links="customers.links"/>
    </div>
    <CustomerFormModal
      :key="selectedCustomer?.id ?? 'create'"
      @update:modalValue="showCreateModal = false"
      :modalValue="showCreateModal"
      :customer="selectedCustomer"
      :teams="teams"
    />

  </AdminLayout>
</template>
