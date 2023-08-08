<template>
  <div class="flex items-center mr-4">
    <input :id="`inline-${name}-radio`"
           type="radio"
           :name="`inline-radio-group-${name}`"
           :checked="modelValue === value"
           :value="value"
           @change="onChange($event)"
           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
    <label :for="`inline-${id}-radio`" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{
        title
      }}</label>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'PermissionRadioField',
  emits: [
    'update:modelValue'
  ],
  props: {
    id: Number,
    value: Number,
    title: String,
    name: String,
    modelValue: Number
  },
  setup(props, {emit}) {
    const onChange = (e) => {
      emit('update:modelValue', parseInt(e.target.value))
    }

    return {
      onChange
    }
  }
})
</script>

<style scoped>

</style>
