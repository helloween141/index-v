<template>
  <div v-if="data">
    <div v-for="(module, keyModule) in data">
      <div class="mb-5">
        <h5 class="mb-2 font-bold tracking-tight text-gray-900 dark:text-white">{{ module.title }}</h5>
        <div
            class="block p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
          <div v-for="(widget, keyWidget) in module.list" class="flex flex-col">
            <div class="mb-1">
              <input :id="`default-checkbox-${widget.id}`"
                     type="checkbox"
                     v-model="widgetIds"
                     :checked="widget.active"
                     :value="widget.id"
                     class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
              />
              <label :for="`default-checkbox-${widget.id}`"
                     class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ widget.title }}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="flex justify-end">
      <button @click.prevent="onSave"
              class="mt-3 bg-green-500 float-right hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        <span class="text-white"> Сохранить </span>
      </button>
    </div>
  </div>
  <div v-else>
    <p class="font-normal text-gray-700 dark:text-gray-400">Виджеты не найдены!</p>
  </div>
</template>

<script lang="ts">
import {defineComponent, onMounted, ref} from "vue";
import {getRouteParameters} from "@/utils/utils";
import {useRoute} from "vue-router";
import {useToast} from "vue-toastification";
import {executeRequest} from "@/api/actionRequest";

export default defineComponent({
  name: 'RoleWidgetEditor',
  emits: ['reload'],
  setup(props, {emit}) {
    const data = ref()
    const widgetIds = ref([])
    const toast = useToast()
    const route = useRoute()
    const {recordId} = getRouteParameters(route)

    onMounted(async () => {
      data.value = await executeRequest('GET', 'vpanel', `get-widget-list/${recordId}`)

      if (data.value) {
        data.value.forEach(item => {
          item.list.forEach(widget => {
            if (widget.active) {
              (widgetIds.value).push(widget.id)
            }
          })
        })
      }
    })

    const onSave = async () => {
      await executeRequest('POST', 'vpanel', `save-widget-list/${recordId}`, {
        widget_ids: widgetIds.value
      })
    }

    return {
      data,
      widgetIds,
      onSave
    }
  }
})
</script>

<style scoped>

</style>
