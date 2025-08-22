import forms from "@tailwindcss/forms"

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./node_modules/@protonemedia/inertiajs-tables-laravel-query-builder/**/*.{js,vue}", "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php", "./storage/framework/views/*.php", "./resources/views/**/*.blade.php", "./resources/views/**/*.vue", "./resources/views/**/*.js"],

    theme: {
        extend: {
            fontFamily: {
                sans: ["inherit"],
            },
        },
    },

    plugins: [forms],
}
