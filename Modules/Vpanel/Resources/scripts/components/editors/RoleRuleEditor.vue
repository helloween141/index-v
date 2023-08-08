<template>
  <div v-if="data">
    <div v-for="(module, keyModule) in data">
      <div class="mb-5">
        <h5 class="mb-2 font-bold tracking-tight text-gray-900 dark:text-white">{{ module.title }}</h5>
        <div
            class="block p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
          <div v-for="(model, keyModel) in module.list">
            <div class="mb-5">
              <h5 class="mb-3 font-bold tracking-tight text-gray-900 dark:text-white">{{ model.title }}</h5>

              <div class="flex mb-3">
                <PermissionRadioField
                    :title="'Запрещено'"
                    :name="`${keyModule}-${keyModel}`"
                    :value="0"
                    :id="0"
                    v-model="model.permission"
                />

                <PermissionRadioField
                    :title="'Чтение'"
                    :name="`${keyModule}-${keyModel}`"
                    :value="1"
                    :id="1"
                    v-model="model.permission"
                />

                <PermissionRadioField
                    :title="'Редактирование'"
                    :name="`${keyModule}-${keyModel}`"
                    :value="2"
                    :id="2"
                    v-model="model.permission"
                />

                <PermissionRadioField
                    :title="'Полный доступ'"
                    :name="`${keyModule}-${keyModel}`"
                    :value="4"
                    :id="4"
                    v-model="model.permission"
                />
              </div>

              <div class="flex items-center">
                <input :id="`checked-${keyModule}-${keyModel}-checkbox`"
                       type="checkbox"
                       :checked="model.menu === 1"
                       :value="!!model.menu"
                       v-model="model.menu"
                       class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label :for="`checked-${keyModule}-${keyModel}-checkbox`"
                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Отображать в меню</label>
              </div>

              <div v-if="(model.access_fields.length) > 0" class="mt-3">
                <div class="mb-1">
                  <span class="font-bold tracking-tight text-gray-900 dark:text-white text-sm">
                    Права по значению полей
                  </span>
                </div>
                <div v-for="accessField in model.access_fields" class="flex">
                  <div class="mb-3 mr-3">
                    <input :id="`checked-${accessField.name}-checkbox`"
                           type="checkbox"
                           :checked="accessField.active === 1"
                           :value="!!accessField.active"
                           v-model="accessField.active"
                           class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label :for="`checked-${accessField.name}-checkbox`"
                           class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ accessField.title }}
                    </label>
                  </div>

                  <div v-show="accessField.active" class="flex flex-col mb-3">
                    <PermissionRadioField
                        :title="'Запрещено'"
                        :name="`${keyModule}-${keyModel}-${accessField.name}`"
                        :value="0"
                        :id="0"
                        v-model="accessField.permission"
                    />

                    <PermissionRadioField
                        :title="'Чтение'"
                        :name="`${keyModule}-${keyModel}-${accessField.name}`"
                        :value="1"
                        :id="1"
                        v-model="accessField.permission"
                    />

                    <PermissionRadioField
                        :title="'Редактирование'"
                        :name="`${keyModule}-${keyModel}-${accessField.name}`"
                        :value="2"
                        :id="2"
                        v-model="accessField.permission"
                    />

                    <PermissionRadioField
                        :title="'Полный доступ'"
                        :name="`${keyModule}-${keyModel}-${accessField.name}`"
                        :value="4"
                        :id="4"
                        v-model="accessField.permission"
                    />
                  </div>
                </div>
              </div>
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
</template>

<script lang="ts">
import {defineComponent, onMounted, ref} from "vue";
import {getRouteParameters} from "@/utils/utils";
import {useRoute} from "vue-router";
import {Button} from "flowbite-vue";
import {useToast} from "vue-toastification";
import PermissionRadioField from "@/components/ui/fields/PermissionRadioField.vue";
import {executeRequest} from "@/api/actionRequest";

export default defineComponent({
  name: 'RoleRuleEditor',
  components: {PermissionRadioField, Button},
  emits: ['reload'],
  setup(props, {emit}) {
    const data = ref()
    const toast = useToast()
    const route = useRoute()
    const {recordId} = getRouteParameters(route)

    onMounted(async () => {
      data.value = await executeRequest('GET', 'vpanel', `get-permission-list/${recordId}`, {
        data: data.value
      })
    })

    const onSave = async () => {
      await executeRequest('POST', 'vpanel', `save-permission-list/${recordId}`, {
        data: data.value
      })
    }

    return {
      data,
      onSave
    }
  }
})
</script>

<style scoped>

</style>
