<template>
  <h1 class="dark:text-white text-2xl">
    <span v-if="recordId > 0">
      Редактировать {{ title }}
    </span>
    <span v-else>
      Создать {{ title }}
    </span>
  </h1>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {useRoute} from "vue-router";
import {getRouteParameters} from "@/utils/utils";

export default defineComponent({
  name: 'FormHeader',
  props: {
    title: String,
  },
  setup() {
    const route = useRoute()
    const {recordId} = getRouteParameters(route)

    return {
      recordId
    }
  }
})
</script>

<style scoped>

</style>
