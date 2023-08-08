<template>
  <section v-if="tabs.length > 0 && currentValues.id > 0">
    <FormTabPanel
        :tabs="tabs"
        @select-tab="selectTab"
    />
    <ModuleComponent
        v-if="modelTab"
        :module="modelTab.module"
        :model="modelTab.model"
        :default-filter="modelTab.filter"
        :is-child="true"
    />
    <DefaultFieldsTable
        v-else
        :fields="incModel.fields"
        :values="currentValues"
        @set-value="setValue"
    />
  </section>
  <section v-else>
    <DefaultFieldsTable
        :fields="incModel.fields"
        :values="currentValues"
        @set-value="setValue"
    />
    <div v-if="currentValues.id > 0">
      <div v-for="additionalModel in additionalModels" class="mt-5">
        <ModuleComponent
            :module="additionalModel.module"
            :model="additionalModel.model"
            :default-filter="additionalModel.filter"
            :is-child="true"
        />
      </div>
    </div>
  </section>
</template>

<script lang="ts">
import {defineComponent, watch} from 'vue';
import {useModelForm} from "@/components/composables/useModelForm";
import FormTabPanel from "@/components/ui/FormTabPanel.vue";
import ModuleComponent from "@/components/ModuleComponent.vue";
import DefaultFieldsTable from "@/components/ui/tables/DefaultFieldsTable.vue";

export default defineComponent({
  name: 'FormContent',
  components: {DefaultFieldsTable, ModuleComponent, FormTabPanel},
  emits: ['on-update-values'],
  props: {
    incValues: Object,
    incModel: Object,
  },
  setup(props, {emit}) {
    const {setValue, selectTab, tabs, modelTab, additionalModels, currentValues} = useModelForm(props, emit)

    watch(currentValues, () => {
      emit('on-update-values', currentValues)
    }, {flush: 'pre', immediate: true, deep: true})

    return {
      tabs,
      modelTab,
      additionalModels,
      currentValues,
      selectTab,
      setValue
    }
  }
})
</script>

<style scoped>

</style>
