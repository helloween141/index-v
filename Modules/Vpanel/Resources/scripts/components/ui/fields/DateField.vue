<template>
  <Datepicker
      v-model="currentValue"
      :readonly="field.readonly"
      :required="field.required"
      :format="field.withTime ? 'dd.MM.yyyy HH:mm' : 'dd.MM.yyyy'"
      :clearable="false"
      :textInput="true"
      @update:modelValue="handleInput"
      locale="ru"
      autoApply
      inputClassName="appearance-none bg-gray-200 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
  />
</template>

<script lang="ts">
import {defineComponent, ref, watch} from "vue";
import moment from "moment";

export default defineComponent({
  name: 'DateField',
  props: {
    field: Object,
    value: String
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const currentValue = ref(props.value || props.field.default)

    const handleInput = (val) => {
      val = moment(val).format('YYYY-MM-DD HH:mm')
      emit('set-value', props.field.name, val)
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
.dp__theme_light {
  --dp-background-color: rgb(229 231 235 / var(--tw-bg-opacity));
}
</style>
