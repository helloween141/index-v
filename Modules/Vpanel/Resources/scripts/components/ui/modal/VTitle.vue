<template>
  <h1 class="dark:text-white text-2xl">
    {{text}}
  </h1>
</template>

<script lang="ts">
export default {
  props: {
    text: { type: String, required: true }
  },
}
</script>