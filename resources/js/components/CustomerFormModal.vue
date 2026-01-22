<script setup lang="ts">
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { watch, computed } from 'vue'
import { useNotify } from '@/composables/useNotify'

const notify = useNotify()
const page = usePage()

const props = defineProps<{
  modalValue: boolean
  teams?: Array<{ id: number; name: string }>
  customer?: {
    id: number
    name: string
    email: string
    phone: string
    address: string
    document: string
    team_id?: number
  } | null
}>()

const emit = defineEmits(['update:modalValue'])

// ✅ Verifica se é GOD
const isGod = computed(() => page.props.isGod)
const isGodSelectTeamId = ref(false)

const form = useForm({
  team_id: null as number | null,
  name: '',
  email: '',
  phone: '',
  address: '',
  document: '',
})

watch(
  () => props.customer,
  (customer) => {
    if (customer) {
      form.defaults({
        team_id: customer.team_id ?? null,
        name: customer.name,
        email: customer.email,
        phone: customer.phone,
        address: customer.address,
        document: customer.document,
      })
      form.reset()
    } else {
      form.defaults({
        team_id: null,
        name: '',
        email: '',
        phone: '',
        address: '',
        document: '',
      })
      form.reset()
    }
  },
  { immediate: true, deep: true }
)

const submiting = () => {
  if(form.team_id){
    
    isGodSelectTeamId.value = false
    submit()
  }
  else{
    isGodSelectTeamId.value = true
  }
}

const submit = () => {
  const payload = { ...form.data() }

  if (!isGod.value) {
    delete payload.team_id
  }

  if (props.customer) {
    form.transform(() => payload).put(`customers/${props.customer.id}`, {
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
    form.transform(() => payload).post('customers', {
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

      <!-- ✅ SELECT DE TEAM - Apenas para GOD -->
      <div v-if="isGod" class="mb-4">
        <label class="text-black/70 text-[11px]" for="team_id">Team *</label>
        <select
          v-model="form.team_id"
          class="w-full text-black/70 rounded border px-3 py-2"
        >
          <option :value="null" disabled>Selecione o team</option>
          <option
            v-for="team in teams"
            :key="team.id"
            :value="team.id"
          >
            {{ team.name }}
          </option>
        </select>
        <p v-if="isGodSelectTeamId" class="text-sm text-red-500">
          Selecione um time
        </p>
      </div>

      <!-- Campos existentes -->
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
          <label class="text-black/70 text-[11px]" for="email">E-Mail *</label>
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
          <label class="text-black/70 text-[11px]" for="phone">Telefone</label>
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
          <label class="text-black/70 text-[11px]" for="document">CPF/CNPJ</label>
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
        <label class="text-black/70 text-[11px]" for="address">Endereço</label>
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
        type="button"
        @click="emit('update:modalValue', false)"
        class="bg-red-600 px-4 py-2 cursor-pointer rounded hover:bg-red-900 text-white"
      >
        Cancelar
      </button>
      <button
        type="submit"
        :disabled="form.processing"
        @click="isGod ? submiting() : submit()"
        class="bg-black/80 px-4 py-2 cursor-pointer rounded hover:bg-black/90 text-white disabled:opacity-50"
      >
        {{ customer ? 'Atualizar' : 'Cadastrar' }}
      </button>
    </template>
  </Modal>
</template>
