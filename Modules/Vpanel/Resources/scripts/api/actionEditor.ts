import axios from "axios";
import {BASE_URL} from "./config.js"

export const loadInterface = async (moduleName: string, modelName: string, id: number = 0, params: object = {}) => {
    try {
        const response = await axios.get(BASE_URL + `/${moduleName}/${modelName}/interface${id ? '/' + id : ''}`, {
            params
        })
        return response?.data
    } catch (error) {
        console.error(error)
    }
}

export const loadList = async (moduleName: string, modelName: string, params: object = {}) => {
    try {
        const response = await axios.get(BASE_URL + `/${moduleName}/${modelName}/list`, {
            params
        })
        return response?.data
    } catch (error) {
        console.error(error)
    }
}

export const sortList = async (moduleName: string, modelName: string, recordsIds: any) => {
    try {
        const response = await axios.post(BASE_URL + `/${moduleName}/${modelName}/sort`, {
            records_ids: recordsIds
        })
        return response?.data
    } catch (error) {
        console.error(error)
    }
}

export const loadPointer = async (moduleName: string, modelName: string, params: object = {}) => {
    try {
        const response = await axios.get(BASE_URL + `/${moduleName}/${modelName}/pointer`, {
            params
        })
        return response?.data
    } catch (error) {
        console.error(error)
    }
}
