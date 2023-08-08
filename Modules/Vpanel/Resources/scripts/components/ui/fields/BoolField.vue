<template>
  <label :for="field.name" class="inline-flex relative items-center cursor-pointer">
    <input
        type="checkbox"
        v-model="currentValue"
        :readonly="field.readonly"
        :required="field.required"
        @input="handleInput"
        :id="field.name"
        class="sr-only peer"
    />
    <div
        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 dark:border-gray-600 peer-checked:bg-blue-600"></div>
  </label>
</template>

<script>
import {defineComponent, ref, watch} from "vue";

export default defineComponent({
  name: 'BoolField',
  props: {
    field: Object,
    value: [Boolean]
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const currentValue = ref((props.value || props.field.default))

    const handleInput = () => {
      emit('set-value', props.field.name, (currentValue.value ? 0 : 1))
    }

    watch(() => props.value, () => {
      currentValue.value = props.value
    })

    return {
      currentValue,
      handleInput
    }
  }
})
</script>

<style scoped>

</style>
