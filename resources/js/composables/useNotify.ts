import { useToast } from 'vue-toast-notification'

export function useNotify() {
  const toast = useToast()

  const baseOptions = {
    position: 'top-right',
    duration: 4000,
    dismissible: true,
  }

  return {
    success(message: string) {
      toast.success(message, baseOptions)
    },

    error(message: string) {
      toast.error(message, baseOptions)
    },

    info(message: string) {
      toast.info(message, baseOptions)
    },

    warning(message: string) {
      toast.warning(message, baseOptions)
    },
  }
}
