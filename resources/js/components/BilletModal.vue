<template>
  <Modal 
    title="BOLETO BANCÁRIO" 
    :modal-value="modalValueBillet" 
    @update:modalValue="$emit('update:modalValueBillet', $event)"
    :class="'max-w-4xl'"
  >
    <div class="space-y-4 text-gray-600">      
      <div class="bg-gray-50 p-4 rounded">
        <div class="w-full flex flex-col gap-5">
          <div class="flex w-full">
            <div class="w-[50%]">
              <span class="text-sm text-gray-600">CÓDIGO:</span>
              <p class="font-mono font-bold">{{ billet?.code }}</p>
            </div>
            <div class="w-[50%]">
              <span class="text-sm text-gray-600">VALOR:</span>
              <p class="font-bold text-lg">{{ formatMoney(billet?.bank_response?.amount) }}</p>
            </div>
          </div>
          <div class="flex w-full">
            <div class="w-[50%]">
              <span class="text-sm text-gray-600">VENCIMENTO:</span>
              <p class="font-bold text-lg">{{ formatDate(billet?.bank_response?.due_date) }}</p>
            </div>
            <div class="w-[50%]">
              <span class="text-sm text-gray-600">STATUS:</span>
              <p class="font-bold text-lg">{{ formatStatus(billet?.bank_response?.status) }}</p>
            </div>
          </div>
        </div>
      </div>
      <div v-if="billet?.bank_response?.barcode" class="border p-4 rounded">
        <span class="text-sm text-gray-600">Código de Barras:</span>
        <p class="font-mono text-sm break-all mt-2">{{ billet.bank_response.barcode }}</p>
        <button
          @click="copyBarcode"
          class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Copiar Código
        </button>
      </div>
      <div class="bg-yellow-50 p-4 rounded">
        <p class="text-sm text-gray-700">
          Use este código de barras para pagar em qualquer banco,
          aplicativo bancário ou lotérica.
        </p>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { useNotify } from '@/composables/useNotify'
import Modal from './Modal.vue'

const notify = useNotify()

const props = defineProps<{
  modalValueBillet: boolean
  billet: any
}>()

const emit = defineEmits(['update:modalValueBillet'])

const copyBarcode = () => {
  navigator.clipboard.writeText(props.billet?.bank_response?.barcode || '')
  notify.success('Código copiado!')
}

const formatMoney = (value: number | undefined) => {
  if (!value) return 'R$ 0,00'
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

const formatDate = (date: string | undefined) => {
  if (!date) return '-'
  try {
    return new Intl.DateTimeFormat('pt-BR').format(new Date(date))
  } catch {
    return date
  }
}

const formatStatus = (status: string | undefined) => {
  const statuses: Record<string, string> = {
    'pending': 'Pendente',
    'paid': 'Pago',
    'cancelled': 'Cancelado'
  }
  return statuses[status || ''] || status || '-'
}
</script>
