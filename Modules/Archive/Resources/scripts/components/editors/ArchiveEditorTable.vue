<template>
  <div v-if="values && values.data.length > 0">
    <div class="container grid grid-cols-6 gap-fx">
      <div v-for="item in values.data"
           class="flex flex-col justify-around w-full rounded cursor-pointer text-center border"
           @click="onClick(item.id)"
      >
        <a :href="getLink(item.path)" download="">
          <div v-if="model.alias === 'image'">
            <img :src="getLink(item.path)" :alt="item.name" class="w-48 inline"/>
          </div>
          <div v-else-if="model.alias === 'file'" class="p-2">
            <i class="fa fa-2x fa-file dark:text-white"></i>
          </div>
          <div class="break-words">
            <span class="text-gray-900 dark:text-white text-sm">{{ item.name }}</span>
          </div>
        </a>
      </div>
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
import {defineComponent} from "vue";
import Pagination from "../../../../../Vpanel/Resources/scripts/components/ui/Pagination.vue";
import {getLink} from "../../../../../Vpanel/Resources/scripts/utils/utils";
import Draggable from 'vuedraggable'

export default defineComponent({
  name: 'ImageEditorTable',
  components: {Pagination, Draggable},
  emits: ['select-record', 'set-page', 'change-sort'],
  props: {
    model: Object,
    values: Object
  },
  setup(props, {emit}) {
    const onClick = (recordId: number) => {
      emit('select-record', recordId)
    }

    const setPage = (page: number) => {
      emit('set-page', page)
    }

    return {
      onClick,
      setPage,
      getLink
    }
  },
})
</script>

<style scoped>
.gap-fx {
  gap: 1rem;
}
</style>
