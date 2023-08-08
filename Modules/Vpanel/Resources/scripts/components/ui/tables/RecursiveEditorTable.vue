<template>
  <div v-if="rows && rows.length > 0">
    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead v-if="headers" class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th v-if="isModal" class="w-8"></th>
          <th v-for="header in headers" scope="col" class="py-3 px-6">
            {{ header }}
          </th>
        </tr>
        </thead>
        <RecursiveRow
            v-for="row in rows"
            :model="model"
            :row="row"
            :level="1"
            :is-modal="isModal"
            :modal-value="modalValue"
            @select-record="onClick"
        />
      </table>
    </div>
    <Pagination
        :pages="values"
        @set-page="setPage"
    />
  </div>
  <div v-else>
    <p class="font-normal text-gray-700 dark:text-gray-400">Записи не найдены!</p>
  </div>
</template>

<script lang="ts">
import {defineComponent, onMounted, ref, watch} from "vue";
import Pagination from "@/components/ui/Pagination.vue";
import {getHeadersForEditorTable, getRowsForEditorTable} from "@/utils/utils";
import Draggable from 'vuedraggable'
import RecursiveRow from "@/components/ui/tables/RecursiveRow.vue";

export default defineComponent({
  name: 'RecursiveEditorTable',
  components: {RecursiveRow, Pagination, Draggable},
  emits: ['select-record', 'set-page', 'change-sort'],
  props: {
    model: Object,
    values: Object,
    isModal: Boolean,
    modalValue: Number
  },
  setup(props, {emit}) {
    let headers = ref()
    let rows = ref()

    const init = () => {
      if (props.model) {
        headers.value = getHeadersForEditorTable(props.model.fields, props.model?.editorGroup || [])
      }

      if (props.values) {
        rows.value = getRowsForEditorTable(props.model.fields, props.values.data, props.model?.editorGroup || [])
      }
    }

    onMounted(() => {
      init()
    })

    watch(() => props.values, () => {
      init()
    }, {deep: true})

    const onClick = (record: any) => {
      emit('select-record', record)
    }

    const setPage = (page: number) => {
      emit('set-page', page)
    }

    const onChangeSort = () => {
      const sortData = []
      rows.value.forEach((row, key) => {
        sortData.push(row.id)
      })
      emit('change-sort', sortData)
    }

    return {
      headers,
      rows,
      onClick,
      onChangeSort,
      setPage
    }
  },
})
</script>

<style scoped>
</style>
