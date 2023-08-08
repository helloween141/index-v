<template>
  <nav v-if="pages && pages.total > 0" class="flex justify-between items-center pt-4">
    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
      Показывается <span class="font-semibold text-gray-900 dark:text-white">{{ pages.from }}-{{ pages.to }}</span>
      из <span class="font-semibold text-gray-900 dark:text-white">{{ pages.total }}</span>
    </span>

    <ul class="inline-flex items-center -space-x-px">
        <li v-for="(link, key) in pages.links">
          <a v-if="key === 0"
             href="#"
             @click.prevent="onClick(link.url)"
             :class="classes.prev"
          >
            <span class="sr-only">Назад</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd">
              </path>
            </svg>
          </a>

          <a v-else-if="key > 0 && key < pages.links.length - 1"
             href="#"
             @click.prevent="onClick(link.url)"
             :aria-current="link.active ? 'page' : ''"
             :class="link.active ? classes.active : classes.default
          ">
            {{ link.label }}
          </a>

          <a v-else
             href="#"
             @click.prevent="onClick(link.url)"
             :class="classes.next"
          >
            <span class="sr-only">Вперед</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd">
              </path>
            </svg>
          </a>
        </li>
      </ul>
  </nav>
</template>

<script>
export default {
  name: 'Pagination',
  data() {
    return {
      classes: {
        'default': 'block py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white',
        'active': 'block z-10 py-2 px-3 leading-tight text-blue-600 bg-blue-50 border border-blue-300 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white',
        'next': 'block py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white',
        'prev': 'block py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'
      }
    }
  },
  props: {
    pages: Object
  },
  methods: {
    onClick(url) {
      if (url) {
        const queryParams = (new URL(url)).searchParams;
        this.$emit('set-page', queryParams.get('page'))
      }
    }
  }
}
</script>

<style scoped>

</style>
