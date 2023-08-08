<template>
  <Datepicker
      v-model="currentValue"
      :format="'dd.MM.yyyy'"
      :textInput="true"
      :placeholder="placeholder"
      @update:modelValue="handleInput"
      locale="ru"
      autoApply
      inputClassName="appearance-none bg-gray-200 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
  />
</template>

<script>
import {defineComponent, ref, watch} from "vue";
import moment from "moment";

export default defineComponent({
  name: 'DateFilterField',
  props: {
    field: Object,
    placeholder: String,
    value: String,
    type: String
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const currentValue = ref(props.value)

    const handleInput = (val) => {
      val = val ? moment(val).format('YYYY-MM-DD') : ''
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
