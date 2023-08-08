<template>
  <div>
    <span class="dark:text-white">{{ field.title }}</span>
    <div class="flex">
      <NumberFilterField
          :placeholder="'От'"
          :type="'from'"
          :value="(value && value[0]) ? value[0].value : ''"
          @set-value="onInput"
          class="mr-3"
      />
      <NumberFilterField
          :placeholder="'До'"
          :type="'to'"
          :value="(value && value[1]) ? value[1].value : ''"
          @set-value="onInput"
      />
    </div>
  </div>
</template>

<script>
import {defineComponent} from "vue";
import NumberFilterField from "./NumberFilterField.vue";

export default defineComponent({
  name: 'NumberRangeFilterField',
  components: {NumberFilterField},
  emits: ['set-filter'],
  props: {
    field: Object,
    value: [Array],
  },
  setup(props, {emit}) {
    let valFrom = ''
    let valTo = ''

    if (Array.isArray(props.value)) {
      props.value.forEach(item => {
        if (item.comparsion === '>=') {
          valFrom = item.value
        } else if (item.comparsion === '<=') {
          valTo = item.value
        }
      })
    }


    const onInput = (val, type) => {
      valFrom = type === 'from' ? val : valFrom
      valTo = type === 'to' ? val : valTo

      const result = []
      if (valFrom) {
        result.push({
          'comparsion': '>=',
          'value': valFrom
        })
      }

      if (valTo) {
        result.push({
          'comparsion': '<=',
          'value': valTo
        })
      }

      emit('set-filter', {[props.field.name]: result}, props.field.name)
    }

    return {
      onInput
    }
  }
})
</script>

<style scoped>

</style>
