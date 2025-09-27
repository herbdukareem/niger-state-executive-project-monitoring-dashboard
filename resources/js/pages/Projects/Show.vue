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
                @click="navigateTo('projects.edit', { id: project.id })"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Update Project
              </button>
              <button
                @click="navigateTo('projects.updates.create', { id: project.id })"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Update
              </button>
              <button
                @click="navigateTo('projects.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
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
                <div v-if="project.lga_name">
                  <dt class="text-sm font-medium text-gray-500">LGA</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ project.lga_name }}</dd>
                </div>
                <div v-if="project.ward_name">
                  <dt class="text-sm font-medium text-gray-500">Ward</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ project.ward_name }}</dd>
                </div>
                <div v-if="project.address">
                  <dt class="text-sm font-medium text-gray-500">Address</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ project.address }}</dd>
                </div>
                <div v-if="project.sector">
                  <dt class="text-sm font-medium text-gray-500">Sector</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ project.sector }}</dd>
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
                    <dd class="mt-1 text-sm text-gray-900">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ project.lga_name }}
                      </div>
                    </dd>
                  </div>
                  <div v-if="project.ward_name">
                    <dt class="text-sm font-medium text-gray-500">Ward</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ project.ward_name }}
                      </div>
                    </dd>
                  </div>
                  <div v-if="project.latitude && project.longitude">
                    <dt class="text-sm font-medium text-gray-500">Coordinates</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        {{ project.latitude.toFixed(6) }}, {{ project.longitude.toFixed(6) }}
                      </div>
                    </dd>
                  </div>
                  <div v-if="project.address">
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <div class="flex items-start">
                        <svg class="w-4 h-4 mr-2 mt-0.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        {{ project.address }}
                      </div>
                    </dd>
                  </div>
                  <div v-if="project.location_description" class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Location Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ project.location_description }}</dd>
                  </div>
                </div>

                <!-- Project Location Map -->
                <div v-if="project.latitude && project.longitude" class="mt-6">
                  <h5 class="text-sm font-medium text-gray-900 mb-3">Project Location on Map</h5>
                  <div class="h-64 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                    <ProjectMap
                      :projects="[project]"
                      :height="256"
                      :center="[project.latitude, project.longitude]"
                      :zoom="15"
                      :show-controls="true"
                    />
                  </div>
                  <p class="mt-2 text-xs text-gray-500">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Click and drag to explore the map. The red marker shows the exact project location.
                  </p>
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

        <!-- Recent Updates Section -->
        <div v-if="project" class="mt-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Updates</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest project progress and activity updates</p>
                </div>
                <button
                  @click="navigateTo('projects.updates.create', { id: project.id })"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Add Update
                </button>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="updatesLoading" class="px-4 py-8 text-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
              <p class="mt-2 text-sm text-gray-500">Loading updates...</p>
            </div>

            <!-- Updates List -->
            <div v-else-if="projectUpdates.length > 0" class="divide-y divide-gray-200">
              <div
                v-for="update in projectUpdates.slice(0, 5)"
                :key="update.id"
                class="px-4 py-4 hover:bg-gray-50 cursor-pointer"
                @click="viewUpdateDetails(update)"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="flex items-center">
                      <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                          <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="flex items-center">
                          <p class="text-sm font-medium text-gray-900">{{ update.title }}</p>
                          <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ formatUpdateType(update.update_type) }}
                          </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ update.description.substring(0, 100) }}{{ update.description.length > 100 ? '...' : '' }}</p>
                        <div class="flex items-center mt-2 text-xs text-gray-400">
                          <span>{{ formatDate(update.created_at) }}</span>
                          <span class="mx-2">•</span>
                          <span>{{ update.creator?.name || 'Unknown' }}</span>
                          <span v-if="update.attachments_count > 0" class="mx-2">•</span>
                          <span v-if="update.attachments_count > 0" class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            {{ update.attachments_count }} {{ update.attachments_count === 1 ? 'file' : 'files' }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="flex-shrink-0">
                    <div class="flex items-center">
                      <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ update.progress_percentage }}%</div>
                        <div class="w-16 bg-gray-200 rounded-full h-2 mt-1">
                          <div
                            class="bg-blue-600 h-2 rounded-full"
                            :style="{ width: update.progress_percentage + '%' }"
                          ></div>
                        </div>
                      </div>
                      <svg class="ml-4 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="px-4 py-8 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No updates yet</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by creating the first project update.</p>
              <div class="mt-6">
                <button
                  @click="navigateTo('projects.updates.create', { id: project.id })"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Create First Update
                </button>
              </div>
            </div>

            <!-- View All Updates Link -->
            <div v-if="projectUpdates.length > 5" class="px-4 py-3 bg-gray-50 text-center border-t border-gray-200">
              <button
                @click="viewAllUpdates()"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
              >
                View all {{ projectUpdates.length }} updates
              </button>
            </div>
          </div>
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
const projectUpdates = ref<ProjectUpdate[]>([]);
const loading = ref(true);
const updatesLoading = ref(false);

interface ProjectUpdate {
  id: number;
  update_type: string;
  title: string;
  description: string;
  progress_percentage: number;
  status: string;
  created_at: string;
  creator: {
    id: number;
    name: string;
  } | null;
  attachments_count: number;
}

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
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatUpdateType = (type: string) => {
  const types = {
    progress: 'Progress',
    financial: 'Financial',
    quality: 'Quality',
    site_visit: 'Site Visit',
    milestone: 'Milestone',
  };
  return types[type] || type;
};

const viewUpdateDetails = (update: ProjectUpdate) => {
  // For now, we'll show an alert with update details
  // Later this can navigate to a dedicated update details page
  alert(`Update Details:\n\nTitle: ${update.title}\nType: ${formatUpdateType(update.update_type)}\nProgress: ${update.progress_percentage}%\nDescription: ${update.description}\nCreated: ${formatDate(update.created_at)}\nCreated by: ${update.creator?.name || 'Unknown'}`);
};

const viewAllUpdates = () => {
  // Navigate to a dedicated updates list page
  // For now, show all updates in console
  console.log('All project updates:', projectUpdates.value);
  alert(`This project has ${projectUpdates.value.length} updates. A dedicated updates page will be implemented soon.`);
};

const fetchProject = async () => {
  try {
    const projectId = route.params.id;
    const response = await axios.get(`/api/projects/${projectId}`);
    project.value = response.data;
  } catch (error) {
    console.error('Error fetching project:', error);
    project.value = null;
  } finally {
    loading.value = false;
  }
};

const fetchProjectUpdates = async () => {
  if (!project.value) return;

  updatesLoading.value = true;
  try {
    const response = await axios.get(`/api/projects/${project.value.id}/updates`);
    projectUpdates.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching project updates:', error);
    projectUpdates.value = [];
  } finally {
    updatesLoading.value = false;
  }
};

onMounted(async () => {
  await fetchProject();
  if (project.value) {
    await fetchProjectUpdates();
  }
});
</script>
