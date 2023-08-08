import {useToast} from "vue-toastification";
import {useRoute} from "vue-router";
import {
    getAdditionalModels,
    getIdentifyFieldValue,
    getModelTabs,
    getRouteParameters,
    prepareFormData,
    setDefaultFieldsValues,
    validateFields
} from "@/utils/utils";
import {onMounted, ref} from "vue";
import router from "@/router";
import {deleteRecord, restoreRecord, saveRecord} from "@/api/actionForm";
import {APIMessage} from "@/api/messages";

export function useModelForm(props, emit) {
    const toast = useToast()
    const route = useRoute()
    const {moduleName, modelName, recordId, query} = getRouteParameters(route)
    const defaultValues = (!recordId && query.defaults) ? JSON.parse(query.defaults) : null

    const currentValues = ref(setDefaultFieldsValues(props.incModel.fields, defaultValues || props.incValues))
    const additionalModels = ref(getAdditionalModels(props.incModel.childModels, currentValues.value['id']))
    const tabs = ref(getModelTabs(props.incModel.childModels, currentValues.value['id']))
    const modelTab = ref()

    onMounted(() => {
        setActiveTab(route.query.tab || '')
        let query = Object.assign({}, route.query);
        ['tab', 'defaults', 'f'].forEach(key => {
            if (query[key]) {
                delete query[key]
            }
        })
        currentValues.value = {...currentValues.value, ...query}
    })

    const selectTab = (selectedTab: any) => {
        setActiveTab(selectedTab.model)

        if (selectedTab.model) {
            router.push({
                path: route.path,
                query: {
                    tab: selectedTab.model
                }
            })
        } else {
            router.replace({query: null})
        }
    }

    const setActiveTab = (activeTab) => {
        tabs.value.forEach((tab, index) => {
            if (tab.model === activeTab) {
                tabs.value[index].active = true
                modelTab.value = tab.model ? tab : null
            } else {
                tabs.value[index].active = false
            }
        })
    }

    const onSave = async (withExit: boolean = false) => {
        const validationErrors = validateFields(props.incModel.fields, currentValues.value)

        if (validationErrors.length === 0) {
            const formData = prepareFormData(currentValues.value)

            const id = await saveRecord(
                moduleName,
                modelName,
                !props.incModel.single ? recordId : 1,
                formData
            )

            if (id) {
                toast.success(APIMessage.SUCCESS_SAVE)
                if (withExit) {
                    await onBack()
                } else if (id === recordId) {
                    emit('reload', moduleName, modelName, recordId)
                } else if (!props.incModel.single) {
                    await router.push({name: 'module', params: {'module': moduleName, 'model': modelName, id}})
                }
            }
        } else {
            toast.error('Заполните поля: \n' + validationErrors.join('\n'))
        }
    }

    const onDelete = async () => {
        const result = await deleteRecord(moduleName, modelName, recordId)
        if (result) {
            toast.success(APIMessage.SUCCESS_DELETE);
            await router.push({name: 'module', params: {'module': moduleName, 'model': modelName}})
        }
    }

    const onRestore = async () => {
        const result = await restoreRecord(moduleName, modelName, recordId)
        if (result) {
            toast.success(APIMessage.SUCCESS_RESTORE);
            emit('reload', moduleName, modelName, recordId)
        }
    }

    const onBack = async () => {
        const masterModel = props.incModel.masterModel
        if (masterModel) {
            const masterModelName = masterModel.model.split('\\').pop()
            const masterId = props.incValues[masterModel.relationKey] || route.query[masterModel.relationKey]

            await router.push({
                name: 'module',
                params: {'module': moduleName, 'model': masterModelName, 'id': masterId},
                query: {'tab': modelName}
            })
        } else {
            await router.push({
                name: 'module',
                params: {'module': moduleName, 'model': modelName}
            })
        }
    }

    const setValue = (fieldName: string, fieldValue: any) => {
        currentValues.value[fieldName] = fieldValue
    }

    const getHeaderTitle = () => {
        return getIdentifyFieldValue(props.incModel.fields, props.incValues) || props.incModel.accusativeRecordTitle
    }

    return {
        onSave,
        onDelete,
        onRestore,
        onBack,
        setValue,
        selectTab,
        getHeaderTitle,
        moduleName,
        modelName,
        recordId,
        query,
        currentValues,
        tabs,
        modelTab,
        additionalModels
    }
}
