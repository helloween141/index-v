import moment from 'moment';
import {STORAGE_PATH} from "@/api/config";
import Mustache from 'mustache'

export const prepareFormData = (values: object): FormData => {
    const data = new FormData()
    Object.keys(values).forEach(key => {
        let item = values[key]
        if (typeof item === 'object' && item !== null && item.hasOwnProperty('id')) {
            data.append(key, item.id)
        } else {
            data.append(key, item)
        }
    })
    return data
}

export const getRouteParameters = (route) => {
    return {
        moduleName: (route.params.module || ''),
        modelName: (route.params.model || ''),
        recordId: parseInt(route.params?.id || ''),
        query: route.query
    }
}

export const getFieldsForFilter = (fields: any) => {
    if (fields) {
        return fields.filter(field => field.inFilter)
    }
    return []
}

export const getPlaceholderForSearch = (fields: any) => {
    if (fields) {
        return fields.filter(field => field.inSearch).map(field => field.title.toLowerCase()).join(', ')
    }
    return ''
}

export const getIdentifyFieldValue = (fields: any, values: any) => {
    if (fields) {
        const identifyField = fields.find(field => field.identify)
        if (identifyField) {
            return values[identifyField['name']] || ''
        }
    }

    return ''
}

export const getHeadersForEditorTable = (fields: any, groups: any = []) => {
    const result = []
    if (fields) {
        fields.forEach(field => {
            if (field.inEditor) {
                if (field.group) {
                    const groupInfo = getGroupByField(groups, field)
                    if (!result.includes(groupInfo.title)) {
                        result.push(groupInfo.title)
                    }
                } else if (!result.includes(field.title)) {
                    result.push(field.title)
                }
            }
        })
    }
    return result
}

export const getRowsForEditorTable = (fields: any, data: any, groups: any = []) => {
    const result = []
    if (data) {
        data.forEach(item => {
                let groupFields = []
                let groupInfo = null
                let obj = {id: item['id'], deleted_at: item['deleted_at']}
                fields.forEach(field => {
                    if (field.inEditor) {
                        const fName = field['name']
                        const fValue = item[field['name']]
                        let mergeValue = {}
                        if (field.type === 'bool') {
                            mergeValue = {[fName]: fValue ? 'Да' : 'Нет'}
                        } else if (field.type === 'date') {
                            mergeValue = {[fName]: field.withTime ? formatDateTime(fValue) : formatDate(fValue)}
                        } else if (field.type === 'select' && fValue) {
                            mergeValue = {[fName]: field.options[fValue]}
                        } else if (field.type === 'image' && fValue) {
                            mergeValue = {
                                [fName]: {
                                    'src': fValue.value ? getLink(fValue.value) : '',
                                    'isImage': true
                                }
                            }
                        } else if (field.type === 'pointer' && fValue) {
                            const identifyKey = Object.keys(fValue)[1] || ''
                            mergeValue = {[fName]: fValue[identifyKey]}
                        } else {
                            mergeValue = {[fName]: (fValue !== null) ? fValue : ''}
                        }

                        if (field.group) {
                            groupInfo = getGroupByField(groups, field)
                            if (groupInfo) {
                                groupFields = {...groupFields, ...mergeValue}
                                if (!obj[groupInfo.name]) {
                                    obj = {...obj, [groupInfo.name]: {}}
                                }
                            }
                        } else {
                            obj = {...obj, ...mergeValue}
                        }
                    }
                })

                if (item.children) {
                    obj = {...obj, ...{children: item.children}}
                }

                if (groupInfo) {
                    groups.forEach(group => {
                        obj[group.name] = Mustache.render(group.template, groupFields)
                    })
                }

                result.push(obj)
            }
        )
    }
    return result
}

export const setDefaultFieldsValues = (fields: any, data: any) => {
    let result = {}

    if (data) {
        if (data.id) {
            result = {...result, ...{id: data.id}}
        }

        fields.forEach(field => {
            result = {...result, ...{[field.name]: (data[field.name] ? data[field.name] : field.defaultValue)}}
        })
    }
    return result
}

