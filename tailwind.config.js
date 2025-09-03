import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/src/**/*.jsx",
        "./resources/js/src/**/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "rgb(var(--primary) / <alpha-value>)",
                secondary: "rgb(var(--secondary) / <alpha-value>)",
                accent: "rgb(var(--accent) / <alpha-value>)",
                background: "rgb(var(--background) / <alpha-value>)",
                text: "rgb(var(--text) / <alpha-value>)",
                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                foreground: "hsl(var(--foreground))",
            },
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },
        },
    },

    plugins: [
        require("@tailwindcss/forms")({ strategy: "class" }),
        require("@tailwindcss/typography"),
    ],
};
