import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

// Import components
import Welcome from '@/pages/Welcome.vue'
import Dashboard from '@/pages/Dashboard.vue'
import ProjectsIndex from '@/pages/Projects/Index.vue'
import ProjectsShow from '@/pages/Projects/Show.vue'
import ProjectsCreate from '@/pages/Projects/Create.vue'
import ProjectsEdit from '@/pages/Projects/Edit.vue'
import ProjectsCreateUpdate from '@/pages/Projects/CreateUpdate.vue'
import Locations from '@/pages/Locations.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: Welcome
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard
  },
  {
    path: '/projects',
    name: 'projects.index',
    component: ProjectsIndex
  },
  {
    path: '/projects/create',
    name: 'projects.create',
    component: ProjectsCreate
  },
  {
    path: '/projects/:id',
    name: 'projects.show',
    component: ProjectsShow,
    props: true
  },
  {
    path: '/projects/:id/edit',
    name: 'projects.edit',
    component: ProjectsEdit,
    props: true
  },
  {
    path: '/projects/:id/updates/create',
    name: 'projects.updates.create',
    component: ProjectsCreateUpdate,
    props: true
  },
  {
    path: '/locations',
    name: 'locations',
    component: Locations
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
