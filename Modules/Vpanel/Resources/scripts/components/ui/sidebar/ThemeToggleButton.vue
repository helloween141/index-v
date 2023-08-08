<template>
  <button aria-hidden="true" class="relative focus:outline-none" @click="onClick">
    <div class="w-12 h-6 transition rounded-full outline-none bg-gray-100 dark:bg-gray-500"></div>
    <div
        class="absolute top-0 left-0 inline-flex items-center justify-center w-6 h-6 transition-all duration-150 transform scale-110 rounded-full shadow-sm translate-x-0 -translate-y-px bg-white text-primary-dark"
        :class="{ 'translate-x-0 -translate-y-px  bg-white text-primary-dark': !themeStore.isDark, 'translate-x-6 text-primary-100 bg-primary-darker': themeStore.isDark }">
      <svg v-show="!themeStore.isDark" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
      </svg>
      <svg v-show="themeStore.isDark" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke="currentColor" style="display: none;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
      </svg>
    </div>
  </button>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useThemeStore} from "@/stores/theme";

export default defineComponent({
  setup(props, { emit }) {
    const themeStore = useThemeStore()

    const onClick = () => {
      emit('on-click')
    }

    return {
      onClick,
      themeStore
    }
  }
})
</script>


<style scoped>

</style>