# Vue.js Implementation Guide

This document explains how Vue.js is used throughout the entire project, including admin pages and web pages.

---

## Overview

The project uses **Vue.js 3** (Composition API) with:
- **Vue Router 4** for routing
- **Vuex 4** for state management
- **Laravel Mix** for bundling
- **127+ Vue components** across admin and web sections

---

## Project Structure

```
resources/js/
├── app.js                 # Admin Vue app entry point
├── web.js                 # Web Vue app entry point (chat functionality)
├── router/
│   └── index.js          # Vue Router configuration (1200+ lines)
├── store/
│   ├── index.js          # Vuex store root
│   ├── admin/            # 41 admin store modules
│   └── web/              # Web store modules
├── admin/                 # Admin Vue components
│   ├── Layouts/          # App.vue, Navbar.vue, Sidebar.vue
│   ├── Dashboard/
│   ├── Pages/            # 53 page setting components
│   ├── Users/
│   ├── Rides/
│   ├── Bookings/
│   └── ... (30+ modules)
├── components/            # Shared web components
│   ├── ChatMessages.vue
│   └── ChatForm.vue
└── web/
    └── modals/
        └── LanguageModal.vue
```

---

## 1. Admin Panel (SPA - Single Page Application)

### Entry Point
**File:** `resources/js/app.js`

```javascript
import { createApp } from "vue";
import router from "./router";
import store from "./store/index";
import AppLayout from "./admin/Layouts/App.vue";
import VueSweetalert2 from "vue-sweetalert2";

const app = createApp({})
    .use(store)           // Vuex state management
    .use(router)          // Vue Router
    .use(VueSweetalert2)   // SweetAlert2 plugin
    .component("AppLayout", AppLayout)
    .mount("#app");
```

**Blade Template:** `resources/views/admin/app.blade.php`
```html
<div id="app">
    <router-view :key="$route.fullPath" />
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
```

### Admin Features

#### A. **Vue Router** (`resources/js/router/index.js`)
- **1200+ lines** of route definitions
- **100+ routes** covering all admin functionality
- Route structure:
  ```javascript
  {
      path: '/admin/dashboard',
      name: 'admin.dashboard',
      component: Dashboard,
      meta: {
          breadcrumbs: [...]
      }
  }
  ```

#### B. **Vuex Store** (`resources/js/store/index.js`)
- **41 store modules** for state management:
  - `languages`, `countries`, `states`, `cities`
  - `users`, `drivers`, `students`, `passengers`
  - `rides`, `bookings`, `reviews`, `transactions`
  - `pages`, `settings`, `messages`, `auth`
  - And 25+ more modules

#### C. **Admin Components** (127+ Vue files)

**Layout Components:**
- `App.vue` - Main layout wrapper
- `Navbar.vue` - Top navigation
- `Sidebar.vue` - Side navigation menu

**Page Categories:**

1. **Dashboard & Management** (20+ components)
   - `Dashboard/Dashboard.vue`
   - `Users/Users.vue`, `Users/User.vue`
   - `Rides/Rides.vue`, `Rides/Ride.vue`
   - `Bookings/Bookings.vue`
   - `Reviews/Reviews.vue`, `Reviews/Review.vue`
   - `Transactions/Transactions.vue`

2. **Page Settings** (53 components)
   - `Pages/CreateHomePageSetting.vue`
   - `Pages/CreateLoginPageSetting.vue`
   - `Pages/CreateSignupPageSetting.vue`
   - `Pages/CreatePostRidePageSetting.vue`
   - `Pages/CreateFindRidePageSetting.vue`
   - `Pages/CreateBookingPageSetting.vue`
   - `Pages/CreateProfilePageSetting.vue`
   - And 46+ more page settings

3. **Mobile Screen Settings** (6 components)
   - `Mobile_Screens/CreateLoginSetting.vue`
   - `Mobile_Screens/CreateSignupSetting.vue`
   - `Mobile_Screens/CreateForgotSetting.vue`
   - `Mobile_Screens/CreateResetPasswordSetting.vue`
   - `Mobile_Screens/CreatePostRideSetting.vue`
   - `Mobile_Screens/CreateFindRideSetting.vue`

4. **General Settings** (9 components)
   - `General_Settings/BankSettings.vue`
   - `General_Settings/ReviewSettings.vue`
   - `General_Settings/PinkRideSettings.vue`
   - `General_Settings/FolkRideSettings.vue`
   - `General_Settings/CancelRideSettings.vue`
   - `General_Settings/ReferralSystemSettings.vue`
   - `General_Settings/RegistrationRewardSettings.vue`
   - `General_Settings/SuccessMessagesSettings.vue`
   - `General_Settings/ErrorHandlingSettings.vue`

5. **Content Management** (10+ components)
   - `News/News.vue`, `News/Create.vue`
   - `Videos/Videos.vue`, `Videos/Create.vue`
   - `Extra_Care_Faqs/ExtraCareFaqs.vue`
   - `Pink_Ride_Faqs/PinkRideFaqs.vue`

