<template>
  <div>
    <div v-if="widgetList.length > 0" class="flex flex-wrap gap-x-3 gap-y-3">
      <component
          v-for="widget in widgetList"
          :is="widget"
      />
    </div>
  </div>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent, onMounted, shallowRef} from "vue";
import {executeRequest} from "@/api/actionRequest";

export default defineComponent({
  setup() {
    const widgetList = shallowRef([]);

    onMounted(async () => {
      const result = await executeRequest('GET', 'vpanel', 'user/widgets')
      widgetList.value = (result).map(widget => loadWidgetComponent(widget))
    })

    const loadWidgetComponent = (widget) => {
      return defineAsyncComponent(() => import(
          `../../../../../Modules/${widget.module}/Resources/scripts/components/widgets/${widget.component}.vue`)
      )
    }

    return {
      widgetList
    }
  }
})
</script>
