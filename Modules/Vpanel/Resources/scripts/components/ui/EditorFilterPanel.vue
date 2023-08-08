<template>
  <div
      class="mb-5 p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
    <div class="flex flex-wrap">
      <div v-for="(field, index) in fields"
           :key="index"
           class="mb-3 pr-3"
           :class="field.type === 'bool' ? 'basis-1/7' : 'basis-1/3'"
      >
        <StringFilterField
            v-if="field.type === 'string'"
            :field="field"
            v-model:value="values[field.name]"
            @set-filter="setFilter"
        />

        <DateRangeFilterField
            v-if="field.type === 'date'"
            :field="field"
            v-model:value="values[field.name]"
            @set-filter="setFilter"
        />

        <NumberRangeFilterField
            v-if="field.type === 'int' || field.type === 'float'"
            :field="field"
            v-model:value="values[field.name]"
            @set-filter="setFilter"
        />

        <BoolFilterField
            v-if="field.type === 'bool'"
            :field="field"
            v-model:value="values[field.name]"
            @set-filter="setFilter"
        />

        <PointerFilterField
            v-if="field.type === 'pointer'"
            :field="field"
            v-model:value="values[field.name]"
            @set-filter="setFilter"
        />

        <SelectFilterField
            v-if="field.type === 'select'"
            :field="field"
            v-model:value="values[field.name]"
            @set-filter="setFilter"
        />
      </div>
    </div>

    <div class="flex justify-end">
      <button
          @click.prevent="onResetFilter"
          class="bg-red-500 hover:bg-red-700 text-gray-800 font-bold py-2 px-4 rounded">
        <span class="text-white">Сбросить</span>
      </button>

      <button
          @click.prevent="onApplyFilter"
          class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
        <span class="text-white">Применить</span>
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import {defineComponent, ref} from "vue";
import StringFilterField from "@/components/ui/filters/StringFilterField.vue";
import DateRangeFilterField from "@/components/ui/filters/DateRangeFilterField.vue";
import BoolFilterField from "@/components/ui/filters/BoolFilterField.vue";
import NumberRangeFilterField from "@/components/ui/filters/NumberRangeFilterField.vue";
import PointerFilterField from "@/components/ui/filters/PointerFilterField.vue";
import SelectFilterField from "@/components/ui/filters/SelectFilterField.vue";
import {useRoute} from "vue-router";

export default defineComponent({
  name: 'EditorFilterPanel',
  components: {
    SelectFilterField,
    PointerFilterField,
    NumberRangeFilterField,
    BoolFilterField,
    DateRangeFilterField,
    StringFilterField
  },
  props: {
    fields: Object
  },
  emits: ['on-filter'],
  setup(props, {emit}) {
    const route = useRoute()
    let filter = route.query.f ? JSON.parse(route.query.f.toString()) : {}
    const values = ref(filter)

    const setFilter = (fieldFilter, fieldName) => {
      values.value[fieldName] = fieldFilter[fieldName]
      filter = {...filter, ...fieldFilter}
      Object.keys(filter).forEach((k) => (!filter[k] && filter[k] !== 0) && delete filter[k]);
    }

    const onApplyFilter = () => {
      emit('on-filter', filter)
    }

    const onResetFilter = () => {
      Object.keys(filter).forEach((k) => k !== 'show_only' && delete filter[k]);
      values.value = {}
      emit('on-filter', filter)
    }

    return {
      filter,
      values,
      onApplyFilter,
      onResetFilter,
      setFilter
    }
  }
})
</script>