6. **Verification & Management** (10+ components)
   - `Driver_Verification/DriverVerification.vue`
   - `Student_Verification/StudentVerification.vue`
   - `Verify_Banks/VerifyBanks.vue`
   - `Verify_Phones/VerifyPhones.vue`
   - `Withdrawal_Requests/WithdrawalRequests.vue`

7. **Other Modules**
   - `Packages/Packages.vue`
   - `Booking_Credits/BookingCredits.vue`
   - `Coffee_Wallets/CoffeeWallets.vue`
   - `Claim_Rewards/ClaimRewards.vue`
   - `No_Show/NoShow.vue`
   - `Form_Submissions/ContactMessages.vue`
   - `Form_Submissions/ClosedAccountMessages.vue`
   - `Site_Settings/SiteSettings.vue`
   - `Profile/Profile.vue`

---

## 2. Web Pages (Hybrid Approach)

### Entry Point
**File:** `resources/js/web.js`

Used for **chat functionality** on web pages:

```javascript
import { createApp } from 'vue';
import ChatMessages from './components/ChatMessages.vue';
import ChatForm from './components/ChatForm.vue';

const app = createApp({
    data() {
        return {
            messages: [],
            chats: [],
        };
    },
    // ... chat logic with Pusher integration
});
```

**Usage in Blade Templates:**
- `booking.blade.php`
- `edit_booking.blade.php`
- `cancel_booking.blade.php`
- `coffee_wall.blade.php`
- `buy_balance.blade.php`
- `create_card.blade.php`
- `my_cards.blade.php`

### Web Components

1. **ChatMessages.vue**
   - Displays chat messages
   - Real-time updates via Pusher
   - Ride details integration
   - Message filtering and formatting

2. **ChatForm.vue**
   - Chat input form
   - Message sending
   - File upload support

3. **LanguageModal.vue**
   - Language selection modal
   - Used in web pages

---

## 3. Vue.js Dependencies

### Core Dependencies (package.json)
```json
{
  "vue": "^3.2.26",
  "vue-router": "^4.0.12",
  "vuex": "^4.0.2",
  "vue-sweetalert2": "^5.0.5",
  "vue-loader": "^17.0.0"
}
```

### Additional Vue Plugins
- `@ckeditor/ckeditor5-vue` - Rich text editor
- `@tinymce/tinymce-vue` - Alternative editor
- `vue-filepond` - File upload component
- `vuedraggable` - Drag and drop functionality
- `vue3-recaptcha2` - reCAPTCHA integration

---

## 4. Build Configuration

### Webpack Mix (`webpack.mix.js`)
```javascript
mix.js("resources/js/app.js", "public/js")      // Admin app
    .js("resources/js/web.js", "public/js")     // Web app
    .vue()                                       // Vue loader
    .postCss("resources/css/app.css", "public/css", [...])
```

### Build Commands
```bash
npm run dev          # Development build
npm run watch        # Watch mode
npm run production   # Production build
```

---

## 5. Vue Component Patterns

### A. Admin Component Structure
```vue
<template>
    <AppLayout>
        <!-- Component content -->
    </AppLayout>
</template>

<script>
export default {
    // Component logic
}
</script>
```

### B. Web Component Structure
```vue
<template>
    <div>
        <!-- Component content -->
    </div>
</template>

<script>
export default {
    props: ['prop1', 'prop2'],
    data() {
        return {
            // Component data
        };
    },
    methods: {
        // Component methods
    }
}
</script>
```

---

## 6. State Management (Vuex)

### Store Modules Structure
Each module typically includes:
- `state` - Initial state
- `mutations` - Synchronous state changes
- `actions` - Async operations (API calls)
- `getters` - Computed state values

**Example Module:**
```javascript
// resources/js/store/admin/users.js
export default {
    namespaced: true,
    state: {
        users: [],
        loading: false
    },
    mutations: {
        SET_USERS(state, users) {
            state.users = users;
        }
    },
    actions: {
        async fetchUsers({ commit }) {
            // API call
        }
    }
}
```

---

## 7. Routing Patterns

### Route Definition
```javascript
{
    path: '/admin/users',
    name: 'admin.users.index',
    component: Users,
    meta: {
        breadcrumbs: [
            {'name': 'Dashboard', 'routeName': 'admin.dashboard'},
            {'name': 'Users', 'routeName': 'admin.users.index'}
        ]
    }
}
```

### Navigation in Components
```javascript
// Using router
this.$router.push({ name: 'admin.users.index' });

// Using router-link
<router-link :to="{ name: 'admin.users.index' }">Users</router-link>
```

---

## 8. API Integration

### Axios Configuration
- CSRF token handling
- Base URL configuration
- Request/response interceptors
- Error handling

### API Calls in Components
```javascript
// In Vuex actions
async fetchData({ commit }) {
    const response = await axios.get('/api/endpoint');
    commit('SET_DATA', response.data);
}

// In components
async loadData() {
    await this.$store.dispatch('module/fetchData');
}
```

---

## 9. Real-time Features

