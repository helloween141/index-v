<template>
  <div>
    <input
        type="password"
        v-model="currentValue"
        @input="handleInput"
        placeholder="Новый пароль"
        class="mb-5 bg-gray-200 appearance-none border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
    />

    <input
        type="password"
        @input="handleInput"
        v-model="repeatValue"
        placeholder="Подтвердите пароль"
        class="bg-gray-200 appearance-none border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
    />

    <p v-show="showError" class="mt-2 text-sm text-red-600 dark:text-red-500">Пароли не совпадают!</p>
  </div>

</template>

<script>
import {defineComponent, ref, watch} from "vue";

export default defineComponent({
  name: 'PasswordField',
  props: {
    field: Object,
    value: String
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const currentValue = ref()
    const repeatValue = ref()
    const showError = ref(false)

    const handleInput = () => {
      if (currentValue.value === repeatValue.value) {
        showError.value = false
        emit('set-value', 'new_password', currentValue.value)
      } else {
        showError.value = true
        emit('set-value', 'new_password', '')
      }
    }

    watch(() => props.value, () => {
      currentValue.value = props.value
    })

    return {
      currentValue,
      repeatValue,
      showError,
      handleInput
    }
  }
})
</script>

<style scoped>

</style>
