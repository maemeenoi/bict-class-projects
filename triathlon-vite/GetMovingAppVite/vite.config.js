// import { defineConfig } from "vite"
// import react from "@vitejs/plugin-react-swc"

// export default defineConfig({
//   root: "src", // Adjust this if your root directory is different
//   server: {
//     https: {
//       key: fs.readFileSync("certs/localhost.key"),
//       cert: fs.readFileSync("certs/localhost.crt"),
//     },
//   },
//   build: {
//     outDir: "dist", // Change this if your output directory is different
//     rollupOptions: {
//       input: "src/index.html", // Adjust this if your entry point is different
//     },
//   },
// })
// import fs from "fs"
// import path from "path"

// export default {
//   server: {
//     https: {
//       key: fs.readFileSync(path.resolve(__dirname, "certs/localhost.key")),
//       cert: fs.readFileSync(path.resolve(__dirname, "certs/localhost.crt")),
//     },
//     build: {
//       outDir: "dist", // Change this if your output directory is different
//       rollupOptions: {
//         input: "src/index.html", // Adjust this if your entry point is different
//       },
//     },
//     // Make sure the server is accessible over the local network
//     host: "0.0.0.0",
//   },
// }

// import { defineConfig } from "vite"
// import fs from "fs"
// import path from "path"

// export default defineConfig({
//   root: path.resolve(__dirname, "src"), // Set the root to the src directory
//   server: {
//     https: {
//       key: fs.readFileSync(path.resolve(__dirname, "certs/localhost.key")),
//       cert: fs.readFileSync(path.resolve(__dirname, "certs/localhost.crt")),
//     },
//     host: "0.0.0.0", // Make sure the server is accessible over the local network
//     port: 8080, // Define the port
//   },
//   build: {
//     outDir: path.resolve(__dirname, "dist"), // Output directory
//     rollupOptions: {
//       input: {
//         main: path.resolve(__dirname, "src/index.html"), // Entry point
//       },
//     },
//   },
// })

import { defineConfig } from "vite"
import react from "@vitejs/plugin-react-swc"

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
})
