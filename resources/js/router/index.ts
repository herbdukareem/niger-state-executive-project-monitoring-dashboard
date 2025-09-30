import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Import components
import Welcome from '@/pages/Welcome.vue'
import Dashboard from '@/pages/Dashboard.vue'
import ProjectsIndex from '@/pages/Projects/Index.vue'
import ProjectsShow from '@/pages/Projects/Show.vue'
import ProjectsCreate from '@/pages/Projects/Create.vue'
import ProjectsEdit from '@/pages/Projects/Edit.vue'
import ProjectsCreateUpdate from '@/pages/Projects/CreateUpdate.vue'
import Locations from '@/pages/Locations.vue'
import Login from '@/pages/auth/Login.vue'

const routes: RouteRecordRaw[] = [
  // Public routes
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    name: 'home',
    component: Welcome,
    meta: { requiresAuth: true }
  },
  // Protected routes
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/projects',
    name: 'projects.index',
    component: ProjectsIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/projects/create',
    name: 'projects.create',
    component: ProjectsCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/projects/:id',
    name: 'projects.show',
    component: ProjectsShow,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/projects/:id/edit',
    name: 'projects.edit',
    component: ProjectsEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/projects/:id/updates/create',
    name: 'projects.updates.create',
    component: ProjectsCreateUpdate,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/locations',
    name: 'locations',
    component: Locations,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Initialize auth if not already done
  if (!authStore.user && authStore.token) {
    await authStore.initializeAuth()
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)

  if (requiresAuth && !authStore.isAuthenticated) {
    // Redirect to login if authentication is required
    next({ name: 'login' })
  } else if (requiresGuest && authStore.isAuthenticated) {
    // Redirect to dashboard if already authenticated and trying to access guest routes
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
