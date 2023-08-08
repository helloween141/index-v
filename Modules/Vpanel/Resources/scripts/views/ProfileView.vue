<template>
  <div
      class="block p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
    <h5 class="mb-3 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Мой профиль</h5>
    <form v-if="modelInterface && currentValues">
      <DefaultFieldsTable
          :fields="modelInterface.fields"
          :values="currentValues"
          @set-value="setValue"
      />
      <div class="flex justify-end">
        <button type="submit" @click.prevent="onSave"
                class="mt-5 bg-green-500 float-right hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
          <span class="text-white">
            Сохранить
          </span>
        </button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import {defineComponent, onMounted, ref} from "vue";
import {loadInterface} from "@/api/actionEditor";
import DefaultFieldsTable from "@/components/ui/tables/DefaultFieldsTable.vue";
import {useUserStore} from "@/stores/user";
import {excludeFields, prepareFormData} from "@/utils/utils";
import {executeRequest} from "@/api/actionRequest";
import {useRouter} from "vue-router";

export default defineComponent({
  components: {DefaultFieldsTable},
  setup() {
    const userStore = useUserStore()
    const currentValues = ref(null)
    const modelInterface = ref(null)
    const router = useRouter()

    onMounted(async () => {
      modelInterface.value = await loadInterface('Vpanel', 'User')
      currentValues.value = {...currentValues.value, ...userStore.user}

      modelInterface.value.fields = excludeFields(modelInterface.value.fields, ['position'])
    })

    const setValue = (fieldName: string, fieldValue: any) => {
      currentValues.value[fieldName] = fieldValue
    }

    const onSave = async () => {
      const formData = prepareFormData(currentValues.value)
      const result = await executeRequest('POST', 'vpanel', 'user/update', formData)
      if (result) {
        setTimeout(() => {
          router.go(0)
        }, 1200)
      }
    }

    return {
      currentValues,
      modelInterface,
      setValue,
      onSave
    }
  }
})
</script>
