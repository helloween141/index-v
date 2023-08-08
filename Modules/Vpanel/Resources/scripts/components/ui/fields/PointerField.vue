<template>
  <v-select
      v-if="identifyLabel"
      v-model="selectedOption"
      :label="identifyLabel"
      :options="options"
      :clearable="!field.required"
      :disabled="field.readonly"
      @update:modelValue="handleInput"
      class="py-2 bg-gray-200 text-gray-700 rounded leading-tight focus:outline-none focus:bg-white focus:border-purple-500 custom-fx"
  >
    <template #search="{attributes, events}">
      <input
          class="vs__search"
          :required="field.required && !selectedOption"
          v-bind="attributes"
          v-on="events"
      />
    </template>
    <template v-slot:no-options>Записи не найдены!</template>
  </v-select>
</template>

<script lang="ts">
import {defineComponent, onMounted, ref, watch} from "vue";
import {parseModelPath} from "@/utils/utils";
import {loadPointer} from "@/api/actionEditor";

export default defineComponent({
  name: 'PointerField',
  props: {
    field: Object,
    value: Object
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const identifyLabel = ref()
    const options = ref()
    const selectedOption = ref(props.value)
    const pointerPath = parseModelPath(props.field.model)

    onMounted(async () => {
      await init()
    })

    watch(() => props.field?.queryString, async (current, previous) => {
      await init()
    })

    const init = async () => {
      const pointerData = await loadPointer(pointerPath.module, pointerPath.model, {
        query_string: props.field?.queryString
      })
      options.value = pointerData.values
      identifyLabel.value = pointerData.identifyLabel
    }

    const handleInput = () => {
      emit('set-value', props.field.name, selectedOption.value)
    }

    return {
      options,
      identifyLabel,
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
