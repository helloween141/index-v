import axios from "axios";
import {APIMessage} from "@/api/messages";
import {useToast} from "vue-toastification";

export const API_URL = '/api'

export const BASE_URL = API_URL + '/vpanel'

export const STORAGE_PATH = '/storage'

const toast = useToast()

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status == 401) {
            location.reload();
        } else {
            const errorMessage = error.response?.data?.error?.message

            if (errorMessage) {
                toast.error(errorMessage)
            } else {
                toast.error(APIMessage.ERROR_LOAD_DATA)
            }
        }
    }
);