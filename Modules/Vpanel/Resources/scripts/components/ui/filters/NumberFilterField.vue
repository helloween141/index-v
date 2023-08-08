<template>
  <input type="number"
         v-model="currentValue"
         @input="handleInput($event.target.value)"
         :placeholder="placeholder"
         class="bg-gray-200 appearance-none border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
  />
</template>

<script>
import {defineComponent, ref, watch} from "vue";

export default defineComponent({
  name: 'NumberFilterField',
  emits: ['set-filter'],
  props: {
    field: Object,
    placeholder: String,
    value: String,
    type: String
  },
  setup(props, {emit}) {
    const currentValue = ref(props.value)

    const handleInput = (val) => {
      emit('set-value', val, props.type)
    }

    watch(() => props.value, (current, previous) => {
      if (!current) {
        currentValue.value = ''
      }
    })

    return {
      handleInput,
      currentValue
    }
  }
})
</script>

<style scoped>

</style>
