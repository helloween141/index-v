<template>
  <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <tbody>
    <tr
        v-for="(field, key) in fields"
        v-show="field.inForm"
        :key="key"
        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 odd:bg-white odd:bg-gray-50"
    >
      <th scope="row" class="px-4 py-4 whitespace-nowrap w-48">
        <div class="flex">
            <span class="font-medium text-gray-900 dark:text-white pr-1">
              {{ field.title }}
            </span>
          <Tooltip
              v-if="field.tooltip"
              :text="field.tooltip"
              :style="'top: -37px; left: -10px;'"
          >
            <i class="fas fa-question-circle cursor-pointer"/>
          </Tooltip>
        </div>
      </th>
      <td class="px-4 py-4">
        <StringField
            v-if="field.type === 'string'"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <PasswordField
            v-if="field.type === 'password'"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <NumberField
            v-if="field.type === 'int' || field.type === 'float'"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <TextField
            v-else-if="field.type === 'text'"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <HtmlField
            v-else-if="field.type === 'html'"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <SelectField
            v-else-if="field.type === 'select'"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <PointerField
            v-else-if="field.type === 'pointer' && !field.isModal"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <PointerModalField
            v-else-if="field.type === 'pointer' && field.isModal"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <BoolField
            v-else-if="field.type === 'bool'"
            :field="field"
            :value="!!values[field.name]"
            @set-value="setValue"
        />

        <DateField
            v-else-if="field.type === 'date'"
            :field="field"
            :value="values[field.name]"
            @set-value="setValue"
        />

        <ImageField
            v-else-if="field.type === 'image'"
            :field="field"
            :value="values[field.name]"
            :ukey="`${field.type}${key}`"
            @set-value="setValue"
        />

        <FileField
            v-else-if="field.type === 'file'"
            :field="field"
            :value="values[field.name]"
            :ukey="`${field.type}${key}`"
            @set-value="setValue"
        />

      </td>
    </tr>
    </tbody>
  </table>
</template>

<script lang="ts">
import {defineComponent, watch} from "vue";
import StringField from "@/components/ui/fields/StringField.vue";
import TextField from "@/components/ui/fields/TextField.vue";
import HtmlField from "@/components/ui/fields/HtmlField.vue";
import SelectField from "@/components/ui/fields/SelectField.vue";
import PointerField from "@/components/ui/fields/PointerField.vue";
import DateField from "@/components/ui/fields/DateField.vue";
import BoolField from "@/components/ui/fields/BoolField.vue";
import NumberField from "@/components/ui/fields/NumberField.vue";
import PointerModalField from "@/components/ui/fields/PointerModalField.vue";
import FileField from "@/components/ui/fields/FileField.vue";
import Tooltip from "@/components/ui/Tooltip.vue";
import PasswordField from "@/components/ui/fields/PasswordField.vue";
import {convertFilterCondition, getConditions} from "@/utils/utils";
import ImageField from "@/components/ui/fields/ImageField.vue";

export default defineComponent({
  name: 'DefaultFieldsTable',
  components: {
    ImageField, PasswordField, Tooltip, FileField, PointerModalField, NumberField, StringField, BoolField, DateField,
    PointerField, SelectField, HtmlField, TextField
  },
  props: {
    fields: Object,
    values: Object
  },
  emits: ['set-value'],
  setup(props, {emit}) {
    const setValue = (fieldName, fieldValue) => {
      emit('set-value', fieldName, fieldValue)
    }

    const {showConditions, filterConditions} = getConditions(props.fields)

    watch(() => props.values, (record, previous) => {
      for (const [key, field] of Object.entries(props.fields)) {
        if (showConditions[field.name]) {
          props.fields[key].inForm = eval(showConditions[field.name])
        } else if (filterConditions[field.name]) {
          props.fields[key].queryString = convertFilterCondition(filterConditions[field.name], record)
        }
      }
    }, {flush: 'pre', immediate: true, deep: true})

    return {
      setValue
    }
  }
})
</script>

<style scoped>

</style>
