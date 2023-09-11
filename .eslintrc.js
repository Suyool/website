module.exports = {
    extends: ['eslint:recommended'],
    parserOptions: {
        ecmaVersion: 2023,
        sourceType: 'module',
        ecmaFeatures: {
            jsx: true
        }
    },
    env: {
        browser: true,
        es6: true,
        node: true
    },
    rules: {
        "no-console": 0,
        "no-unused-vars": 0,
        "array-bracket-spacing": ["error", "always"],
        "object-curly-spacing": ["error", "always"],
        "no-undef": "off",
        "no-prototype-builtins": "off",
        "no-redeclare": "off",
        "quotes": ["error", "double", { "allowTemplateLiterals": true }]
    }
};
