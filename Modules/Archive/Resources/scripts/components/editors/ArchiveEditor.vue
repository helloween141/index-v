<template>
  <div v-if="incModel">
    <EditorActionPanel
        :is-child="isChild"
        :is-modal="isModal"
        :model="incModel"
        @on-create="createRecord"
        @on-search="applySearch"
    />

    <ArchiveEditorTable
        @select-record="selectRecord"
        @set-page="setPage"
        :model="incModel"
        :values="currentValues"
    />
  </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import EditorActionPanel from "../../../../../Vpanel/Resources/scripts/components/ui/EditorActionPanel.vue";
import ArchiveEditorTable from "./ArchiveEditorTable.vue";
import {useModelEditor} from "../../../../../Vpanel/Resources/scripts/components/composables/useModelEditor";

export default defineComponent({
  name: 'ArchiveEditor',
  components: {ArchiveEditorTable, EditorActionPanel},
  emits: ['select-record', 'reload'],
  props: {
    incModel: Object,
    incValues: Object,
    incPathData: Object,
    isModal: Boolean,
    isChild: Boolean
  },
  setup(props, {emit}) {
    const {selectRecord, createRecord, applySearch, setPage, currentValues} = useModelEditor(props, emit)

    return {
      selectRecord,
      createRecord,
      applySearch,
      setPage,
      currentValues
    }
  }
})
</script>

<style scoped>

</style>
