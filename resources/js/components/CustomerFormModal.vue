<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import { useToast } from 'vue-toast-notification';
import { useNotify } from '@/composables/useNotify'

const notify = useNotify()
const $toast = useToast()

const props = defineProps<{
  modalValue: boolean
  customer?: {
    id: number,
    name: string,
    email: string,
    phone: string,
    address: string,
    document: string,
  } | null
}>()

const emit = defineEmits(['update:modalValue'])

const form = useForm({
  id: '',
  name: '',
  email: '',
  phone: '',
  address: '',
  document: '',
})

watch(
  () => props.customer,
  (customer) => {
    console.log('Customer recebido no modal:', customer)

    if (customer) {
      form.defaults({
        id: customer.id,
        name: customer.name,
        email: customer.email,
        phone: customer.phone,
        address: customer.address,
        document: customer.document,
      })

      form.reset()
    } else {
      form.reset()
    }
  },
  {
    immediate: true,
    deep: true,
    flush: 'post',
  }
)

const submit = () => {
  console.log(props.customer?.id)
  if (props.customer) {
    form.put(`/a/admin/customers/${props.customer.id}`, {
      onSuccess: () => {
        notify.success('Cliente alterado com sucesso')
        emit('update:modalValue', false)
        form.reset()
      },
      onError: () => {
        notify.error('Erro ao alterar cliente')
      },
    })
  } else {
    form.post('/a/admin/customers', {
      onSuccess: () => {
        notify.success('Cliente criado com sucesso')
        emit('update:modalValue', false)
        form.reset()
      },
      onError: () => {
        notify.error('Erro ao criar cliente')
      },
    })
  }
}

</script>

<template>
      <Modal
        :modal-value="modalValue"
        :title="customer ? 'Editar cliente' : 'Cadastrar cliente'"
        @update:modalValue="emit('update:modalValue', $event)"
        :class="'max-w-5xl'"
      >
        <form class="space-y-4" @submit.prevent="submit">
          <div class="flex gap-3">
            <div class="w-1/2">
              <label class="text-black/70 text-[11px]" for="name">Nome *</label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Digite o nome"
                class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
              />
              <p v-if="form.errors.name" class="text-sm text-red-500">
                {{ form.errors.name }}
              </p>
            </div>
            <div class="w-1/2">
              <label class="text-black/70 text-[11px]" for="name">E-Mail *</label>
              <input
                v-model="form.email"
                type="text"
                placeholder="Digite o email"
                class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
              />
              <p v-if="form.errors.email" class="text-sm text-red-500">
                {{ form.errors.email }}
              </p>
            </div>
          </div>

          <div class="flex gap-3">
            <div class="w-1/2">
              <label class="text-black/70 text-[11px]" for="name">Telefone</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="Digite o telefone"
                class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
              />
              <p v-if="form.errors.phone" class="text-sm text-red-500">
                {{ form.errors.phone }}
              </p>
            </div>
            <div class="w-1/2">
              <label class="text-black/70 text-[11px]" for="name">CPF/CNPJ</label>
              <input
                v-model="form.document"
                type="text"
                placeholder="Digite o documento"
                class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
              />
              <p v-if="form.errors.document" class="text-sm text-red-500">
                {{ form.errors.document }}
              </p>
            </div>
          </div>

          <div>
            <label class="text-black/70 text-[11px]" for="name">Endereço</label>
            <textarea
              v-model="form.address"
              placeholder="Digite o endereço"
              class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
            >{{ form?.address }}</textarea>
            <p v-if="form.errors.address" class="text-sm text-red-500">
              {{ form.errors.address }}
            </p>
          </div>          
        </form>        
        <template #footer>
          <button
            variant="outline"
            type="button"
            @click="emit('update:modalValue', false)"
            class="bg-red-600 px-4 py-2 cursor-pointer rounded hover:bg-red-900"
          >
            Cancelar
          </button>

          <button
            type="submit"
            :disabled="form.processing"
            @click="submit"
            class="bg-black/80 px-4 py-2 cursor-pointer rounded hover:bg-black/90"
          >
            {{ customer ? 'Atualizar' : 'Cadastrar' }}
          </button>
        </template>
      </Modal>
</template>
