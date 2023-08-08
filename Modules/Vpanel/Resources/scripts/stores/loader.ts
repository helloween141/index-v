import {defineStore} from 'pinia'

export interface ThemeState {
    loading: boolean | false,
}

export const useLoaderStore = defineStore({
    id: 'loaderStore',
    state: (): ThemeState => ({
        loading: false
    }),
    actions: {
        showLoader() {
            this.loading = true
        },
        hideLoader() {
            this.loading = false
        }
    }
})
