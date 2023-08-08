<template>
  <aside
      class="w-96 bg-white min-h-screen border-r dark:border-gray-700 dark:border-primary-darker dark:bg-gray-900 md:block"
      aria-label="Sidebar">
    <div class="overflow-y-auto h-full py-4 px-3 bg-gray-50 rounded dark:bg-gray-800">

      <div class="flex justify-between items-center pb-4">
        <Logo/>
        <ThemeToggleButton @on-click="onToggle"/>
      </div>

      <div class="w-full border-t dark:border-gray-700"></div>

      <div class="pb-4 mt-3">
        <ul v-if="menu">
          <li v-for="item in menu" :key="item.key">
            <div v-if="item.list">
              <button @click="onCollapse(item)" type="button"
                      class="flex items-center p-2 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                <i :class="[item.icon, 'text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white dark:text-gray-400']"></i>
                <span class="flex-1 ml-3 text-left whitespace-nowrap" data-sidebar-toggle-item>{{ item.title }}</span>
                <i class="fa-solid" :class="item.active ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </button>
              <ul v-show="item.active">
                <li v-for="(subItem, key) in item.list" :key="key">
                  <RouterLink :to="{ name: 'module', params: { module: item.module, model: subItem.model }}"
                              class="flex items-center p-2 pl-11 w-full text-base font-normal text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                  >
                    <i v-if="subItem.icon"
                       :class="[subItem.icon, 'text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white dark:text-gray-400']"></i>
                    <span>{{ subItem.title }}</span>
                  </RouterLink>
                </li>
              </ul>
            </div>
            <div v-else>
              <RouterLink :to="{ name: 'module', params: { module: item.module, model: item.model }}"
                          class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group"
              >
                <i :class="[item.icon, 'text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white dark:text-gray-400']"></i>
                <span class="ml-3">{{ item.title }}</span>
              </RouterLink>
            </div>
          </li>
        </ul>
      </div>

      <div class="w-full border-b dark:border-gray-700"></div>

      <div class="mt-3">
        <ul>
          <li>
            <RouterLink to="/admin/profile"
                        class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
              <img v-if="userStore.user.avatar?.value"
                   :src="getLink(userStore.user.avatar.value)"
                   class="w-9 h-9 rounded-full"
              />
              <img v-else
                   src="@/assets/images/default-avatar.png"
                   class="w-9 h-9 rounded-full"
              />
              <span class="ml-3">{{ userStore.user.name }}</span>
            </RouterLink>
          </li>
          <a v-if="userStore.user.admin_id" @click.prevent="onAuthAsAdmin"
             class="flex items-center p-2 cursor-pointer text-base font-normal text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
            <i class="fa-solid fa-sign-in text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white dark:text-gray-400"></i>
            <span class="ml-3">Вернуться как администратор</span>
          </a>
          <a href="/admin/logout"
             class="flex items-center p-2 text-base font-normal text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
            <i class="fa-solid fa-sign-out text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white dark:text-gray-400"></i>
            <span class="ml-3">Выйти</span>
          </a>
        </ul>
      </div>

    </div>
  </aside>
</template>

<script lang="ts">
import {defineComponent, onMounted, ref} from "vue"
import {useUserStore} from "@/stores/user";
import ThemeToggleButton from "@/components/ui/sidebar/ThemeToggleButton.vue";
import Logo from "@/components/ui/sidebar/Logo.vue";
import {getLink, getRouteParameters} from "@/utils/utils";
import {useRoute} from "vue-router";
import {executeRequest} from "@/api/actionRequest";
import router from "@/router";

export default defineComponent({
  name: 'Sidebar',
  components: {Logo, ThemeToggleButton},
  setup(props, {emit}) {
    const userStore = useUserStore()
    const route = useRoute()
    const menu = ref()

    onMounted(async () => {
      menu.value = await executeRequest('GET', 'vpanel', 'menu')
      if (menu.value) {
        const {moduleName} = getRouteParameters(route)
        for (const [key, item] of Object.entries(menu.value)) {
          if (item.list && item.module === moduleName) {
            item.active = true
          }
        }
      }
    })

    const onCollapse = (item) => {
      item.active = !item.active
    }

    const onToggle = () => {
      emit('on-toggle-theme')
    }

    const onAuthAsAdmin = async () => {
      const result = await executeRequest('POST', 'vpanel', 'user/auth-as-admin')
      if (result) {
        await router.push({name: 'dashboard'})
        location.reload()
      }
    }

    return {
      menu,
      userStore,
      getLink,
      onCollapse,
      onToggle,
      onAuthAsAdmin
    }
  },
})
</script>

<style scoped>
</style>
