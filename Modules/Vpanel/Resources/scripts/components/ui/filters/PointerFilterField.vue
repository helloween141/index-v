<template>
    <div>
        <span class="dark:text-white mb-3">{{ field.title }}</span>

        <v-select
            v-model="currentValue"
            :label="identifyLabel"
            :options="options"
            @update:modelValue="handleInput"
            class="py-2 bg-gray-200 text-gray-700 rounded leading-tight focus:outline-none focus:bg-white focus:border-purple-500 custom-fx"
            :multiple="isMultiple"
            :push-tags="isMultiple"
        >
            <template v-slot:no-options>Записи не найдены!</template>
        </v-select>
    </div>
</template>

<script lang="ts">
import {defineComponent, onMounted, ref, watch} from "vue";
import {parseModelPath} from "@/utils/utils";
import {loadPointer} from "@/api/actionEditor";

export default defineComponent({
    name: 'PointerFilterField',
    props: {
        field: Object,
        value: Array
    },
    emits: ['set-filter'],
    setup(props, {emit}) {
        const options = ref([])
        const identifyLabel = ref(props.field.identify || 'name')
        const currentValue = ref([])
        const isMultiple = !!props.field?.filterConfig?.multiple_selection

        onMounted(async () => {
            const pointerPath = parseModelPath(props.field.model)
            const pointerData = await loadPointer(pointerPath.module, pointerPath.model)

            if (pointerData) {
                options.value = pointerData.values
                identifyLabel.value = pointerData.identifyLabel

                if (options.value && Array.isArray(props.value)) {
                    options.value.forEach(option => {
                        if (props.value.includes(option.id)) {
                            currentValue.value.push({
                                id: option.id,
                                name: option.name
                            })
                        }
                    })
                }
            }

        })

        watch(() => props.value, (current, previous) => {
            if (!current) {
                currentValue.value = []
            }
        })

        const handleInput = () => {
            if (!isMultiple && currentValue.value) {
                currentValue.value = [currentValue.value]
            }
            const result = currentValue.value ? currentValue.value.map(item => item.id) : null
            emit('set-filter', {[props.field.name]: result}, props.field.name)
        }

        return {
            options,
            identifyLabel,
            currentValue,
            isMultiple,
            handleInput
        }
    }
})
</script>

<style scoped>
.custom-fx {
    font-size: 1rem;
    padding: 0.17rem 0.17rem;
}
</style>
