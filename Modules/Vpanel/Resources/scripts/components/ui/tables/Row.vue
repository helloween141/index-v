<template>
  <tr
      :class="{'cursor-pointer': isModal, 'text-red-500': row.deleted_at}"
      class="cursor-pointer border-b odd:bg-white odd:border-b odd:dark:bg-gray-900 odd:dark:border-gray-700 even:bg-gray-50 even:dark:bg-gray-800 even:dark:border-gray-700"
      @click="onSelect(row)"
  >
    <td v-for="(val, key, index) in row"
        :key="index"
        v-show="!['id', 'children', 'deleted_at'].includes(key)"
        class="py-3 px-6"
        :class="key"
        @click.prevent="isModal && onSelect(row)"
    >
      <img v-if="val && val.isImage && val.src"
           :src="val.src"
           style="width: 64px"
      />
      <span v-if="val && !val.isImage" v-html="val"></span>
    </td>
  </tr>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import Pagination from "@/components/ui/Pagination.vue";
import Draggable from 'vuedraggable'

export default defineComponent({
  name: 'Row',
  components: {Pagination, Draggable},
  emits: ['select-record'],
  props: {
    model: Object,
    row: Object,
    isModal: Boolean
  },
  setup(props, {emit}) {
    const onSelect = (record: any) => {
      emit('select-record', record)
    }

    return {
      onSelect
    }
  },
})
</script>

<style scoped>
</style>
