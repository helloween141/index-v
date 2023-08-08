<template>
  <main>
    <div class="flex">
      <Sidebar
          @on-toggle-theme="onToggleTheme"
      />
      <RouterView class="w-full p-5"/>
    </div>
    <ModalsContainer/>
  </main>
</template>

<script lang="ts">
import {defineComponent, onMounted} from "vue";
import {useThemeStore} from "@/stores/theme";
import Sidebar from "@/components/ui/sidebar/Sidebar.vue";
import {useUserStore} from "@/stores/user";
import {useLoaderStore} from "@/stores/loader";

export default defineComponent({
  components: {Sidebar},
  setup() {
    const themeStore = useThemeStore()
    const userStore = useUserStore()
    const loaderStore = useLoaderStore()

    onMounted(async () => {
      await userStore.getUserData()
    });

    const onToggleTheme = () => {
      themeStore.switchTheme()
      applyTheme()
    }

    const applyTheme = () => {
      if (themeStore.isDark) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    }

    applyTheme()

    return {
      onToggleTheme,
      themeStore,
      userStore,
      loaderStore
    }
  }
})
</script>

<style>
</style>
