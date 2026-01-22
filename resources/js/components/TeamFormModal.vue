<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import { useNotify } from '@/composables/useNotify'

const notify = useNotify()

const props = defineProps<{
  modalValue: boolean
  users: Array<{ id: number; name: string; }>
  team?: {
    id: number
    user_id: number
    name: string
    personal: boolean
  } | null
}>()

const emit = defineEmits(['update:modalValue'])

const form = useForm({
  user_id: null as number | null,
  name: '',
  personal: true, // Sempre true
})

watch(
  () => props.team,
  (team) => {
    console.log('Team recebido no modal:', props.team)
    if (team) {
      form.defaults({
        user_id: team.user.id,
        name: team.name,
        personal: true,
      })
      form.reset()
    } else {
      form.defaults({
        user_id: null,
        name: '',
        personal: true,
      })
      form.reset()
    }
  },
  {
    immediate: true,
    deep: true,
  }
)

const submit = () => {
  if (props.team) {
    form.put(`/teams/${props.team.id}`, {
      onSuccess: () => {
        notify.success('Team alterado com sucesso')
        emit('update:modalValue', false)
        form.reset()
      },
      onError: () => {
        notify.error('Erro ao alterar team')
      },
    })
  } else {
    form.post('/teams', {
      onSuccess: () => {
        notify.success('Team criado com sucesso')
        emit('update:modalValue', false)
        form.reset()
      },
      onError: () => {
        notify.error('Erro ao criar team')
      },
    })
  }
}
</script>

<template>
  <Modal
    :modal-value="modalValue"
    :title="team ? 'Editar Team' : 'Criar Team'"
    @update:modalValue="emit('update:modalValue', $event)"
    :class="'max-w-2xl'"
  >
    <form class="space-y-4" @submit.prevent="submit">
      <div>
        <label class="text-black/70 text-[11px]" for="user_id">Administrador do Team *</label>
        <select
          v-model="form.user_id"
          class="w-full text-black/70 rounded border px-3 py-2"
        >
          <option :value="null" disabled>
            Selecione o usuário
          </option>
          <option
            v-for="user in users"
            :key="user.id"
            :value="user.id"
          >
            {{ user.name }} ({{ user.email }})
          </option>
        </select>
        <p v-if="form.errors.user_id" class="text-sm text-red-500">
          {{ form.errors.user_id }}
        </p>
      </div>

      <div>
        <label class="text-black/70 text-[11px]" for="name">Nome do Team *</label>
        <input
          v-model="form.name"
          placeholder="Digite o nome do team"
          class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
        />
        <p v-if="form.errors.name" class="text-sm text-red-500">
          {{ form.errors.name }}
        </p>
      </div>

      <!-- Campo personal é hidden pois sempre é true -->
      <input type="hidden" v-model="form.personal" />
    </form>

    <template #footer>
      <button
        variant="outline"
        type="button"
        @click="emit('update:modalValue', false)"
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
        {{ team ? 'Atualizar' : 'Cadastrar' }}
      </button>
    </template>
  </Modal>
</template>
