<template>
  <div v-if="incModel">
    <EditorActionPanel
        :is-child="isChild"
        :is-modal="!!isModal"
        :model="incModel"
        @on-create="createRecord"
        @on-search="applySearch"
        @on-toggle-filter="toggleFilterPanel"
    />

    <EditorFilterPanel
        v-show="showFilterPanel && filterFields.length > 0"
        :fields="filterFields"
        @on-filter="applyFilter"
    />
    
    <EditorShowControlPanel
        v-show="incModel.softDelete"
        :is-recursive="incModel.recursive"
        @on-filter="applyFilter"
    />

    <RecursiveEditorTable
        v-if="incModel.recursive"
        @select-record="selectRecord"
        @set-page="setPage"
        @change-sort="changeSort"
        :model="incModel"
        :values="currentValues"
        :is-modal="isModal"
        :modal-value="modalValue"
    />
    <DefaultEditorTable
        v-else
        @select-record="selectRecord"
        @set-page="setPage"
        @change-sort="changeSort"
        :model="incModel"
        :values="currentValues"
        :is-modal="isModal"
        :modal-value="modalValue"
    />
  </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import DefaultEditorTable from "@/components/ui/tables/DefaultEditorTable.vue";
import EditorActionPanel from "@/components/ui/EditorActionPanel.vue";
import EditorFilterPanel from "@/components/ui/EditorFilterPanel.vue";
import RecursiveEditorTable from "@/components/ui/tables/RecursiveEditorTable.vue";
import {useModelEditor} from "@/components/composables/useModelEditor";
import EditorShowControlPanel from "@/components/ui/EditorShowControlPanel.vue";

export default defineComponent({
  name: 'DefaultModelEditor',
  components: {EditorShowControlPanel, RecursiveEditorTable, EditorFilterPanel, EditorActionPanel, DefaultEditorTable},
  emits: ['select-record', 'reload'],
  props: {
    incModel: Object,
    incValues: Object,
    incPathData: Object,
    isModal: Boolean,
    isChild: Boolean,
    modalValue: Number
  },
  setup(props, {emit}) {
    const {
      selectRecord, createRecord, applySearch, setPage, toggleFilterPanel, applyFilter,
      currentValues, showFilterPanel, filterFields, changeSort
    } = useModelEditor(props, emit)

    return {
      selectRecord,
      createRecord,
      toggleFilterPanel,
      applyFilter,
      applySearch,
      setPage,
      changeSort,
      showFilterPanel,
      filterFields,
      currentValues
    }
  }
})
</script>

<style scoped>

</style>