### Pusher Integration (Web)
- Chat messages
- Real-time notifications
- Live updates

**Example:**
```javascript
// In web.js
Echo.private(`ride.${rideId}`)
    .listen('MessageSent', (e) => {
        // Handle message
    });
```

---

## 10. Component Communication

### Props (Parent → Child)
```vue
<ChildComponent :propName="value" />
```

### Events (Child → Parent)
```vue
<!-- Child -->
this.$emit('event-name', data);

<!-- Parent -->
<ChildComponent @event-name="handleEvent" />
```

### Vuex (Global State)
```javascript
// Access state
this.$store.state.module.data

// Dispatch action
this.$store.dispatch('module/action')

// Commit mutation
this.$store.commit('module/mutation', payload)
```

---

## 11. File Statistics

### Vue Files by Category

| Category | Count | Examples |
|----------|-------|----------|
| Admin Pages | 53 | Create*PageSetting.vue |
| Admin Management | 30+ | Users, Rides, Bookings |
| Admin Settings | 15+ | General_Settings, Site_Settings |
| Admin Layouts | 3 | App, Navbar, Sidebar |
| Web Components | 3 | ChatMessages, ChatForm, LanguageModal |
| **Total** | **127+** | |

---

## 12. Common Patterns

### A. Form Handling
```vue
<template>
    <form @submit.prevent="handleSubmit">
        <input v-model="form.field" />
        <button type="submit">Submit</button>
    </form>
</template>

<script>
export default {
    data() {
        return {
            form: {
                field: ''
            }
        };
    },
    methods: {
        async handleSubmit() {
            await this.$store.dispatch('module/submit', this.form);
        }
    }
}
</script>
```

### B. Loading States
```vue
<template>
    <div v-if="loading">Loading...</div>
    <div v-else>Content</div>
</template>

<script>
export default {
    computed: {
        loading() {
            return this.$store.state.module.loading;
        }
    }
}
</script>
```

### C. Error Handling
```vue
<script>
export default {
    methods: {
        async fetchData() {
            try {
                await this.$store.dispatch('module/fetch');
            } catch (error) {
                this.$swal.fire('Error', error.message, 'error');
            }
        }
    }
}
</script>
```

---

## 13. Best Practices

### 1. **Component Organization**
- Group related components in folders
- Use consistent naming (PascalCase)
- Keep components focused and reusable

### 2. **State Management**
- Use Vuex for global state
- Keep local state in components when appropriate
- Use namespaced modules

### 3. **Routing**
- Use named routes
- Include breadcrumb metadata
- Implement route guards for authentication

### 4. **API Calls**
- Use Vuex actions for API calls
- Handle errors consistently
- Show loading states

### 5. **Performance**
- Lazy load routes when possible
- Use computed properties for derived data
- Avoid unnecessary re-renders

---

## 14. Development Workflow

### 1. **Create New Admin Page**
```bash
# 1. Create Vue component
resources/js/admin/Module/Component.vue

# 2. Add route in router/index.js
{
    path: '/admin/route',
    name: 'admin.route.index',
    component: Component
}

# 3. Create Vuex module (if needed)
resources/js/store/admin/module.js

# 4. Build assets
npm run dev
```

### 2. **Create New Web Component**
```bash
# 1. Create component
resources/js/components/Component.vue

# 2. Import in web.js
import Component from './components/Component.vue';

# 3. Use in Blade template
<div id="component">
    <!-- Vue will mount here -->
</div>
```

---

## 15. Troubleshooting

### Common Issues

1. **Component Not Rendering**
   - Check if Vue app is mounted to correct element
   - Verify component is imported correctly
   - Check browser console for errors

2. **Router Not Working**
   - Verify route is defined in router/index.js
   - Check route name matches exactly
   - Ensure router-view is in template

3. **State Not Updating**
   - Use mutations for synchronous updates
   - Use actions for async operations
   - Check if module is namespaced

4. **API Calls Failing**
   - Verify CSRF token is set
   - Check API endpoint URL
   - Verify authentication headers

---

## 16. Key Files Reference

| File | Purpose |
|------|---------|
| `resources/js/app.js` | Admin Vue app entry |
| `resources/js/web.js` | Web Vue app entry |
| `resources/js/router/index.js` | All route definitions |
| `resources/js/store/index.js` | Vuex store root |
| `resources/views/admin/app.blade.php` | Admin SPA container |
| `webpack.mix.js` | Build configuration |

---

## 17. Summary

### Admin Panel
- **Full SPA** using Vue Router
- **100+ routes** for all admin functionality
- **41 Vuex modules** for state management
- **127+ Vue components** for UI

### Web Pages
- **Hybrid approach** - Blade templates + Vue components
- **Chat functionality** with real-time updates
- **Language selection** modal
- **Selective Vue usage** where needed

### Technology Stack
- Vue 3 (Composition API)
- Vue Router 4
- Vuex 4
- Laravel Mix
- Axios for API calls
- Pusher for real-time features

---

*Last Updated: Based on project analysis*
