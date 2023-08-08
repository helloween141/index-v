<template>
  <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700 mb-5">
    <ul class="flex flex-wrap -mb-px">
      <li v-for="tab in tabs" class="mr-2">
        <a href="#"
           @click.prevent="onClick(tab)"
           class="inline-block p-4 rounded-t-lg border-b-2"
           :class="[
               tab.active
               ? 'text-blue-600 rounded-t-lg border-b-2 border-blue-600 active dark:text-blue-500 dark:border-blue-500'
               : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300']"
        >
          {{ tab.title }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import {defineComponent, ref} from "vue";

export default defineComponent({
  name: 'FormTabPanel',
  props: {
    tabs: Array
  },
  emits: ['select-tab'],
  setup(props, {emit}) {
    const onClick = (selectedTab) => {
      emit('select-tab', selectedTab)
    }

    return {
      onClick
    }
  }
})
</script>
