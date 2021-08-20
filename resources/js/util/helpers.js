import _isEmpty from 'lodash/isEmpty'
import _isEqual from 'lodash/isEqual'

export default {
    isEmpty(str) {
        return str === ''
            || str === null
            || str === undefined
            || str === false
    },
    abbr(str) {
        let newStr = ''
        const splittedStr = str.split(' ')

        splittedStr.forEach(function(word, index) {
            if (index != 0 && index != splittedStr.length - 1)
                newStr += word.charAt(0) + '. '
            else
                newStr += word + ' '
        })

        return newStr.trim()
    },
    getObjectDiff(obj1, obj2) {
        const diff = Object.keys(obj1).reduce((result, key) => {
            if (! Object.prototype.hasOwnProperty.call(obj2, key)) {
                result.push(key)
            } else if (_isEqual(obj1[key], obj2[key])) {
                const resultKeyIndex = result.indexOf(key)
                result.splice(resultKeyIndex, 1)
            }
            return result
        }, Object.keys(obj2))

        return diff
    },
    getChangedItem(arr1, arr2) {
        return arr1.filter((el, index) => {
            return Object.keys(el).some((prop) => {
                if (_isEmpty(arr2)) return

                return el[prop] !== arr2[index][prop]
            })
        })[0]
    },
    strLimit(str, limit, lastChars = null) {
        let newStr = str

        if (str.length > limit) {
            newStr = str.substr(0, limit)
            newStr += '...'
        }

        if (lastChars) {
            newStr += str.substr(str.length - lastChars, str.length)
        }

        return newStr
    },
    removeChildren(element) {
        while(element.firstChild) {
            element.firstChild.remove()
        }

        return element
    },
    valueToBRL(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value)
    },
    getExtension(string) {
        return string.substr(string.lastIndexOf('.') + 1)
    },
    dataURLtoFile(dataurl, filename) {
        var arr = dataurl.split(','),
            mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]),
            n = bstr.length,
            u8arr = new Uint8Array(n)

        while(n--) {
            u8arr[n] = bstr.charCodeAt(n)
        }

        return new File([u8arr], filename, {type: mime})
    },
    mask(mask, str) {
        const replaceAt = function(str, index, replacement) {
            return str.substr(0, index) + replacement + str.substr(index + replacement.length)
        }

        mask = JSON.parse(JSON.stringify(mask))

        str = str.replaceAll(' ', '')

        for (let i = 0; i < str.length; i++) {
            mask = replaceAt(mask, mask.indexOf('#'), str[i])
        }

        return mask
    },
    maskPhone(str)  {
        if (! str) {
            return ''
        }

        const masksMap = {
            8: '####-####',
            9: '# ####-####',
            10: '(##) ####-####',
            11: '(##) # ####-####',
        }

        for (const index in masksMap) {
            if (index == str.length) {
                return this.mask(masksMap[index], str)
            }
        }

        return ''
    }
}

