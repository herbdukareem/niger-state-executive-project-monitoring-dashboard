<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import type { BreadcrumbItem } from '@/types';
import axios from 'axios';

interface Project {
    id: number;
    name: string;
    id_code: string;
    status: string;
    progress_percentage: number;
    total_budget: number;
    cumulative_expenditure: number;
    start_date: string;
    end_date: string;
    project_manager: {
        id: number;
        name: string;
    };
    updates_count: number;
    attachments_count: number;
    latest_update?: {
        id: number;
        title: string;
        created_at: string;
    };
}

const router = useRouter();
const projects = ref<Project[]>([]);
const loading = ref(true);

const searchQuery = ref('');
const selectedStatus = ref('');
const selectedManager = ref('');

// Sample managers data
const managers = ref([
    { id: 1, name: 'John Doe' },
    { id: 2, name: 'Jane Smith' },
    { id: 3, name: 'Mike Johnson' },
    { id: 4, name: 'Sarah Wilson' }
]);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Projects',
        href: '/projects',
    },
];

const statusColors = {
    not_started: 'bg-gray-100 text-gray-800',
    in_progress: 'bg-blue-100 text-blue-800',
    on_hold: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const statusLabels = {
    not_started: 'Not Started',
    in_progress: 'In Progress',
    on_hold: 'On Hold',
    completed: 'Completed',
    cancelled: 'Cancelled',
};

const filteredProjects = computed(() => {
    return projects.value.filter(project => {
        const matchesSearch = !searchQuery.value ||
            project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            project.id_code.toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesStatus = !selectedStatus.value || project.status === selectedStatus.value;

        const matchesManager = !selectedManager.value ||
            project.project_manager.id.toString() === selectedManager.value;

        return matchesSearch && matchesStatus && matchesManager;
    });
});

const navigateTo = (routeName: string, params?: any) => {
  router.push({ name: routeName, params });
};

const fetchProjects = async () => {
    try {
        const response = await axios.get('/api/projects');
        projects.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error fetching projects:', error);
        // Fallback to empty array if API fails
        projects.value = [];
    } finally {
        loading.value = false;
    }
};

const search = () => {
    // Trigger reactive filtering - no additional action needed
    console.log('Searching with filters:', {
        search: searchQuery.value,
        status: selectedStatus.value,
        manager: selectedManager.value
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedStatus.value = '';
    selectedManager.value = '';
};

onMounted(() => {
    fetchProjects();
});

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString();
};

const getBudgetUtilization = (project: Project) => {
    if (project.total_budget === 0) return 0;
    return (project.cumulative_expenditure / project.total_budget) * 100;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="md:flex md:items-center md:justify-between mb-6">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                            Projects
                        </h2>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <button
                            @click="navigateTo('projects.create')"
                            class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            New Project
                        </button>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ projects.length }}</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Projects</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ projects.length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ projects.filter(p => p.status === 'in_progress').length }}</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">In Progress</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ projects.filter(p => p.status === 'in_progress').length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ projects.filter(p => p.status === 'completed').length }}</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ projects.filter(p => p.status === 'completed').length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ projects.filter(p => p.status === 'on_hold').length }}</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">On Hold</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ projects.filter(p => p.status === 'on_hold').length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input
                                    id="search"
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search projects..."
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    @keyup.enter="search"
                                />
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select
                                    id="status"
                                    v-model="selectedStatus"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="not_started">Not Started</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label for="manager" class="block text-sm font-medium text-gray-700">Project Manager</label>
                                <select
                                    id="manager"
                                    v-model="selectedManager"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                >
                                    <option value="">All Managers</option>
                                    <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                                        {{ manager.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="flex items-end space-x-2">
                                <button
                                    @click="search"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Search
                                </button>
                                <button
                                    @click="clearFilters"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Grid -->
                <div v-if="loading" class="text-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                    <p class="mt-4 text-gray-500">Loading projects...</p>
                </div>
                <div v-else class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <div
                        v-for="project in filteredProjects"
                        :key="project.id"
                        class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-indigo-200"
                    >
                        <!-- Project Header -->
                        <div class="px-6 py-5 border-b border-gray-100">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-1">
                                        <button
                                            @click="navigateTo('projects.show', { id: project.id })"
                                            class="hover:text-indigo-600 text-left transition-colors duration-200 focus:outline-none focus:text-indigo-600"
                                        >
                                            {{ project.name }}
                                        </button>
                                    </h3>
                                    <p class="text-sm text-gray-500 font-medium">{{ project.id_code }}</p>
                                </div>
                                <span
                                    :class="statusColors[project.status]"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ml-4 flex-shrink-0"
                                >
                                    {{ statusLabels[project.status] }}
                                </span>
                            </div>
                        </div>

                        <!-- Project Content -->
                        <div class="px-6 py-5">
                            <!-- Manager and Key Info -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ project.project_manager.name }}</p>
                                        <p class="text-xs text-gray-500">Project Manager</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ Math.round(project.progress_percentage) }}%</p>
                                    <p class="text-xs text-gray-500">Complete</p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-6">
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span class="font-medium">Project Progress</span>
                                    <span class="text-gray-500">{{ Math.round(project.progress_percentage) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div
                                        class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-3 rounded-full transition-all duration-500"
                                        :style="{ width: `${project.progress_percentage}%` }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Budget Information -->
                            <div class="mb-6">
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span class="font-medium">Budget Utilization</span>
                                    <span class="text-gray-500">{{ Math.round(getBudgetUtilization(project)) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div
                                        class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500"
                                        :style="{ width: `${getBudgetUtilization(project)}%` }"
                                    ></div>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Spent: {{ formatCurrency(project.cumulative_expenditure) }}</span>
                                    <span class="text-gray-500">Total: {{ formatCurrency(project.total_budget) }}</span>
                                </div>
                            </div>

                            <!-- Project Timeline -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-xs text-gray-500">Start Date</p>
                                            <p class="text-sm font-medium text-gray-900">{{ formatDate(project.start_date) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-xs text-gray-500">End Date</p>
                                            <p class="text-sm font-medium text-gray-900">{{ formatDate(project.end_date) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Stats -->
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>{{ project.updates_count }} updates</span>
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        <span>{{ project.attachments_count }} files</span>
                                    </div>
                                </div>
                                <button
                                    @click="navigateTo('projects.show', { id: project.id })"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    View Details
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Latest Update -->
                            <div v-if="project.latest_update" class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 mb-1">Latest Update</p>
                                        <p class="text-sm text-gray-700 font-medium truncate">{{ project.latest_update.title }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(project.latest_update.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Summary -->
                <div v-if="!loading && filteredProjects.length === 0" class="text-center py-16">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No projects found</h3>
                    <p class="text-gray-500 mb-4">No projects match your current search criteria.</p>
                    <button
                        @click="clearFilters"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Clear filters
                    </button>
                </div>

                <div v-if="!loading && filteredProjects.length > 0" class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium text-gray-900">{{ filteredProjects.length }}</span> of
                        <span class="font-medium text-gray-900">{{ projects.length }}</span> projects
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
