<template>
  <QuillEditor v-model:content="currentValue"
               theme="snow"
               toolbar="full"
               content-type="html"
               class="bg-gray-200 appearance-none border-gray-200 rounded w-full text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
               style="height: 300px; font-size: initial;"
               @textChange="handleInput"
  />
</template>

<script>
import {defineComponent, ref, watch} from "vue";
import {QuillEditor} from "@vueup/vue-quill";

export default defineComponent({
  name: 'HtmlField',
  components: {QuillEditor},
  props: {
    field: Object,
    value: [String, Number]
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const currentValue = ref(props.value || props.field.default || '')

    const handleInput = () => {
      emit('set-value', props.field.name, currentValue.value)
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
