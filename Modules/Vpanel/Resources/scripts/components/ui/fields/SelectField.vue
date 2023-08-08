<template>
  <v-select
      v-model="selectedOption"
      :options="options"
      :searchable="true"
      :clearable="!field.required"
      :disabled="field.readonly"
      @update:modelValue="handleInput"
      class="py-2 bg-gray-200 text-gray-700 rounded leading-tight focus:outline-none focus:bg-white focus:border-purple-500 custom-fx"
  >
    <template #search="{attributes, events}">
      <input
          class="vs__search"
          :required="field.required && !currentValue"
          v-bind="attributes"
          v-on="events"
      />
    </template>
    <template v-slot:no-options>Записи не найдены!</template>
  </v-select>
</template>


<script>
import {defineComponent, ref, watch} from "vue";

export default defineComponent({
  name: 'SelectField',
  props: {
    field: Object,
    value: [String, Number]
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const selectedOption = ref(props.value ? props.field.options[props.value] : '')
    const options = ref([])

    const initOptions = (incOptions) => {
      const result = []
      for (const [key, value] of Object.entries(incOptions)) {
        result.push({
          id: key,
          label: value
        })
      }
      return result
    }

    options.value = initOptions(props.field.options)

    const handleInput = () => {
      emit('set-value', props.field.name, selectedOption.value?.id)
    }

    watch(() => props.field.options, (selectedOption) => {
      options.value = initOptions(selectedOption)
    }, {deep: true})

    watch(() => props.value, () => {
      selectedOption.value = props.field.options[props.value]
    })

    return {
      options,
      selectedOption,
      handleInput
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
