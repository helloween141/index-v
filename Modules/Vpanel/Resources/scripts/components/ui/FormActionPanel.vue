<template>
  <div>
    <button v-show="model.showBackButton" @click.prevent="onBack"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
      <span class="text-white">
        <i class="fa-solid fa-arrow-rotate-back"></i>
        Назад
      </span>
    </button>
    <button v-show="model.showDeleteButton && !model.single && record.id" @click.prevent="onDelete"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
      <span class="text-white">
        Удалить
      </span>
    </button>
    <button v-show="!!record.deleted_at" @click.prevent="onRestore"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
      <span class="text-white">
        Восстановить
      </span>
    </button>
    <button v-show="!record.deleted_at && model.showSaveButton" type="submit" @click.prevent="onSave(false)"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
      <span class="text-white">
        Сохранить
      </span>
    </button>
    <button v-show="!record.deleted_at && model.showSaveButton" type="submit" @click.prevent="onSave(true)"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
      <span class="text-white">
        Сохранить и выйти
      </span>
    </button>
  </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
  name: 'FormActionPanel',
  props: {
    model: Object,
    record: Object
  },
  emits: ['on-save', 'on-delete', 'on-back', 'on-restore'],
  setup(props, {emit}) {
    const onSave = (withExit: boolean) => {
      emit('on-save', withExit)
    }

    const onDelete = () => {
      if (!props.model.softDelete || !!props.record.deleted_at) {
        if (confirm('Запись будет удалена безвозвратно. Продолжить?')) {
          emit('on-delete')
        }
      } else {
        if (confirm('Запись будет помечена как удаленная. Продолжить?')) {
          emit('on-delete')
        }
      }
    }

    const onRestore = () => {
      emit('on-restore')
    }

    const onBack = () => {
      emit('on-back')
    }

    return {
      onSave,
      onDelete,
      onRestore,
      onBack
    }
  }
})
</script>
