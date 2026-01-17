<script setup lang="ts">
  import { Link } from '@inertiajs/vue3';
  import { Button } from '@/components/ui/button';

  const props = defineProps(['links']);
</script>

<template>
    <div class="flex justify-center gap-2">
      <Link
        v-for="link in links"
        :key="link.label"
        :href="link.url ?? ''"
        preserve-scroll        
      >
        <Button
          :disabled="!link.url"
          :variant="link.active ? 'default' : 'outline'"
          size="sm"
          v-html="link.label"
          class="cursor-pointer"
        />
      </Link>
    </div>
</template>
