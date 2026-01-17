<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { useForm } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { watch } from 'vue'
import { useToast } from 'vue-toast-notification';
import { useNotify } from '@/composables/useNotify'

const notify = useNotify()
const $toast = useToast()

const props = defineProps<{
  modalValue: boolean
  roles: Array<{ id: number; name: string }>
  user?: {
    id: number
    name: string
    email: string
    role_id: number
  } | null
}>()

const emit = defineEmits(['update:modalValue'])

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: null as number | null,
})

watch(
  () => props.user,
  (user) => {
    console.log('User recebido no modal:', user)

    if (user) {
      form.defaults({
        name: user.name,
        email: user.email,
        role_id: user.role_id,
        password: '',
        password_confirmation: '',
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
  if (props.user) {
    form.put(`/a/admin/users/${props.user.id}`, {
      onSuccess: () => {
        notify.success('Usuário alterado com sucesso')
        emit('update:modalValue', false)
        form.reset()
      },
      onError: () => {
        notify.error('Erro ao alterar usuário')
      },
    })
  } else {
    form.post('/a/admin/users', {
      onSuccess: () => {
        notify.success('Usuário criado com sucesso')
        emit('update:modalValue', false)
        form.reset()
      },
      onError: () => {
        notify.error('Erro ao criar usuário')
      },
    })
  }
}

</script>

<template>
      <Modal
        :modal-value="modalValue"
        :title="user ? 'Editar usuário' : 'Criar usuário'"
        @update:modalValue="emit('update:modalValue', $event)"
        :class="'max-w-4xl'"
      >
        <form class="space-y-4" @submit.prevent="submit">
          <div>
            <label class="text-black/70 text-[11px]" for="name">Name *</label>
            <input
              v-model="form.name"
              placeholder="Digite o Nome"
              class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
            />
            <p v-if="form.errors.name" class="text-sm text-red-500">
              {{ form.errors.name }}
            </p>
          </div>

          <div>
            <label class="text-black/70 text-[11px]" for="name">E-Mail *</label>
            <input
              v-model="form.email"
              placeholder="Email"
              class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
            />
            <p v-if="form.errors.email" class="text-sm text-red-500">
              {{ form.errors.email }}
            </p>
          </div>

          <div class="flex gap-3">
            <div class="w-1/2">
              <label class="text-black/70 text-[11px]" for="name">Senha *</label>
              <input
                v-model="form.password"
                type="password"
                placeholder="Senha"
                class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
              />
              <p v-if="form.errors.password" class="text-sm text-red-500">
                {{ form.errors.password }}
              </p>
            </div>
            <div class="w-1/2">
              <label class="text-black/70 text-[11px]" for="name">Repita a Senha *</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                placeholder="Repita a Senha"
                class="w-full text-black/70 rounded border px-3 py-2 placeholder-gray-300"
              />
              <p v-if="form.errors.password" class="text-sm text-red-500">
                {{ form.errors.password_confirmation }}
              </p>
            </div>
          </div>

          <div>
            <label class="text-black/70 text-[11px]" for="name">Tipo de Usuário *</label>
            <select
              v-model="form.role_id"
              class="w-full text-black/70 rounded border px-3 py-2"
            >
              <option :value="null" disabled>
                Selecione a role
              </option>

              <option
                v-for="role in roles"
                :key="role.id"
                :value="role.id"
              >
                {{ role.name }}
              </option>
            </select>
            <p v-if="form.errors.role_id" class="text-sm text-red-500">
              {{ form.errors.role_id }}
            </p>
          </div>
        </form>

        <!-- SLOT FOOTER (FORA DO FORM) -->
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
            {{ user ? 'Atualizar' : 'Cadastrar' }}
          </button>
        </template>
      </Modal>
</template>
