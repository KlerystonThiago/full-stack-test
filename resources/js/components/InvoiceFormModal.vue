<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { useForm } from '@inertiajs/vue3'
import { watch, ref, computed } from 'vue' // Adicionei ref e computed
import { useToast } from 'vue-toast-notification';
import { useNotify } from '@/composables/useNotify'

const notify = useNotify()
const $toast = useToast()

const props = defineProps<{
  modalValue: boolean
  customers: Array<{ id: number; name: string }>
  status: Array<{ id: number; name: string }>
  products: Array<{ id: number; name: string; description: string; price: string }>
  invoice_items?: {
  }
  invoice?: {
    id: number;
    customer_id: number;
    code: string;
    amount: number;
    status: Array<{id: number, name: string}>;
    issue_date: string;
    due_date: string;
    payment_date?: string;
    customer: {
        id: number;
        name: string;
        email: string;
    };
  } | null
}>()

const emit = defineEmits(['update:modalValue'])

const form = useForm({
  id: '',
  code: '',
  amount: '',
  status_id: '',
  issue_date: '',
  due_date: '',
  payment_date: '',
  customer_id: '',
  products: [] as Array<{ product_id: number; name: string; price: number; quantity: number }>, // Array de produtos
})

const selectedProductId = ref<number | ''>('')
  
const addedProducts = ref<Array<{
  product_id: number;
  name: string;
  description: string;
  price: number;
  quantity: number;
}>>([])

const totalAmount = computed(() => {
  return addedProducts.value.reduce((sum, product) => {
    return sum + (product.price * product.quantity)
  }, 0)
})

watch(totalAmount, (newTotal) => {
  form.amount = newTotal.toFixed(2)
})

const addProduct = () => {
  if (!selectedProductId.value) {
    notify.error('Selecione um produto')
    return
  }
  
  const product = props.products.find(p => p.id === selectedProductId.value)

  if (!product) {
    notify.error('Produto não encontrado')
    return
  }
  
  const alreadyAdded = addedProducts.value.find(p => p.product_id === product.id)

  if (alreadyAdded) {
    notify.error('Produto já adicionado à lista')
    return
  }
  
  addedProducts.value.push({
    product_id: product.id,
    name: product.name,
    description: product.description,
    price: parseFloat(product.price),
    quantity: 1,
  })
  
  selectedProductId.value = ''

  notify.success('Produto adicionado')
}

const removeProduct = (productId: number) => {
  const index = addedProducts.value.findIndex(p => p.product_id === productId)

  if (index !== -1) {
    addedProducts.value.splice(index, 1)
    notify.success('Produto removido')
  }
}

const updateQuantity = (productId: number, newQuantity: number) => {
  const product = addedProducts.value.find(p => p.product_id === productId)

  if (product && newQuantity > 0) {
    product.quantity = newQuantity
  }
}

const convertDateFormat = (date) => {
  const [day, month, year] = date.split('-')
  return `${year}-${month}-${day}`
}

watch(
  () => props.invoice,
  (invoice) => {
    if (invoice) {
      form.defaults({
        id: invoice.id,
        code: invoice.code,
        amount: invoice.amount,
        status_id: invoice.status.id,
        issue_date: invoice.issue_date,
        due_date: convertDateFormat(invoice.due_date),
        payment_date: invoice.payment_date || '',
        customer_id: invoice.customer_id,
        products: [],
      })
      form.reset()
      
      if (invoice.products && invoice.products.length > 0) {
        addedProducts.value = invoice.products.map(product => ({
          product_id: product.id,
          name: product.name,
          description: product.description,
          price: parseFloat(product.pivot.unit_price),
          quantity: product.pivot.quantity,
        }))
        console.log('entrou, lá ele...')
      } else {
        addedProducts.value = []
      }
    } else {
      form.defaults({
        id: '',
        code: '',
        amount: '',
        status_id: '',
        issue_date: '',
        due_date: '',
        payment_date: '',
        customer_id: '',
        products: [],
      })
      form.reset()
      addedProducts.value = []
    }
  },
  {
    immediate: true,
    deep: true,
  }
)

const submit = () => {
  if (addedProducts.value.length === 0) {
    notify.error('Adicione pelo menos um produto à fatura')
    return
  }
  
  form.products = addedProducts.value.map(product => ({
    product_id: product.product_id,
    name: product.name,
    price: product.price,
    quantity: product.quantity,
  }))

  if (props.invoice) {
    form.put(`/a/admin/invoices/${props.invoice.id}`, {
      onSuccess: () => {
        notify.success('Fatura alterada com sucesso')
        emit('update:modalValue', false)
        form.reset()
        addedProducts.value = []
      },
      onError: () => {
        notify.error('Erro ao alterar a fatura')
      },
    })
  } else {
    form.post('/a/admin/invoices', {
      onSuccess: () => {
        notify.success('Fatura criada com sucesso')
        emit('update:modalValue', false)
        //form.reset()
        addedProducts.value = []
      },
      onError: () => {
        notify.error('Erro ao criar fatura')
      },
    })
  }
}
</script>

