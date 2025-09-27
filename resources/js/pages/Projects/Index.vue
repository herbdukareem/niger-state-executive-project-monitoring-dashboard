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
        // Sample data for demonstration
        projects.value = [
            {
                id: 1,
                name: 'Water Infrastructure Project',
                id_code: 'WIP-2024-001',
                status: 'in_progress',
                progress_percentage: 65,
                total_budget: 500000,
                cumulative_expenditure: 325000,
                start_date: '2024-01-15',
                end_date: '2024-12-31',
                project_manager: { id: 1, name: 'John Doe' },
                updates_count: 12,
                attachments_count: 8,
                latest_update: {
                    id: 1,
                    title: 'Monthly Progress Report',
                    created_at: '2024-09-25'
                }
            },
            {
                id: 2,
                name: 'Education Initiative',
                id_code: 'EDU-2024-002',
                status: 'in_progress',
                progress_percentage: 40,
                total_budget: 750000,
                cumulative_expenditure: 300000,
                start_date: '2024-03-01',
                end_date: '2025-02-28',
                project_manager: { id: 2, name: 'Jane Smith' },
                updates_count: 8,
                attachments_count: 15,
                latest_update: {
                    id: 2,
                    title: 'Site Visit Report',
                    created_at: '2024-09-20'
                }
            }
        ];
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
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
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
                <div v-if="loading" class="text-center py-8">
                    <div class="text-gray-500">Loading projects...</div>
                </div>
                <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="project in filteredProjects"
                        :key="project.id"
                        class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <h3 class="text-lg font-medium text-gray-900 truncate">
                                        <button @click="navigateTo('projects.show', { id: project.id })" class="hover:text-indigo-600 text-left">
                                            {{ project.name }}
                                        </button>
                                    </h3>
                                </div>
                                <span :class="statusColors[project.status]" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ statusLabels[project.status] }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-500 mb-2">{{ project.id_code }}</p>
                            <p class="text-sm text-gray-600 mb-4">Manager: {{ project.project_manager.name }}</p>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progress</span>
                                    <span>{{ Math.round(project.progress_percentage) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        class="bg-blue-600 h-2 rounded-full"
                                        :style="{ width: `${project.progress_percentage}%` }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Budget Info -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Budget Utilization</span>
                                    <span>{{ Math.round(getBudgetUtilization(project)) }}%</span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatCurrency(project.cumulative_expenditure) }} / {{ formatCurrency(project.total_budget) }}
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="text-sm text-gray-500 mb-4">
                                <div>Start: {{ formatDate(project.start_date) }}</div>
                                <div>End: {{ formatDate(project.end_date) }}</div>
                            </div>

                            <!-- Stats -->
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>{{ project.updates_count }} updates</span>
                                <span>{{ project.attachments_count }} files</span>
                            </div>

                            <!-- Latest Update -->
                            <div v-if="project.latest_update" class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">Latest update:</p>
                                <p class="text-sm text-gray-700 truncate">{{ project.latest_update.title }}</p>
                                <p class="text-xs text-gray-500">{{ formatDate(project.latest_update.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Summary -->
                <div v-if="!loading && filteredProjects.length === 0" class="text-center py-8">
                    <div class="text-gray-500">No projects found matching your criteria.</div>
                    <button
                        @click="clearFilters"
                        class="mt-2 text-indigo-600 hover:text-indigo-500 text-sm"
                    >
                        Clear filters
                    </button>
                </div>

                <div v-if="!loading && filteredProjects.length > 0" class="mt-6 text-center text-sm text-gray-500">
                    Showing {{ filteredProjects.length }} of {{ projects.length }} projects
                </div>
            </div>
        </div>
    </AppLayout>
</template>
