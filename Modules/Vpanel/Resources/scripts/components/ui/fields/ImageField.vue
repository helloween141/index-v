<template>
    <div>
        <label :for="`dropzone-${ukey}`"
               class="flex flex-col w-1/2 text-center bg-gray-50 rounded-lg border-2 border-gray-300 border-dashed cursor-pointer dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
        >
            <div @dragover.prevent="onDrop"
                 @dragleave.prevent="onDrop"
                 @drop.prevent="onDrop"
                 class="pt-5 pb-6"
            >
                <div v-if="currentValue && currentValue.name">
                    <p class="text-sm text-gray-500 dark:text-gray-400 flex flex-col items-center">
                        <img class="w-48 rounded-lg" :src="getLink(currentValue.value)" alt=""/>
                        <span class="font-semibold">{{ currentValue.name }}</span>
                    </p>
                </div>
                <div v-else class="flex flex-col justify-center items-center">
                    <i class="fas fa-2x fa-image pb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Нажмите на иконку загрузки</span> или перенесите изображение
                    </p>
                </div>
            </div>
            <input :id="`dropzone-${ukey}`" type="file" class="hidden" ref="image" @change="onChange($event)"/>
        </label>
        <div class="mt-3" v-if="currentValue">
            <a
                    v-if="currentValue.id"
                    :href="getLink(currentValue.value)"
                    download
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-3">
                Скачать
            </a>
            <a v-if="currentValue.name"
               href="#"
               @click.prevent="onReset"
               class="cursor-pointer bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-3">
                Удалить
            </a>
        </div>
    </div>
</template>

<script>
import {defineComponent, ref} from "vue";
import {getLink} from "@/utils/utils";

export default defineComponent({
    name: 'ImageField',
    props: {
        field: Object,
        value: [String, Number, Object],
        ukey: String
    },
    emits: ['set-value'],
    setup(props, {emit}) {
        const currentValue = ref(props.value)

        const onDrop = ($event) => {
            handleInput($event.dataTransfer)
        }

        const onChange = ($event) => {
            handleInput($event.target)
        }

        const onReset = () => {
            currentValue.value = null
            emit('set-value', props.field.name, null)
        }

        const handleInput = (target) => {
            if (target && target.files) {
                currentValue.value = target.files[0]
                emit('set-value', props.field.name, currentValue.value)
            }
        }
        return {
            currentValue,
            onDrop,
            onChange,
            onReset,
            getLink
        }
    }
})
</script>

<style scoped>
</style>
