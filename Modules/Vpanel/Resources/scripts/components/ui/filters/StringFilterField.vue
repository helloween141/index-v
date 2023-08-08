<template>
  <div>
     <span class="dark:text-white mb-3">{{ field.title }}</span>
     <input type="text"
             :value="value"
             @input="onInput($event.target.value)"
             class="bg-gray-200 appearance-none border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
     />
  </div>
</template>

<script>
import {defineComponent} from "vue";

export default defineComponent({
  name: 'StringFilterField',
  emits: ['set-filter'],
  props: {
    value: String,
    field: Object,
  },
  setup(props, {emit}) {
    const onInput = (val) => {
      emit('set-filter', {[props.field.name]: val}, props.field.name)
    }

    return {
      onInput
    }
  }
})
</script>

<style scoped>

</style>
