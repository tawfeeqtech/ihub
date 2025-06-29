import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
// import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/rtl.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        // tailwindcss(),
    ],
});

// import { defineConfig, loadEnv } from "vite";
// import laravel from "laravel-vite-plugin";
// import tailwindcss from "@tailwindcss/vite";

// export default defineConfig(({ mode }) => {
//     const env = loadEnv(mode, process.cwd(), "");

//     return {
//         plugins: [
//             laravel({
//                 input: ["resources/css/app.css", "resources/js/app.js"],
//                 refresh: true,
//             }),
//             tailwindcss(),
//         ],
//         define: {
//             "import.meta.env.VITE_ABLY_PUBLIC_KEY": JSON.stringify(
//                 env.VITE_ABLY_PUBLIC_KEY
//             ),
//         },
//     };
// });
