module.exports = {
    env: {
        browser: true,
        es2021: true,
        jquery: true,
        node: true
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/recommended'
    ],
    parserOptions: {
        ecmaVersion: 12,
        sourceType: 'module'
    },
    plugins: [
        'vue'
    ],
    rules: {
        indent: [
            'error',
            4
        ],
        'linebreak-style': [
            'error',
            'unix'
        ],
        quotes: [
            'error',
            'single'
        ],
        semi: [
            'error',
            'never'
        ],
        'quote-props': ['error', 'as-needed'],
        'prefer-const': ['error']
    },
    globals: {
        axios: true,
        Vue: true,
        Swal: true,
        _: true,
        VueTippy: true
    }
}