export const formatDate = (date): string => {
    return moment(date).format('DD.MM.YYYY')
}

export const formatDateTime = (date): string => {
    return moment(date).format('DD.MM.YYYY HH:mm')
}

export const getLink = (value): string => {
    return value ? `${STORAGE_PATH}/${value}` : ''
}

export const parseModelPath = (path: string) => {
    const prsr = path.split('\\')

    return {
        module: prsr[1],
        model: prsr[3]
    }
}

export const getModelTabs = (childModels: any, masterId: Number) => {
    const tabs = []
    if (childModels) {
        for (const childModel of childModels) {
            const path = parseModelPath(childModel.model)
            if (childModel.tab) {
                tabs.push({
                    title: childModel.title,
                    module: path.module,
                    model: path.model,
                    filter: {
                        [childModel.relation_key]: masterId
                    }
                })
            }
        }
        if (tabs.length > 0) {
            tabs.unshift({
                title: 'Основная информация',
                module: '',
                model: '',
                active: true
            })
        }
    }
    return tabs
}

export const getAdditionalModels = (childModels: any, masterId: Number) => {
    const result = []
    if (childModels) {
        for (const childModel of childModels) {
            const path = parseModelPath(childModel.model)
            result.push({
                module: path.module,
                model: path.model,
                filter: {
                    [childModel.relation_key]: masterId
                }
            })
        }
    }
    return result
}

export const getConditions = (fields: any) => {
    let showConditions = {}
    let filterConditions = {}
    if (fields) {
        fields.forEach(field => {
            if (field.showCondition) {
                showConditions = {...showConditions, [field.name]: field.showCondition}
            } else if (field.filterCondition) {
                filterConditions = {...filterConditions, [field.name]: field.filterCondition}
            }
        })
    }
    return {showConditions, filterConditions}
}

export const convertFilterCondition = (condition: string, record: any) => {
    const matchStr = condition.match(/\*(.*?)\*/g)
    if (matchStr) {
        matchStr.forEach(ms => {
            const clearMs = ms.replace(/\*/g, '')
            condition = condition.replace(ms, record[clearMs])
        });
    }
    return condition
}

export const validateFields = (fields: any, values: any) => {
    const validationErrors = []
    if (fields) {
        fields.forEach(field => {
            if (field.required) {
                let requiredValue = values[field.name]
                if (typeof values[field.name] === 'object' && requiredValue !== null) {
                    requiredValue = requiredValue.id
                }

                const requiredDependencies = field.requiredDependencies
                if (requiredDependencies && Object.keys(requiredDependencies).length > 0) {
                    Object.keys(requiredDependencies).forEach(key => {
                        if (!requiredValue && values[key] === requiredDependencies[key]) {
                            validationErrors.push(field.title)
                        }
                    })
                } else {
                    if (!requiredValue && field.type !== 'password') {
                        validationErrors.push(field.title)
                    }
                }
            }
        })
    }

    return validationErrors
}

export const excludeFields = (fields: any, excludedNames: any) => {
    if (fields) {
        return fields.filter(field => !excludedNames.includes(field.name))
    }
    return [];
}

export const getOptions = (values: any) => {
    const result = []
    if (values) {
        values.forEach(value => {
            result[value.id] = value.name
        })
    }
    return result
}

export const filterObject = (rawObj: object, allowed: any = []) => {
    return Object.keys(rawObj)
        .filter(key => allowed.includes(key))
        .reduce((obj, key) => {
            obj[key] = rawObj[key]
            return obj
        }, {})
}

export const hasOnlyKey = (keyName: string, object: Object): boolean => {
    const objectKeys = Object.keys(object);

    return objectKeys.length === 1 && objectKeys[0] === keyName;
}

const getGroupByField = (groups: any, field: any) => {
    return groups.find(group => group.name === field.group)
}
