<template>
  <div class="relative">
    <div v-show="show"
         class="inline-block absolute z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm transition-opacity duration-300 tooltip dark:bg-gray-700"
         :style="style"
    >
      {{ text }}
      <div class="tooltip-arrow" data-popper-arrow></div>
    </div>
    <div @mouseenter="onMouseEnter"
         @mouseleave="onMouseLeave"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script lang="ts">
import {defineComponent, ref} from "vue";

export default defineComponent({
  name: "Tooltip",
  props: {
    text: String,
    style: String
  },
  setup() {
    let show = ref(false)

    const onMouseEnter = () => {
      show.value = true
    }

    const onMouseLeave = () => {
      show.value = false
    }

    return {
      onMouseEnter,
      onMouseLeave,
      show
    }
  }
})
</script>

<style scoped>

</style>
