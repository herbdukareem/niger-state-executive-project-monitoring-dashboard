<template>
  <AppLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div v-if="loading" class="text-center py-8">
          <div class="text-gray-500">Loading project...</div>
        </div>
        
        <div v-else-if="project">
          <!-- Header -->
          <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
              <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ project.name }}
              </h2>
              <p class="mt-1 text-sm text-gray-500">{{ project.id_code }}</p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
              <button
                @click="navigateTo('projects.updates.create', { id: project.id })"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
              >
                Create Update
              </button>
              <button
                @click="navigateTo('projects.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Back to Projects
              </button>
            </div>
          </div>

          <!-- Project Details -->
          <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Project Details</h3>
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Status</dt>
                  <dd class="mt-1">
                    <span :class="getStatusColor(project.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ getStatusLabel(project.status) }}
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Progress</dt>
                  <dd class="mt-1">
                    <div class="flex items-center">
                      <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                        <div
                          class="bg-blue-600 h-2 rounded-full"
                          :style="{ width: `${project.progress_percentage}%` }"
                        ></div>
                      </div>
                      <span class="text-sm text-gray-900">{{ Math.round(project.progress_percentage) }}%</span>
                    </div>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Total Budget</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatCurrency(project.total_budget) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Expenditure</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatCurrency(project.cumulative_expenditure) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(project.start_date) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">End Date</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(project.end_date) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Project Manager</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ project.project_manager.name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Updates</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ project.updates_count }} updates</dd>
                </div>
              </div>

              <!-- Location Information -->
              <div v-if="project.lga_name || project.ward_name || project.latitude" class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-md font-medium text-gray-900 mb-4">Location Information</h4>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div v-if="project.lga_name">
                    <dt class="text-sm font-medium text-gray-500">Local Government Area</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ project.lga_name }}</dd>
                  </div>
                  <div v-if="project.ward_name">
                    <dt class="text-sm font-medium text-gray-500">Ward</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ project.ward_name }}</dd>
                  </div>
                  <div v-if="project.latitude && project.longitude">
                    <dt class="text-sm font-medium text-gray-500">Coordinates</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ project.latitude.toFixed(6) }}, {{ project.longitude.toFixed(6) }}
                    </dd>
                  </div>
                  <div v-if="project.address">
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ project.address }}</dd>
                  </div>
                  <div v-if="project.location_description" class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Location Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ project.location_description }}</dd>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Updates -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Updates</h3>
              <div v-if="project.latest_update" class="border rounded-lg p-4">
                <h4 class="font-medium text-gray-900">{{ project.latest_update.title }}</h4>
                <p class="text-sm text-gray-500 mt-1">{{ formatDate(project.latest_update.created_at) }}</p>
              </div>
              <div v-else class="text-gray-500 text-center py-4">
                No updates available
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8">
          <div class="text-red-500">Project not found</div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
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
  // Location fields
  lga_id?: string;
  lga_name?: string;
  ward_id?: string;
  ward_name?: string;
  latitude?: number;
  longitude?: number;
  address?: string;
  location_description?: string;
}

const router = useRouter();
const route = useRoute();
const project = ref<Project | null>(null);
const loading = ref(true);

const navigateTo = (routeName: string, params?: any) => {
  router.push({ name: routeName, params });
};

const getStatusColor = (status: string) => {
  const colors = {
    not_started: 'bg-gray-100 text-gray-800',
    in_progress: 'bg-blue-100 text-blue-800',
    on_hold: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
  const labels = {
    not_started: 'Not Started',
    in_progress: 'In Progress',
    on_hold: 'On Hold',
    completed: 'Completed',
    cancelled: 'Cancelled',
  };
  return labels[status] || status;
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const fetchProject = async () => {
  try {
    const projectId = route.params.id;
    const response = await axios.get(`/api/projects/${projectId}`);
    project.value = response.data;
  } catch (error) {
    console.error('Error fetching project:', error);
    // Sample data for demonstration
    project.value = {
      id: parseInt(route.params.id as string) || 1,
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
      },
      // Sample location data
      lga_id: 'bida',
      lga_name: 'Bida',
      ward_id: 'bida_central',
      ward_name: 'Bida Central',
      latitude: 9.0833,
      longitude: 6.0167,
      address: 'Along Bida-Minna Road, Bida',
      location_description: 'Located near the main market area for easy community access'
    };
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProject();
});
</script>