<template>
    <Modal
      :modal-value="modalValue"
      :title="invoice ? 'Editar fatura' : 'Cadastrar fatura'"
      @update:modalValue="emit('update:modalValue', $event)"
      :class="'max-w-6xl'"
    >
      <form class="space-y-4" @submit.prevent="submit">
        <div class="flex gap-3">
          <div class="w-1/2">
            <label class="text-black/70 text-[11px]" for="name">Cliente *</label>
            <select
              v-model="form.customer_id"
              class="w-full text-black/70 rounded border px-3 py-2"
            >
              <option value="" disabled>
                Selecione o cliente
              </option>
              <option
                v-for="customer in customers"
                :key="customer.id"
                :value="customer.id"
              >
                {{ customer.name }}
              </option>
            </select>
            <p v-if="form.errors.customer_id" class="text-sm text-red-500">
              {{ form.errors.customer_id }}
            </p>
          </div>            
          <div class="w-1/2">
            <label class="text-black/70 text-[11px]" for="name">Status *</label>
            <select
              v-model="form.status_id"
              class="w-full text-black/70 rounded border px-3 py-2"
            >
              <option value="" disabled>
                Selecione o status
              </option>
              <option
                v-for="item in status"
                :key="item.id"
                :value="item.id"
              >
                {{ item.name }}
              </option>
            </select>
            <p v-if="form.errors.status_id" class="text-sm text-red-500">
              {{ form.errors.status_id }}
            </p>
          </div>            
        </div>
        <div class="flex gap-3">
          <div class="w-1/2">
            <label class="text-black/70 text-[11px]" for="due_date">Vencimento</label>
            <input v-model="form.due_date" class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300" id="due_date" type="date" required />
            <p v-if="form.errors.due_date" class="text-sm text-red-500">
              {{ form.errors.due_date }}
            </p>
          </div>  
          <div class="w-1/2"></div>          
        </div>

        <h2 class="card-text text-black/70 text-lg font-semibold mt-6">
          Items
        </h2>
        
        <div class="flex gap-3">
          <div class="w-9/12">
            <label class="text-black/70 text-[11px]" for="name">Produto *</label>
            <select
              v-model="selectedProductId"
              class="w-full text-black/70 rounded border px-3 py-2"
            >
              <option value="" selected disabled>
                Selecione o produto
              </option>
              <option
                v-for="product in products"
                :key="product.id"
                :value="product.id"
              >
                {{ product.name }} - R$ {{ parseFloat(product.price).toFixed(2) }}
              </option>
            </select>
          </div>
          <div class="w-3/12">
            <button
              type="button"
              @click="addProduct"
              class="w-full mt-[24px] bg-blue-700 text-white px-4 py-2 cursor-pointer rounded hover:bg-blue-800"
            >
              Adicionar
            </button>
          </div>
        </div>
        
        <div v-if="addedProducts.length > 0" class="mt-4 border rounded-lg overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-black/70">Produto</th>
                <th class="px-4 py-2 text-center text-sm font-semibold text-black/70">Qtd</th>
                <th class="px-4 py-2 text-right text-sm font-semibold text-black/70">Preço Unit.</th>
                <th class="px-4 py-2 text-right text-sm font-semibold text-black/70">Subtotal</th>
                <th class="px-4 py-2 text-center text-sm font-semibold text-black/70">Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="product in addedProducts" 
                :key="product.product_id"
                class="border-t hover:bg-gray-50"
              >
                <td class="px-4 py-3 text-sm text-black/70">
                  <div class="font-medium">{{ product.name }}</div>
                </td>
                <td class="px-4 py-3 text-center">
                  <input 
                    type="number" 
                    min="1"
                    :value="product.quantity"
                    @input="updateQuantity(product.product_id, parseInt(($event.target as HTMLInputElement).value))"
                    class="w-16 text-center border rounded px-2 py-1 text-sm text-black/70"
                  />
                </td>
                <td class="px-4 py-3 text-right text-sm text-black/70">
                  R$ {{ product.price.toFixed(2) }}
                </td>
                <td class="px-4 py-3 text-right text-sm font-medium text-black/70">
                  R$ {{ (product.price * product.quantity).toFixed(2) }}
                </td>
                <td class="px-4 py-3 text-center">
                  <button
                    type="button"
                    @click="removeProduct(product.product_id)"
                    class="text-red-600 hover:text-red-800 font-medium text-sm"
                  >
                    Remover
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div class="bg-gray-50 px-4 py-3 border-t flex justify-between items-center">
            <span class="text-sm font-semibold text-black/70">Total da Fatura:</span>
            <span class="text-lg font-bold text-black/90">
              R$ {{ totalAmount.toFixed(2) }}
            </span>
          </div>
        </div>
        
        <div v-else class="mt-4 p-4 bg-gray-50 rounded border border-dashed text-center">
          <p class="text-sm text-gray-500">Nenhum produto adicionado ainda</p>
        </div>
      </form>        

      <template #footer>
        <button
          variant="outline"
          type="button"
          @click="emit('update:modalValue', false), form.reset()"
          class="bg-red-600 px-4 py-2 cursor-pointer rounded hover:bg-red-900 text-white"
        >
          Cancelar
        </button>
        <button
          type="submit"
          :disabled="form.processing"
          @click="submit"
          class="bg-black/80 px-4 py-2 cursor-pointer rounded hover:bg-black/90 text-white disabled:opacity-50"
        >
          {{ invoice ? 'Atualizar' : 'Cadastrar' }}
        </button>
      </template>
    </Modal>
</template>
