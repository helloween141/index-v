import {defineStore} from 'pinia'
import axios from "axios";
import {BASE_URL} from "@/api/config";

export interface User {
    id: number | 0,
    name: string | '',
    email: string | '',
    admin_id?: number | 0
}

export interface UserState {
    auth: boolean | false,
    user: User
}

export const useUserStore = defineStore({
    id: 'userStore',
    state: (): UserState => <UserState>({
        auth: false,
        user: {}
    }),
    actions: {
        async getUserData() {
            try {
                const userInfo = await axios.get(BASE_URL + `/user/info`)
                this.auth = true
                this.user = userInfo.data
            } catch (error) {
                this.auth = false
                this.user = {}
                console.error(error)
            }
        }
    }
})
