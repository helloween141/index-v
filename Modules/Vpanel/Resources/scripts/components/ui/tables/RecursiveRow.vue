<template>
  <tr
      :class="{'text-red-500': row.deleted_at}"
      class="cursor-pointer border-b even:bg-white even:border-b even:dark:bg-gray-900 even:dark:border-gray-700 odd:bg-gray-50 odd:dark:bg-gray-800 odd:dark:border-gray-700"
      @click.prevent="onSelect(row)"
  >
    <td v-if="isModal" class="px-6 w-8">
      <div class="flex justify-between">
        <input
            :checked="row.id === modalValue"
            type="checkbox"
            class="ml-3 cursor-pointer w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            @click.prevent="isModal && onSelect(row)"
        />
      </div>
    </td>
    <td v-for="(val, key, index) in row"
        v-show="!['id', 'children', 'deleted_at'].includes(key)"
        :key="index"
        :class="key"
        class="py-3 px-6"
    >
      <div :style="offsetStyle"
           class="py-3"
      >
        <div class="flex items-center">
          <i v-if="row.children && row.children.length > 0 && !model.expandTree && index === 2"
             @click.stop="onShow()"
             class="fa-solid cursor-pointer cursor-pointer bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-1.5 rounded-lg mr-3"
             :class="chevronClass"
          />

          <img v-if="val && val.isImage && val.src"
               :src="val.src"
               class="py-3"
               style="width: 64px"
          />
          <div v-if="val && !val.isImage"
               v-html="val"
               class="py-3">
          </div>
        </div>
      </div>
    </td>
  </tr>
  <RecursiveRow
      v-if="show || model.expandTree"
      v-for="child in children"
      :model="model"
      :row="child"
      :is-modal="isModal"
      :modal-value="modalValue"
      :level="level + 1"
      @select-record="onSelect"
  />
</template>

<script lang="ts">
import {defineComponent, onMounted, ref, watch} from "vue";
import Pagination from "@/components/ui/Pagination.vue";
import {getRowsForEditorTable} from "@/utils/utils";
import Draggable from 'vuedraggable'

export default defineComponent({
  name: 'RecursiveRow',
  components: {Pagination, Draggable},
  emits: ['select-record', 'select-child-record'],
  props: {
    model: Object,
    row: Object,
    children: Object,
    level: Number,
    isModal: Boolean,
    modalValue: Number
  },
  setup(props, {emit}) {
    const show = ref(false)
    const chevronClass = ref('fa-plus-square')
    const children = ref([])
    const offsetStyle = props.level > 1 ? `margin-left: ${props.level * 13}px; padding-left: 1rem` : 'padding-left: 0'

    onMounted(() => {
      init()
    })

    const init = () => {
      if (props.model.expandTree) {
        onShow()
      }

      if (props.row.children) {
        children.value = getRowsForEditorTable(props.model.fields, props.row.children, props.model?.editorGroup || [])
      }
    }

    watch(() => [props.row], () => {
      init()
    }, {deep: true})


    const onSelect = (record: any) => {
      emit('select-record', record)
    }

    const onShow = () => {
      show.value = !show.value
      chevronClass.value = show.value ? 'fa-minus-square' : 'fa-plus-square'
    }

    return {
      children,
      show,
      chevronClass,
      offsetStyle,
      onSelect,
      onShow
    }
  },
})
</script>

<style scoped>
</style>
