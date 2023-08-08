<template>
  <div v-if="currentValues"
       class="relative block p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
    <form>
      <div class="mb-3 flex justify-between flex-wrap">
        <FormHeader
            :title="getHeaderTitle()"
        />
        <FormActionPanel
            :model="incModel"
            :record="incValues"
            @on-save="onSave"
            @on-delete="onDelete"
            @on-back="onBack"
        />
      </div>

      <div class="w-full border-t dark:border-gray-700"></div>

      <section>
        <section v-if="tabs.length > 0 && currentValues.id > 0">
          <FormTabPanel
              :tabs="tabs"
              @select-tab="selectTab"
          />
          <ModuleComponent
              v-if="modelTab"
              :module="modelTab.module"
              :model="modelTab.model"
              :default-filter="modelTab.filter"
              :is-child="true"
          />
          <DefaultFieldsTable
              v-else
              :fields="incModel.fields"
              :values="currentValues"
              @set-value="setValue"
          />
        </section>
        <section v-else>
          <DefaultFieldsTable
              :fields="incModel.fields"
              :values="currentValues"
              @set-value="setValue"
          />
          <div v-if="currentValues.id > 0">
            <div v-for="additionalModel in additionalModels" class="mt-5">
              <ModuleComponent
                  :module="additionalModel.module"
                  :model="additionalModel.model"
                  :default-filter="additionalModel.filter"
                  :is-child="true"
              />
            </div>
          </div>
        </section>

        <div class="mt-3" v-if="!modelTab">
          <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Роли</h3>
          <ul class="w-48 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <li v-for="role in allRoles" class="w-full rounded-t-lg border-b border-gray-200 dark:border-gray-600">
              <div class="flex items-center pl-3">
                <input type="checkbox"
                       :id="role.name"
                       :value="role.name"
                       :checked="checkedRoles[role.name]"
                       v-model="checkedRoles"
                       @change="onCheckRole($event)"
                       class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                />
                <label :for="role.name"
                       class="py-3 ml-2 w-full text-sm font-medium text-gray-900 dark:text-gray-300">
                  {{ role.title }}
                </label>
              </div>
            </li>
          </ul>
        </div>
        <div v-show="!modelTab && currentValues.id && !currentValues.role.includes('ROOT')" class="mt-4">
          <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Войти под пользователем</h3>
          <button @click.prevent="onAuthAsUser"
                  class="bg-orange-500 hover:bg-orange-700 text-white font-bold mr-3 py-2 px-4 rounded focus:outline-none focus:shadow-outline">
              <span class="text-white">
                <i class="fa-solid fa-sign-in"></i>
                  Авторизироваться
              </span>
          </button>
        </div>
      </section>

      <div class="mt-3 flex justify-end">
        <FormActionPanel
            :model="incModel"
            :record="incValues"
            @on-save="onSave"
            @on-delete="onDelete"
            @on-back="onBack"
        />
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import FormActionPanel from "@/components/ui/FormActionPanel.vue";
import {defineComponent, onMounted, ref} from "vue";
import DefaultFieldsTable from "@/components/ui/tables/DefaultFieldsTable.vue";
import FormTabPanel from "@/components/ui/FormTabPanel.vue";
import ModuleView from "@/views/ModuleView.vue";
import ModuleComponent from "@/components/ModuleComponent.vue";
import {loadList} from "@/api/actionEditor";
import {useModelForm} from "@/components/composables/useModelForm";
import FormHeader from "@/components/ui/FormHeader.vue";
import {executeRequest} from "@/api/actionRequest";
import router from "@/router";
import {useUserStore} from "@/stores/user";

export default defineComponent({
  name: 'UserModelForm',
  components: {FormHeader, ModuleComponent, ModuleView, FormTabPanel, DefaultFieldsTable, FormActionPanel},
  props: {
    incModel: Object,
    incValues: Object
  },
  emits: ['reload'],
  setup(props, {emit}) {
    const {
      onSave, onDelete, onBack, setValue, selectTab, getHeaderTitle,
      currentValues, tabs, modelTab, additionalModels
    } = useModelForm(props, emit)

    currentValues.value['role'] = currentValues.value['role'] || '[]'

    const checkedRoles = ref(currentValues.value['role'] ? JSON.parse(currentValues.value['role']) : [])
    const allRoles = ref()
    const userStore = useUserStore()

    onMounted(async () => {
      allRoles.value = await loadList('Vpanel', 'Role')
    })

    const onCheckRole = () => {
      currentValues.value = {...currentValues.value, 'role': JSON.stringify(checkedRoles.value)}
    }

    const onAuthAsUser = async () => {
      const result = await executeRequest('POST', 'vpanel', 'user/auth-as-user', {
        'user_id': currentValues.value['id']
      }, false)
      if (result) {
        await router.push({name: 'dashboard'})
        location.reload()
      }
    }

    return {
      currentValues,
      additionalModels,
      allRoles,
      checkedRoles,
      tabs,
      modelTab,
      onSave,
      onDelete,
      onBack,
      onAuthAsUser,
      setValue,
      onCheckRole,
      getHeaderTitle,
      selectTab
    }
  },
})
</script>

<style scoped>

</style>
