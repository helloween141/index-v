<template>
  <div
      class="relative block p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
    <form>
      <!--<span class="block mb-5">{{ incModel }}</span>-->
      <div class="mb-3 flex justify-between flex-wrap">
        <FormHeader :title="getHeaderTitle()"/>

        <FormActionPanel
            v-show="!modelTab"
            :model="incModel"
            :record="incValues"
            @on-save="onSave"
            @on-restore="onRestore"
            @on-delete="onDelete"
            @on-back="onBack"
        />
      </div>

      <div class="w-full border-t dark:border-gray-700"></div>

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

      <div class="mt-3 flex justify-end">
        <FormActionPanel
            v-show="!modelTab"
            :model="incModel"
            :record="incValues"
            @on-save="onSave"
            @on-delete="onDelete"
            @on-back="onBack"
        />
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import FormActionPanel from "@/components/ui/FormActionPanel.vue";
import {defineComponent} from "vue";
import DefaultFieldsTable from "@/components/ui/tables/DefaultFieldsTable.vue";
import FormTabPanel from "@/components/ui/FormTabPanel.vue";
import ModuleView from "@/views/ModuleView.vue";
import ModuleComponent from "@/components/ModuleComponent.vue";
import {useModelForm} from "@/components/composables/useModelForm";
import FormHeader from "@/components/ui/FormHeader.vue";
import FormContent from "@/components/ui/FormContent.vue";
import {getIdentifyFieldValue} from "@/utils/utils";

export default defineComponent({
  name: 'DefaultModelForm',
  components: {FormContent, FormHeader, ModuleComponent, ModuleView, FormTabPanel, DefaultFieldsTable, FormActionPanel},
  props: {
    incModel: Object,
    incValues: Object
  },
  emits: ['reload'],
  setup(props, {emit}) {
    const {
      onSave, onDelete, onRestore, onBack, setValue, selectTab, getHeaderTitle,
      currentValues, tabs, modelTab, additionalModels
    } = useModelForm(props, emit)

    const headerTitle = `${props.incModel.accusativeRecordTitle} ${getIdentifyFieldValue(props.incModel.fields, props.incValues)}`

    return {
      onSave,
      onDelete,
      onRestore,
      onBack,
      setValue,
      selectTab,
      getHeaderTitle,
      tabs,
      modelTab,
      additionalModels,
      currentValues,
      headerTitle
    }
  },
})
</script>

<style scoped>

</style>
