<script setup lang="ts">
  import { ref } from 'vue';
  import AppLayout from '@/layouts/AppLayout.vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import { Button } from '@/components/ui/button';
  import Pagination from '@/components/Pagination.vue';
  import InvoiceFormModal from '@/components/InvoiceFormModal.vue';
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
  import BilletModal from '@/components/BilletModal.vue';

  const { can } = usePermissions()

  interface Status {
    id: number;
    name: string;
  }

  interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
  }

  interface Invoice {
    id: number;
    code: string;
    amount: number;
    status: Status[];
    issue_date: string;
    due_date: string;
    payment_date?: string;
    customer: {
        id: number;
        name: string;
        email: string;
    };
  }

  interface Customer {
      id: number;
      name: string;
  }

  const props = defineProps<{
    invoices: any
    customers: Customer[]
    status: Status[]
    products: Product[]
  }>()

  const showCreateModal = ref(false)
  const showCreateModalBillet = ref(false)
  const selectedInvoice = ref()

  // ✅ Variável para guardar o boleto selecionado
  const selectedBillet = ref(null)

  const editInvoice = (invoice: Invoice) => {
    selectedInvoice.value = invoice
    showCreateModal.value = true
  }

  const createInvoice = () => {
    selectedInvoice.value = null
    showCreateModal.value = true
  }

  const deleteInvoice = async (invoice: { code: string; id: RouteParams<"admin.invoices.destroy"> | undefined; }) => {
    const result = await Swal.fire({
      title: 'Excluir fatura?',
      text: `A Fatura ${invoice.code} será removida`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Cancelar',
    })

    if (result.isConfirmed) {
      router.delete(route('invoices.destroy', invoice.id), {
        onSuccess: () => {
          Swal.fire('Excluído!', 'Fatura removida com sucesso.', 'success')
        },
      })
    }
  }

  // ✅ Função corrigida para guardar o boleto selecionado
  const showBillet = (invoice: any) => {
    selectedBillet.value = invoice.bankBillet
    showCreateModalBillet.value = true
  }
</script>

<template>
  <Head title="Invoices" />
  <AppLayout>
    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Invoices</h1>
            <Button
              v-if="can('customer:create')"
              class="cursor-pointer"
              @click="createInvoice"
            >
                Gerar Invoice
            </Button>
        </div>

        <div class="rounded-md border">
          <Table>
              <TableHeader>
                  <TableRow>
                      <TableHead class="pl-5">Código</TableHead>
                      <TableHead>Cliente</TableHead>
                      <TableHead>Valor</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead>Emissão</TableHead>
                      <TableHead>Vencimento</TableHead>
                      <TableHead class="text-center">Actions</TableHead>
                  </TableRow>
              </TableHeader>
              <TableBody>
                  <TableRow v-for="invoice in invoices.data" :key="invoice.id">
                      <TableCell class="pl-5">{{ invoice.code }}</TableCell>
                      <TableCell>{{ invoice.customer?.name || '-'}}</TableCell>
                      <TableCell>{{ invoice.amount }}</TableCell>
                      <TableCell>{{ invoice.status.name || '-' }}</TableCell>
                      <TableCell>{{ invoice.issue_date }}</TableCell>
                      <TableCell>{{ invoice.due_date }}</TableCell>
                      <TableCell class="flex justify-end">
                          <div class="flex justify-center gap-2">
                              <!-- ✅ Botão Ver Boleto -->
                              <Button
                                v-if="invoice.bankBillet"
                                @click="showBillet(invoice)"
                                class="cursor-pointer bg-blue-600"
                                variant="outline"
                                size="sm"
                              >
                                Ver Boleto
                              </Button>

                              <Button
                                v-if="can('invoice:update') && invoice.status.id != 4"
                                variant="outline"
                                size="sm"
                                @click="editInvoice(invoice)"
                                class="cursor-pointer"
                              >
                                  Edit
                              </Button>

                              <Button
                                  v-if="can('invoice:delete')"
                                  variant="destructive"
                                  size="sm"
                                  @click="deleteInvoice(invoice)"
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

        <Pagination :links="invoices.links"/>
    </div>

    <!-- ✅ Modal de Invoice (fora do loop) -->
    <InvoiceFormModal
      :key="selectedInvoice?.id ?? 'create'"
      @update:modalValue="showCreateModal = false"
      :modalValue="showCreateModal"
      :invoice="selectedInvoice"
      :customers="customers"
      :status="status"
      :products="products"
    />

    <!-- ✅ Modal de Boleto FORA do loop -->
    <BilletModal 
      :modalValueBillet="showCreateModalBillet" 
      @update:modalValueBillet="showCreateModalBillet = false"
      :billet="selectedBillet"
    />
  </AppLayout>
</template>
