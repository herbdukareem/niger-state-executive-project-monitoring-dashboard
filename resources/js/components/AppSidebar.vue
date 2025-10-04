<template>
  <!-- Mobile sidebar overlay -->
  <div v-if="isMobileOpen" class="fixed inset-0 z-50 lg:hidden">
    <div class="fixed inset-0 bg-gray-900/80" @click="toggleMobileSidebar"></div>
    <div class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl">
      <div class="flex flex-col h-full bg-white border-r border-gray-200">
        <!-- Mobile Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
              <Building2 class="h-5 w-5 text-white" />
            </div>
            <div>
              <h1 class="text-lg font-semibold text-gray-900">Niger State</h1>
              <p class="text-xs text-gray-500">Project Monitor</p>
            </div>
          </div>
          <button
            @click="toggleMobileSidebar"
            class="p-1.5 rounded-md hover:bg-gray-100 transition-colors"
          >
            <X class="h-4 w-4" />
          </button>
        </div>

        <!-- Mobile Navigation -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
          <div v-for="section in filteredNavigationItems" :key="section.id" class="space-y-1">
            <button
              @click="toggleSection(section.id)"
              class="flex items-center justify-between w-full px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors"
            >
              <span>{{ section.title }}</span>
              <ChevronRight v-if="isSectionCollapsed(section.id)" class="h-3 w-3" />
              <ChevronDown v-else class="h-3 w-3" />
            </button>

            <div v-if="!isSectionCollapsed(section.id)" class="space-y-1">
              <button
                v-for="item in section.items"
                :key="item.routeName"
                @click="navigateTo(item.routeName)"
                :class="[
                  'flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                  isActiveRoute(item.routeName)
                    ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-600'
                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                ]"
              >
                <component
                  :is="item.icon"
                  :class="[
                    'h-4 w-4 mr-3 flex-shrink-0 transition-colors',
                    isActiveRoute(item.routeName) ? 'text-indigo-600' : 'text-gray-400'
                  ]"
                />
                <span class="flex-1 text-left">{{ item.title }}</span>
                <span
                  v-if="item.badge"
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                >
                  {{ item.badge }}
                </span>
              </button>
            </div>
          </div>
        </nav>

        <!-- Mobile Footer -->
        <div class="p-4 border-t border-gray-200">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
              <span class="text-sm font-medium text-gray-700">AD</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">Admin User</p>
              <p class="text-xs text-gray-500 truncate">admin@nigerstate.gov.ng</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Desktop sidebar -->
  <div
    :class="[
      'hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:flex-col transition-all duration-300',
      isCollapsed ? 'lg:w-16' : 'lg:w-72'
    ]"
  >
    <div class="flex flex-col h-full bg-white border-r border-gray-200">
      <!-- Desktop Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <div v-if="!isCollapsed" class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
            <Building2 class="h-5 w-5 text-white" />
          </div>
          <div>
            <h1 class="text-lg font-semibold text-gray-900">Niger State</h1>
            <p class="text-xs text-gray-500">Project Monitor</p>
          </div>
        </div>
        <button
          @click="toggleSidebar"
          class="p-1.5 rounded-md hover:bg-gray-100 transition-colors"
        >
          <ChevronRight v-if="isCollapsed" class="h-4 w-4" />
          <ChevronDown v-else class="h-4 w-4" />
        </button>
      </div>

      <!-- Desktop Navigation -->
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <div v-for="section in filteredNavigationItems" :key="section.id" class="space-y-1">
          <button
            v-if="!isCollapsed"
            @click="toggleSection(section.id)"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors"
          >
            <span>{{ section.title }}</span>
            <ChevronRight v-if="isSectionCollapsed(section.id)" class="h-3 w-3" />
            <ChevronDown v-else class="h-3 w-3" />
          </button>

          <div v-if="!isSectionCollapsed(section.id) || isCollapsed" class="space-y-1">
            <button
              v-for="item in section.items"
              :key="item.routeName"
              @click="navigateTo(item.routeName)"
              :class="[
                'flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                isActiveRoute(item.routeName)
                  ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-600'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
              ]"
              :title="isCollapsed ? item.title : ''"
            >
              <component
                :is="item.icon"
                :class="[
                  'flex-shrink-0 transition-colors',
                  isCollapsed ? 'h-5 w-5' : 'h-4 w-4 mr-3',
                  isActiveRoute(item.routeName) ? 'text-indigo-600' : 'text-gray-400'
                ]"
              />
              <template v-if="!isCollapsed">
                <span class="flex-1 text-left">{{ item.title }}</span>
                <span
                  v-if="item.badge"
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                >
                  {{ item.badge }}
                </span>
              </template>
            </button>
          </div>
        </div>
      </nav>

      <!-- Desktop Footer -->
      <div v-if="!isCollapsed" class="p-4 border-t border-gray-200">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
            <span class="text-sm font-medium text-gray-700">AD</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">Admin User</p>
            <p class="text-xs text-gray-500 truncate">admin@nigerstate.gov.ng</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mobile menu button -->
  <button
    @click="toggleMobileSidebar"
    class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-md bg-white shadow-md border border-gray-200"
  >
    <Menu class="h-5 w-5 text-gray-600" />
  </button>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import {
    LayoutGrid,
    Folder,
    Plus,
    BarChart3,
    MapPin,
    Settings,
    Users,
    UserCog,
    Shield,
    FileText,
    Calendar,
    DollarSign,
    AlertTriangle,
    ChevronDown,
    ChevronRight,
    Menu,
    X,
    Building2
} from 'lucide-vue-next';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// Sidebar state
const isCollapsed = ref(false);
const isMobileOpen = ref(false);

