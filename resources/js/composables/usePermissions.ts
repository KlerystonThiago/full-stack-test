import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
  const page = usePage()

  const user = computed(() => page.props.auth.user)
  const permissions = computed(() => user.value?.permissions || [])
  const role = computed(() => user.value?.role)
  const isOwner = computed(() => user.value?.is_owner || false)
console.log('PERMISSIONS: ', permissions.value);
  const can = (permission: string): boolean => {
    if (isOwner.value) return true
    if (permissions.value.includes('*')) return true
    return permissions.value.includes(permission)
  }

  const cannot = (permission: string): boolean => {
    return !can(permission)
  }

  return {
    user,
    permissions,
    role,
    isOwner,
    can,
    cannot,
  }
}