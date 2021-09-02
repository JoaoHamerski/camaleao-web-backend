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
    sourceType: 'module',
    ecmaFeatures: {
      jsx: true
    }
  },
  plugins: [
    'vue'
  ],
  rules: {
    indent: ['error', 2],
    quotes: ['error', 'single'],
    semi: ['error', 'never'],
    'quote-props': ['error', 'as-needed'],
    'prefer-const': ['error'],
    'no-var': ['error'],
    'no-trailing-spaces': ['error', {ignoreComments: true}],
    'no-multiple-empty-lines': ['error', {max: 1, maxEOF: 0}],
    'vue/component-tags-order': ['error', {
      order: [ 'script', 'template', 'style' ]
    }],
    'space-before-function-paren': ['error']
  },
  globals: {
    axios: true,
    Vue: true,
    Swal: true,
    _: true,
    VueTippy: true
  }
}
