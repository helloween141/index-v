<template>
  <v-select
      v-if="identifyLabel"
      ref="select"
      v-model="selectedOption"
      :label="identifyLabel"
      :clearable="!field.required"
      :disabled="field.readonly"
      @click="handleClick"
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
    <template v-slot:no-options>Идет загрузка...</template>
  </v-select>
</template>

<script lang="ts">
import {defineComponent, onMounted, ref, watch} from "vue";
import {$vfm} from "vue-final-modal";
import VModal from "@/components/ui/modal/VModal.vue";
import DefaultModelEditor from "@/components/editors/DefaultModelEditor.vue";
import {parseModelPath} from "@/utils/utils";
import {loadInterface, loadList} from "@/api/actionEditor";

export default defineComponent({
  name: 'PointerModalField',
  props: {
    field: Object,
    value: Object
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const identifyLabel = ref()
    const selectedOption = ref(props.value)
    const pointerPath = parseModelPath(props.field.model)
    let identifyField = {}
    let model = null
    let values = null

    onMounted(async () => {
      await init()
    })

    watch(() => selectedOption.value, (current, previous) => {
      if (!current) {
        emit('set-value', props.field.name, selectedOption.value)
      }
    })

    const init = async () => {
      model = await loadInterface(pointerPath.module, pointerPath.model)

      identifyField = model.fields.find(field => field.identify)
      if (identifyField) {
        identifyLabel.value = identifyField['name']
      }

      values = await loadList(pointerPath.module, pointerPath.model, {
        page: 1,
        query_string: props.field?.queryString
      })
    }

    const handleClick = async () => {
      await init()
      $vfm.show({
        component: VModal,
        bind: {
          name: 'PointerModal'
        },
        slots: {
          content: {
            component: DefaultModelEditor,
            bind: {
              incModel: model,
              incValues: values,
              incPathData: {
                module: pointerPath.module,
                model: pointerPath.model,
              },
              name: name,
              isModal: true,
              modalValue: props.value?.id
            },
            on: {
              selectRecord(record) {
                selectedOption.value = record[identifyLabel.value]
                emit('set-value', props.field.name, record)
                $vfm.hide('PointerModal')
              }
            }
          }
        }
      })
    }

    return {
      selectedOption,
      identifyLabel,
      handleClick
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
