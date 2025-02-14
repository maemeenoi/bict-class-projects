const CACHE_NAME = "get-moving-app-cache-v1"
const urlsToCache = [
  "/",
  "/index.html",
  "/styles.css", // Add CSS file
  "/manifest.json", // Add manifest file
  "/service-worker.js", // Add the service worker file itself

  // Add React and Babel scripts
  "https://unpkg.com/react@18/umd/react.development.js",
  "https://unpkg.com/react-dom@18/umd/react-dom.development.js",
  "https://unpkg.com/@babel/standalone/babel.min.js",

  // Add model, view, and viewmodel scripts
  "/Model/Database.js",
  "/Model/Workout.js",
  "/Model/User.js",
  "/Model/Log.js",
  "/View/AddUser.js",
  "/View/AddWorkout.js",
  "/View/FetchUserWithWorkout.js",
  "/ViewModel/GetMovingAppViewModel.js",
  "/index.js",

  // Add icon files
  "/images/maskable_icon_x48.png",
  "/images/maskable_icon_x72.png",
  "/images/maskable_icon_x96.png",
  "/images/maskable_icon_x128.png",
  "/images/maskable_icon_x192.png",
  "/images/maskable_icon_x384.png",
  "/images/maskable_icon_x512.png",
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
