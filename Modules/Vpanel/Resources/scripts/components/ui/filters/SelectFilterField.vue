<template>
  <div>
    <span class="dark:text-white mb-3">{{ field.title }}</span>
    <v-select
        v-model="currentValue"
        :options="options"
        :searchable="true"
        :clearable="true"
        @update:modelValue="onInput"
        class="py-2 bg-gray-200 text-gray-700 rounded leading-tight focus:outline-none focus:bg-white focus:border-purple-500 font-medium custom-fx"
    >
      <template v-slot:no-options>Записи не найдены!</template>
    </v-select>
  </div>
</template>

<script>
import {defineComponent, ref, watch} from "vue";

export default defineComponent({
  name: 'SelectFilterField',
  emits: ['set-filter'],
  props: {
    field: Object,
    value: String,
  },
  setup(props, {emit}) {
    let currentValue = ref(props.field.options[props.value?.id || props.value])
    const options = []

    for (const [key, value] of Object.entries(props.field.options)) {
      options.push({
        id: key,
        label: value
      })
    }

    watch(() => props.value, (current, previous) => {
      if (!current) {
        currentValue.value = ''
      }
    })

    const onInput = (val) => {
      emit('set-filter', {[props.field.name]: val}, props.field.name)
    }

    return {
      options,
      currentValue,
      onInput
    }
  }
})
</script>

<style scoped>
.custom-fx {
  font-size: 1rem;
  padding: 0.17rem 0.17rem;
}
</style>
