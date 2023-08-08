<template>
  <vue-final-modal
    v-slot="{ params, close }"
    v-bind="$attrs"
    classes="flex justify-center mt-5 mb-5 overflow-y-auto"
    content-class="relative w-3/4 h-fit p-4 border dark:border-gray-800 rounded bg-white dark:bg-gray-900"
    v-on="$listeners"
  >
    <i class="fa fa-close float-right cursor-pointer dark:text-gray-500" @click="close"></i>

    <span class="mr-8 text-2xl font-bold">
      <slot name="title"></slot>
    </span>

    <div class="flex-grow">
      <slot name="content"></slot>
    </div>
  </vue-final-modal>
</template>

<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
  name: 'VModal',
  inheritAttrs: false
})
</script>
