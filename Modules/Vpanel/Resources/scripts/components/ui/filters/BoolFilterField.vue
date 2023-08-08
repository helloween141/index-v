<template>
  <div class="flex flex-col">
    <span class="dark:text-white">{{ field.title }}</span>
    <div class="mt-2">
      <label :for="field.name" class="inline-flex relative items-center cursor-pointer">
        <input
            type="checkbox"
            v-model="currentValue"
            @input="onChange"
            :id="field.name"
            class="sr-only peer"
        />
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 dark:border-gray-600 peer-checked:bg-blue-600"></div>
      </label>
    </div>
  </div>
</template>

<script>
import {defineComponent, ref, watch} from "vue";

export default defineComponent({
  name: 'BoolFilterField',
  emits: ['set-filter'],
  props: {
    value: Boolean,
    field: Object,
  },
  setup(props, {emit}) {
    const currentValue = ref(props.value ? true : false)

    const onChange = () => {
      emit('set-filter', {[props.field.name]: (currentValue.value ? 0 : 1)}, props.field.name)
    }

    watch(() => props.value, (current, previous) => {
      if (!props.value) {
        currentValue.value = false
      }
    })

    return {
      currentValue,
      onChange
    }
  }
})
</script>

<style scoped>

</style>
