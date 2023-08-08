import axios from "axios";
import {BASE_URL} from "./config.js"

export const loadRecord = async (moduleName: string, modelName: string, id: number) => {
    try {
        const response = await axios.get(BASE_URL + `/${moduleName}/${modelName}/record/${id || 0}`)
        return response?.data
    } catch (error) {
        console.error(error)
    }
}

export const saveRecord = async (moduleName: string, modelName: string, id: number, formData: object) => {
    try {
        const response = await axios.post(BASE_URL + `/${moduleName}/${modelName}/save/${id}`, formData)
        return response?.data
    } catch (error) {
        console.error(error)
    }
}

export const deleteRecord = async (moduleName: string, modelName: string, id: number) => {
    try {
        const response = await axios.delete(BASE_URL + `/${moduleName}/${modelName}/delete/${id}`)
        return response?.data
    } catch (error) {
        console.error(error)
    }
}

export const restoreRecord = async (moduleName: string, modelName: string, id: number) => {
    try {
        const response = await axios.patch(BASE_URL + `/${moduleName}/${modelName}/restore/${id}`)
        return response?.data
    } catch (error) {
        console.error(error)
    }
}
