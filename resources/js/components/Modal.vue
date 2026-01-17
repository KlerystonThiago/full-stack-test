<script setup lang="ts">
  import { computed } from 'vue'
  const props = defineProps<{
    modalValue: boolean
    title?: string
    class?: string
  }>()
  const emit = defineEmits(['update:modalValue'])
  const close = () => {
    emit('update:modalValue', false)
    console.log('emit - update:modalValue');
  }
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modalValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out delay-15"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div 
            class="w-full rounded bg-white border p-6 shadow-xl" 
            :class="class"
          >
            <div class="mb-4 flex items-center justify-between">
              <h2 class="card-text text-black/70 text-lg font-semibold">
                {{ title }}
              </h2>
              <button @click="close" class="bg-red-700 px-2 py-1 text-white rounded hover:bg-red-800 cursor-pointer transition-colors duration-200">
                ✕
              </button>
            </div>
            <slot />
            <div class="mt-6 flex justify-end gap-2">
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
