<template>
  <div v-if="!loading" class="overflow-x-auto">
    <component
        :is="targetComponent"
        :inc-model="modelInterface"
        :inc-values="modelValues"
        :inc-path-data="pathData"
        :is-child="isChild"
        @reload="loadModule"
    />
  </div>
  <div v-else>
    <Spinner :h-screen="true"/>
  </div>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent, ref, shallowRef, watch} from "vue";
import {useRoute} from "vue-router";
import {loadInterface, loadList} from "@/api/actionEditor";
import {loadRecord} from "@/api/actionForm";
import Spinner from "@/components/ui/Spinner.vue";

export default defineComponent({
  name: 'ModuleComponent',
  components: {Spinner},
  props: {
    module: String,
    model: String,
    recordId: Number,
    isChild: Boolean,
    defaultFilter: {
      type: Object,
      default: null
    }
  },
  setup(props) {
    let loading = ref(true)
    const route = useRoute()
    const modelInterface = ref()
    const modelValues = ref()
    const targetComponent = shallowRef()
    const pathData = ref()

    const loadModule = async (moduleName, modelName, recordId) => {
      if (moduleName && modelName) {
        modelInterface.value = await loadInterface(moduleName, modelName, recordId)

        if (modelInterface.value) {
          if (recordId >= 0 || modelInterface.value?.single) {
            modelValues.value = await loadRecord(moduleName, modelName, recordId)
            const formComponent = modelInterface.value?.formComponent
            targetComponent.value = formComponent ? loadCustomComponent(formComponent, moduleName, 'forms') : loadFormComponent()
          } else {
            let filter = props.defaultFilter
            let page = 1

            if (route.query.f) {
              filter = JSON.parse((route.query.f).toString())
            }

            if (route.query.page) {
              page = parseInt(route.query.page.toString())
            }

            modelValues.value = await loadList(moduleName, modelName, {
              page,
              filter: JSON.stringify(filter),
            })
            const editorComponent = modelInterface.value?.editorComponent
            targetComponent.value = editorComponent ? loadCustomComponent(editorComponent, moduleName, 'editors') : loadEditorComponent()
          }
        }
      }
      window.scrollTo(0, 0)
    }

    const loadCustomComponent = (component: string, moduleName: string, type: string) => {
      return defineAsyncComponent(() => import(
              /* @vite-ignore */
          '../../../../' + moduleName + '/Resources/scripts/components/' + type + '/' + component + '.vue')
      )
    }

    const loadEditorComponent = () => {
      return defineAsyncComponent(() => import(
          /* @vite-ignore */
          '../components/editors/DefaultModelEditor.vue')
      )
    }

    const loadFormComponent = () => {
      return defineAsyncComponent(() => import(
          /* @vite-ignore */
          '../components/forms/DefaultModelForm.vue')
      )
    }

    watch(props, async (to) => {
      pathData.value = {
        module: props.module,
        model: props.model,
        filter: props.defaultFilter
      }
      loading.value = true
      await loadModule(props.module, props.model, props.recordId)
      loading.value = false
    }, {
      flush: 'pre',
      immediate: true,
      deep: true
    })

    return {
      targetComponent,
      modelInterface,
      modelValues,
      pathData,
      loading,
      loadModule
    }
  }
})
</script>

<style scoped>
</style>
