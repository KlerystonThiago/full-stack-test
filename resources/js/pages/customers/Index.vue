<script setup lang="ts">
  import { ref } from 'vue';
  import AppLayout from '@/layouts/AppLayout.vue';
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
  import { usePermissions } from '@/composables/usePermissions'

  const { can } = usePermissions()

  interface Customer {
    id: number,
    name: string,
    email: string,
    phone: string,
    address: string,
    document: string,
    team_id: number,
  }

  const props = defineProps<{
    customers: any
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

  const deleteCustomer = async (customer: { name: any; id: RouteParams<"customers.destroy"> | undefined; }) => {
    const result = await Swal.fire({
      title: 'Excluir cliente?',
      text: `O cliente ${customer.name} será removido`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Cancelar',
    })

    if (result.isConfirmed) {
      router.delete(route('customers.destroy', customer.id), {
        onSuccess: () => {
          Swal.fire('Excluído!', 'Cliente removido com sucesso.', 'success')
        },
      })
    }
  }
</script>

<template>
  <Head title="Clientes" />

  <AppLayout>
    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Clientes</h1>
            <Button 
              v-if="can('customer:create')"
              class="cursor-pointer" 
              @click="createCustomer"
            >
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
                              <Button
                                v-if="can('customer:update')"
                                variant="outline" 
                                size="sm" 
                                @click="editCustomer(customer)" 
                                class="cursor-pointer"
                              >
                                  Edit
                              </Button>
                              <Button
                                v-if="can('customers:delete')"
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
    />

  </AppLayout>
</template>
















































































































<!-- <script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Customer {
    id: number;
    name: string;
    email: string;
    phone?: string;
    address?: string;
    document?: string;
    invoices_count: number;
    created_at: string;
}

interface Props {
    customers: {
        data: Customer[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Customers',
        href: '/customers',
    },
];

const deleteCustomer = (customer: Customer) => {
    if (customer.invoices_count > 0) {
        alert('Cannot delete customer with existing invoices.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${customer.name}?`)) {
        router.delete(`/customers/${customer.id}`);
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold">Customers</h1>
                <Link href="/customers/create">
                    <Button>Create Customer</Button>
                </Link>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Phone</TableHead>
                            <TableHead>Document</TableHead>
                            <TableHead>Invoices</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="customer in customers.data" :key="customer.id">
                            <TableCell class="font-medium">{{ customer.name }}</TableCell>
                            <TableCell>{{ customer.email }}</TableCell>
                            <TableCell>{{ customer.phone || '-' }}</TableCell>
                            <TableCell>{{ customer.document || '-' }}</TableCell>
                            <TableCell>{{ customer.invoices_count }}</TableCell>
                            <TableCell>{{ formatDate(customer.created_at) }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/customers/${customer.id}`">
                                        <Button variant="outline" size="sm">View</Button>
                                    </Link>
                                    <Link :href="`/customers/${customer.id}/edit`">
                                        <Button variant="outline" size="sm">Edit</Button>
                                    </Link>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteCustomer(customer)"
                                        :disabled="customer.invoices_count > 0"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div v-if="customers.last_page > 1" class="flex justify-center gap-2">
                <Link
                    v-for="page in customers.last_page"
                    :key="page"
                    :href="`/customers?page=${page}`"
                >
                    <Button
                        :variant="page === customers.current_page ? 'default' : 'outline'"
                        size="sm"
                    >
                        {{ page }}
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
 -->