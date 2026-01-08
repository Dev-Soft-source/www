# CANEDU

A web application built with Laravel (backend) and Vue.js (frontend), using Inertia.js for a seamless SPA experience.

---

## 🚀 Features

- **Laravel 9.x** (or your preferred version)
- **Vue 3**
- **Inertia.js**

---

## ⚙️ Installation

### Prerequisites

- PHP >= 8.0
- Composer
- Node.js >= 16.x
- npm or yarn
- MySQL (or another supported database)

### Steps

1. Clone the repo

   ```bash
    git clone https://github.com/xelentsolutions/proxima-ride.git
    cd proxima-ride

2. Install PHP dependencies
    ```bash
       composer install

3. Install JS dependencies
    ```bash
      npm install
        # or
      yarn install

4. Generate application key
    ```bash
      php artisan key:generate


5. Build assets & start watcher
    ```bash
        npm run watch
        # or
        yarn watch

6. Serve the app
    ```bash
       php artisan serve
