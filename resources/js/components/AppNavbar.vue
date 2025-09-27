<template>
  <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Left side -->
        <div class="flex items-center">
          <!-- Mobile menu button -->
          <button
            @click="toggleMobileSidebar"
            class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          >
            <Menu class="h-6 w-6" />
          </button>
          
          <!-- Page title -->
          <div class="ml-4 lg:ml-0">
            <h1 class="text-xl font-semibold text-gray-900">{{ pageTitle }}</h1>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-4">
          <!-- Search -->
          <div class="hidden md:block">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Search class="h-5 w-5 text-gray-400" />
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search projects..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @keyup.enter="performSearch"
              />
            </div>
          </div>

          <!-- Notifications -->
          <button
            @click="toggleNotifications"
            class="relative p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          >
            <Bell class="h-6 w-6" />
            <span v-if="unreadNotifications > 0" class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
          </button>

          <!-- Quick actions -->
          <div class="relative">
            <button
              @click="toggleQuickActions"
              class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
            >
              <Plus class="h-6 w-6" />
            </button>
            
            <!-- Quick actions dropdown -->
            <div
              v-if="showQuickActions"
              class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            >
              <div class="py-1">
                <button
                  @click="navigateTo('projects.create')"
                  class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <Folder class="h-4 w-4 mr-3 text-gray-400" />
                  New Project
                </button>
                <button
                  @click="navigateTo('projects.updates.create')"
                  class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <FileText class="h-4 w-4 mr-3 text-gray-400" />
                  Project Update
                </button>
                <button
                  @click="navigateTo('reports')"
                  class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <BarChart3 class="h-4 w-4 mr-3 text-gray-400" />
                  Generate Report
                </button>
              </div>
            </div>
          </div>

          <!-- User menu -->
          <div class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                <span class="text-sm font-medium text-white">{{ userInitials }}</span>
              </div>
            </button>
            
            <!-- User menu dropdown -->
            <div
              v-if="showUserMenu"
              class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            >
              <div class="py-1">
                <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100">
                  <div class="font-medium">{{ userName }}</div>
                  <div class="text-gray-500">{{ userEmail }}</div>
                </div>
                <button
                  @click="navigateTo('profile')"
                  class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <User class="h-4 w-4 mr-3 text-gray-400" />
                  Profile
                </button>
                <button
                  @click="navigateTo('settings')"
                  class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <Settings class="h-4 w-4 mr-3 text-gray-400" />
                  Settings
                </button>
                <div class="border-t border-gray-100">
                  <button
                    @click="logout"
                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    <LogOut class="h-4 w-4 mr-3 text-gray-400" />
                    Sign out
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  Menu,
  Search,
  Bell,
  Plus,
  Folder,
  FileText,
  BarChart3,
  User,
  Settings,
  LogOut
} from 'lucide-vue-next';

const router = useRouter();
const route = useRoute();

// Props
interface Props {
  title?: string;
}

const props = withDefaults(defineProps<Props>(), {
  title: ''
});

// Reactive data
const searchQuery = ref('');
const showQuickActions = ref(false);
const showUserMenu = ref(false);
const showNotifications = ref(false);
const unreadNotifications = ref(3);

// User data (would come from auth store in real app)
const userName = ref('Admin User');
const userEmail = ref('admin@nigerstate.gov.ng');

// Computed
const pageTitle = computed(() => {
  if (props.title) return props.title;
  
  // Generate title from route
  const routeTitle = route.meta?.title || route.name;
  if (typeof routeTitle === 'string') {
    return routeTitle.charAt(0).toUpperCase() + routeTitle.slice(1);
  }
  return 'Dashboard';
});

const userInitials = computed(() => {
  return userName.value
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase();
});

// Methods
const navigateTo = (routeName: string) => {
  router.push({ name: routeName });
  closeAllDropdowns();
};

const toggleMobileSidebar = () => {
  // Emit event to parent to toggle mobile sidebar
  emit('toggle-mobile-sidebar');
};

const toggleQuickActions = () => {
  showQuickActions.value = !showQuickActions.value;
  showUserMenu.value = false;
  showNotifications.value = false;
};

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
  showQuickActions.value = false;
  showNotifications.value = false;
};

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  showQuickActions.value = false;
  showUserMenu.value = false;
};

const closeAllDropdowns = () => {
  showQuickActions.value = false;
  showUserMenu.value = false;
  showNotifications.value = false;
};

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ 
      name: 'projects.index', 
      query: { search: searchQuery.value.trim() } 
    });
  }
};

const logout = () => {
  // Handle logout logic
  console.log('Logging out...');
  closeAllDropdowns();
};

// Click outside to close dropdowns
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement;
  if (!target.closest('.relative')) {
    closeAllDropdowns();
  }
};

// Emit events
const emit = defineEmits<{
  'toggle-mobile-sidebar': [];
}>();

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
