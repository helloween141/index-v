import {ref} from 'vue'
import {useRoute} from "vue-router";
import {getFieldsForFilter, getRouteParameters, hasOnlyKey} from "@/utils/utils";
import router from "@/router";
import {loadList, sortList} from "@/api/actionEditor";

export function useModelEditor(props, emit) {
    const route = useRoute()
    const filter = route.query.f ? JSON.parse(<string>route.query.f) : {}
    const currentValues = ref(props.incValues)
    const showFilterPanel = ref(Object.keys(filter).length > 0 && !hasOnlyKey('show_only', filter))
    const {moduleName, modelName} = getRouteParameters(route)

    const selectRecord = (record: any) => {
        if (props.isModal) {
            emit('select-record', record)
        } else {
            router.push({
                name: 'module', params: {
                    module: props.incPathData.module,
                    model: props.incPathData.model,
                    id: record.id
                }
            })
        }
    }

    const createRecord = () => {
        let query = props.incPathData.filter
        router.push({
            name: 'module',
            params: {
                module: props.incPathData.module,
                model: props.incPathData.model,
                id: 0
            },
            query
        })
    }

    const applySearch = async (search) => {
        if (props.isModal) {
            currentValues.value = await loadList(props.incPathData.module, props.incPathData.model, {
                page: 1,
                search
            })
        } else {
            currentValues.value = await loadList(moduleName, modelName, {
                page: 1,
                search
            })
        }
    }

    const applyFilter = async (filter) => {
        if (props.isModal) {
            currentValues.value = await loadList(props.incPathData.module, props.incPathData.model, {
                page: 1,
                filter: JSON.stringify(filter)
            })
        } else {
            (Object.keys(filter).length === 0)
                ? await router.push({path: route.path, query: {tab: route.query.tab}})
                : await router.push({path: route.path, query: {f: JSON.stringify(filter), tab: route.query.tab}})
            emit('reload', props.incPathData.module || moduleName, props.incPathData.model || modelName)
        }
    }

    const setPage = async (page: number) => {
        if (props.isModal) {
            currentValues.value = await loadList(props.incPathData.module, props.incPathData.model, {page})
        } else {
            await router.push({path: route.fullPath, query: {...route.query, page}})
            emit('reload', props.incPathData.module || moduleName, props.incPathData.model || modelName)
        }
    }

    const changeSort = async (sortData) => {
        await sortList(props.incPathData.module, props.incPathData.model, sortData)
    }

    const filterFields = getFieldsForFilter(props.incModel?.fields)

    const toggleFilterPanel = (value) => {
        showFilterPanel.value = value
    }

    return {
        selectRecord,
        createRecord,
        applySearch,
        setPage,
        changeSort,
        toggleFilterPanel,
        applyFilter,
        showFilterPanel,
        currentValues,
        filterFields,
        moduleName,
        modelName
    }
}
