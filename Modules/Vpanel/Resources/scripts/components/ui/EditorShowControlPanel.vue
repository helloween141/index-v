<template>
  <div
      class="mb-5 p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
    <div class="flex flex-wrap">
      <span class="text-sm font-medium text-gray-900 dark:text-gray-300 pr-4">Показывать:</span>
      <div class="flex items-center mr-4">
        <input type="radio"
               value="actual"
               name="colored-radio"
               :checked="currentValue === 'actual'"
               @change="onChange('actual')"
               class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        <label for="green-radio" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Актуальные</label>
      </div>
      <div v-show="!isRecursive" class="flex items-center mr-4">
        <input type="radio"
               value="trashed"
               name="colored-radio"
               :checked="currentValue === 'trashed'"
               @change="onChange('trashed')"
               class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        <label for="red-radio" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Удаленные</label>
      </div>
      <div class="flex items-center mr-4">
        <input type="radio"
               value="all"
               name="colored-radio"
               :checked="currentValue === 'all'"
               @change="onChange('all')"
               class="w-4 h-4 text-orange-500 bg-gray-100 border-gray-300 focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        <label for="orange-radio" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Все</label>
      </div>
    </div>

  </div>
</template>

<script lang="ts">
import {defineComponent, ref} from "vue";
import {useRoute} from "vue-router";

export default defineComponent({
  name: 'EditorShowControlPanel',
  props: {
    fields: Object,
    isRecursive: Boolean
  },
  emits: ['on-filter'],
  setup(props, {emit}) {
    const route = useRoute()
    let filter = route.query.f ? JSON.parse(route.query.f.toString()) : {}
    const currentValue = ref(filter.show_only || 'actual')
    const onChange = (value: string) => {
      if (value === 'actual') {
        delete filter.show_only
      } else {
        filter = {...filter, ...{show_only: value}}
      }
      emit('on-filter', filter)
    }

    return {
      filter,
      currentValue,
      onChange
    }
  }
})
</script>
