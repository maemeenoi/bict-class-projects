const CACHE_NAME = "get-moving-app-cache-v2"
const urlsToCache = [
  "/",
  "/index.html",
  "/src/App.css", // Adjust path if styles.css exists
  "/manifest.webmanifest", // Adjust path if manifest exists
  "/service-worker.js", // The service worker file itself

  // Model scripts
  "/src/model/database.js",
  "/src/model/workout.js",
  "/src/model/user.js",
  "/src/model/log.js",

  // View scripts
  "/src/view/add_user.jsx",
  "/src/view/add_workout.jsx",
  "/src/view/fetch_user_with_workout.jsx",

  // ViewModel script
  "/src/viewmodel/get_moving_app_viewmodel.js",

  // Other necessary files
  "/src/main.jsx",
  "/src/App.jsx",
  "/src/index.css",

  // Icon files
  "/src/assets/images/maskable_icon_x48.png",
  "/src/assets/images/maskable_icon_x72.png",
  "/src/assets/images/maskable_icon_x96.png",
  "/src/assets/images/maskable_icon_x128.png",
  "/src/assets/images/maskable_icon_x192.png",
  "/src/assets/images/maskable_icon_x384.png",
  "/src/assets/images/maskable_icon_x512.png",

  // Screenshots
  "/src/assets/images/screenshot1.png",
  "/src/assets/images/screenshot2.png",
]
// Install a service worker
self.addEventListener("install", (event) => {
  event.waitUntil(
    (async () => {
      const cache = await caches.open(CACHE_NAME)
      await cache.addAll(urlsToCache)
    })()
  )
})

// Cache and return requests
self.addEventListener("fetch", (event) => {
  event.respondWith(
    (async () => {
      const cache = await caches.open(CACHE_NAME)
      const cachedResponse = await cache.match(event.request)
      if (cachedResponse) {
        return cachedResponse
      } else {
        try {
          const fetchResponse = await fetch(event.request)
          await cache.put(event.request, fetchResponse.clone())
          return fetchResponse
        } catch (e) {
          // Handle network errors
        }
      }
    })()
  )
})

// Update a service worker
self.addEventListener("activate", (event) => {
  const cacheWhitelist = ["get-moving-app-cache-v1"]
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName)
          }
        })
      )
    })
  )
})