// Navigation items with modern structure
const navigationItems = ref([
    {
        id: 'overview',
        title: 'Overview',
        items: [
            {
                title: 'Dashboard',
                href: '/dashboard',
                routeName: 'dashboard',
                icon: LayoutGrid,
                badge: null
            }
        ]
    },
    {
        id: 'projects',
        title: 'Project Management',
        items: [
            {
                title: 'All Projects',
                href: '/projects',
                routeName: 'projects.index',
                icon: Folder,
                badge: null
            },
            {
                title: 'Create Project',
                href: '/projects/create',
                routeName: 'projects.create',
                icon: Plus,
                badge: null
            },
            {
                title: 'Locations',
                href: '/locations',
                routeName: 'locations',
                icon: MapPin,
                badge: null
            }
        ]
    },
    {
        id: 'analytics',
        title: 'Analytics & Reports',
        items: [
            {
                title: 'Analytics',
                href: '/analytics',
                routeName: 'analytics',
                icon: BarChart3,
                badge: null
            },
            {
                title: 'Reports',
                href: '/reports',
                routeName: 'reports',
                icon: FileText,
                badge: null
            }
        ]
    },
    {
        id: 'management',
        title: 'Management',
        items: [
            {
                title: 'Budget',
                href: '/budget',
                routeName: 'budget',
                icon: DollarSign,
                badge: null
            },
            {
                title: 'Schedule',
                href: '/schedule',
                routeName: 'schedule',
                icon: Calendar,
                badge: null
            },
            {
                title: 'Team',
                href: '/team',
                routeName: 'team',
                icon: Users,
                badge: null
            },
            {
                title: 'User Management',
                href: '/users',
                routeName: 'users.index',
                icon: UserCog,
                badge: null
            },
            {
                title: 'Roles & Permissions',
                href: '/roles',
                routeName: 'roles.index',
                icon: Shield,
                badge: null
            },
            {
                title: 'Risk Assessment',
                href: '/risk',
                routeName: 'risk',
                icon: AlertTriangle,
                badge: '3'
            }
        ]
    },
    {
        id: 'settings',
        title: 'Settings',
        items: [
            {
                title: 'Settings',
                href: '/settings',
                routeName: 'settings',
                icon: Settings,
                badge: null
            }
        ]
    }
]);

// Filter navigation items based on user permissions
const filteredNavigationItems = computed(() => {
    if (!authStore.isAuthenticated) {
        return navigationItems.value;
    }

    return navigationItems.value.map(section => ({
        ...section,
        items: section.items.filter(item => {
            // Show User Management only to admin users
            if (item.routeName === 'users.index') {
                return authStore.isAdmin;
            }
            // Show Roles & Permissions to admin users (admin, super_admin, governor)
            if (item.routeName === 'roles.index') {
                return authStore.isAdmin;
            }
            // Show all other items to authenticated users
            return true;
        })
    })).filter(section => section.items.length > 0); // Remove empty sections
});

// Collapsed sections state
const collapsedSections = ref(new Set());

const toggleSection = (sectionId: string) => {
    if (collapsedSections.value.has(sectionId)) {
        collapsedSections.value.delete(sectionId);
    } else {
        collapsedSections.value.add(sectionId);
    }
};

const isSectionCollapsed = (sectionId: string) => {
    return collapsedSections.value.has(sectionId);
};

const isActiveRoute = (routeName: string) => {
    return route.name === routeName;
};

const navigateTo = (routeName: string) => {
    router.push({ name: routeName });
    if (window.innerWidth < 1024) {
        isMobileOpen.value = false;
    }
};

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};

const toggleMobileSidebar = () => {
    isMobileOpen.value = !isMobileOpen.value;
};
</script>
