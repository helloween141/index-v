<template>
  <div>
    <div class="mb-3 flex justify-between items-center">
      <h1 class="dark:text-white text-2xl">
        {{ model.title }}
      </h1>

      <div class="flex w-full justify-end">
        <div v-show="searchPlaceholder && !isChild" class="relative w-1/2">
          <input type="search"
                 v-model="searchString"
                 @keyup.enter="onSearch"
                 :placeholder="`Поиск: ${searchPlaceholder}`"
                 class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-lg border-l-gray-200 border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-l-gray-700  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
          />
          <button @click.prevent="onSearch"
                  class="absolute top-0 right-0 p-2.5 text-sm font-medium text-white bg-blue-500 rounded-r-lg border border-blue-500 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <i class="fa-solid fa-search text-white"></i>
          </button>
        </div>

        <button v-show="!isChild && filterCount > 0"
                @click.prevent="onToggleFilter"
                class="hover:bg-blue-700 text-gray-800 font-bold ml-3 py-2 px-4 rounded"
                :class="show ? 'bg-gray-400 dark:bg-gray-700' : 'bg-blue-500'"
        >
          <i class="fa-solid fa-filter text-white"></i>
        </button>

        <button v-show="(!isModal || isChild) && model.showCreateButton"
                @click.prevent="onCreate"
                class="bg-blue-500 hover:bg-blue-700 ml-3 text-gray-800 font-bold py-2 px-4 rounded">
          <span class="text-white">
            <i class="fa-solid fa-circle-plus"></i> Создать {{ model.accusativeRecordTitle }}
          </span>
        </button>
      </div>
    </div>
    <div class="w-full border-t dark:border-gray-700 mb-5"></div>
  </div>
</template>

<script lang="ts">
import {defineComponent, ref} from "vue";
import {getFieldsForFilter, getPlaceholderForSearch} from "@/utils/utils";

export default defineComponent({
  name: 'EditorActionPanel',
  props: {
    model: Object,
    isModal: Boolean,
    isChild: Boolean,
    title: String,
    accusativeTitle: String,
    showFilterButton: Boolean
  },
  emits: ['on-create', 'on-toggle-filter', 'on-search'],
  setup(props, {emit}) {
    const searchString = ref('')
    const searchPlaceholder = getPlaceholderForSearch(props.model.fields)
    const filterCount = (getFieldsForFilter(props.model.fields)).length
    const show = ref(false)

    const onCreate = () => {
      emit('on-create')
    }

    const onToggleFilter = () => {
      show.value = !show.value
      emit('on-toggle-filter', show.value)
    }

    const onSearch = () => {
      emit('on-search', searchString.value)
    }

    return {
      onCreate,
      onSearch,
      onToggleFilter,
      searchString,
      searchPlaceholder,
      filterCount,
      show
    }
  }
})
</script>
