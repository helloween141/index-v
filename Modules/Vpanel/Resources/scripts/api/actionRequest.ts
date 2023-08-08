import axios from "axios";
import {API_URL} from "./config.js"
import {APIMessage} from "@/api/messages";
import {useToast} from "vue-toastification";

const toast = useToast()

export const executeRequest = async (method: string, moduleName: string, actionName: string, data: object = {}, showMessage: boolean = true) => {
    try {
        let response = null

        if (method === 'GET') {
            response = await axios.get(API_URL + `/${moduleName}/${actionName}`, {
                params: data
            })
        } else if (method === 'POST') {
            response = await axios.post(API_URL + `/${moduleName}/${actionName}`, data)
            if (showMessage && response) {
                toast.success(APIMessage.SUCCESS_SAVE)
            }
        } else if (method === 'DELETE') {
            response = await axios.delete(API_URL + `/${moduleName}/${actionName}`, data)
            if (showMessage && response) {
                toast.success(APIMessage.SUCCESS_DELETE)
            }
        }

        return response?.data;

    } catch (error) {
        console.error(error)
    }
}
