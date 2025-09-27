<template>
  <AppSidebarLayout>
    <template #breadcrumbs>
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
          <li>
            <div>
              <a href="#" @click="navigateTo('dashboard')" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Home</span>
              </a>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
              <a href="#" @click="navigateTo('projects.index')" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Projects</a>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
              <a href="#" @click="navigateTo('projects.show', { id: project?.id })" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">{{ project?.name || 'Project' }}</a>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
              <span class="ml-4 text-sm font-medium text-gray-500">Edit Project</span>
            </div>
          </li>
        </ol>
      </nav>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="!project" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Project not found</h3>
        <p class="mt-1 text-sm text-gray-500">The project you're looking for doesn't exist or you don't have permission to edit it.</p>
        <div class="mt-6">
          <button @click="navigateTo('projects.index')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Back to Projects
          </button>
        </div>
      </div>

      <!-- Edit Form -->
      <div v-else>
        <!-- Header -->
        <div class="mb-8">
          <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
              <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edit Project: {{ project.name }}
              </h2>
              <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  {{ project.id_code }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6-4h6"></path>
                  </svg>
                  Current Status: {{ getStatusLabel(project.status) }}
                </div>
              </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
              <button
                @click="navigateTo('projects.show', { id: project.id })"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Cancel
              </button>
            </div>
          </div>
        </div>

        <!-- Edit Form -->
        <v-form @submit.prevent="updateProject">
          <!-- Basic Information -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-information-outline</v-icon>
              Basic Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.name"
                    label="Project Name"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Project name is required']"
                    prepend-inner-icon="mdi-folder-outline"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.id_code"
                    label="Project Code"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Project code is required']"
                    prepend-inner-icon="mdi-identifier"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.status"
                    label="Project Status"
                    variant="outlined"
                    required
                    :items="statusOptions"
                    item-title="label"
                    item-value="value"
                    prepend-inner-icon="mdi-flag-outline"
                    :rules="[v => !!v || 'Status is required']"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.progress_percentage"
                    label="Progress (%)"
                    variant="outlined"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    prepend-inner-icon="mdi-progress-clock"
                    suffix="%"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.sector"
                    label="Sector"
                    variant="outlined"
                    prepend-inner-icon="mdi-domain"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.implementing_organization"
                    label="Implementing Organization"
                    variant="outlined"
                    prepend-inner-icon="mdi-office-building"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Budget Information -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-currency-ngn</v-icon>
              Budget Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.total_budget"
                    label="Total Budget"
                    variant="outlined"
                    type="number"
                    min="0"
                    step="0.01"
                    prepend-inner-icon="mdi-cash-multiple"
                    prefix="₦"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.cumulative_expenditure"
                    label="Cumulative Expenditure"
                    variant="outlined"
                    type="number"
                    min="0"
                    step="0.01"
                    prepend-inner-icon="mdi-cash-minus"
                    prefix="₦"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Timeline -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-calendar-range</v-icon>
              Project Timeline
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.start_date"
                    label="Start Date"
                    variant="outlined"
                    type="date"
                    prepend-inner-icon="mdi-calendar-start"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.end_date"
                    label="End Date"
                    variant="outlined"
                    type="date"
                    prepend-inner-icon="mdi-calendar-end"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Location Information -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-map-marker</v-icon>
              Location Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.lga_id"
                    label="Local Government Area"
                    variant="outlined"
                    :items="lgas"
                    item-title="name"
                    item-value="id"
                    prepend-inner-icon="mdi-city"
                    @update:model-value="onLgaChange"
                    clearable
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.ward_id"
                    label="Ward"
                    variant="outlined"
                    :items="wards"
                    item-title="name"
                    item-value="id"
                    prepend-inner-icon="mdi-home-city"
                    :disabled="!form.lga_id"
                    clearable
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.latitude"
                    label="Latitude"
                    variant="outlined"
                    type="number"
                    step="0.000001"
                    min="-90"
                    max="90"
                    prepend-inner-icon="mdi-crosshairs-gps"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.longitude"
                    label="Longitude"
                    variant="outlined"
                    type="number"
                    step="0.000001"
                    min="-180"
                    max="180"
                    prepend-inner-icon="mdi-crosshairs-gps"
                  />
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="form.address"
                    label="Address"
                    variant="outlined"
                    prepend-inner-icon="mdi-home-map-marker"
                  />
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.location_description"
                    label="Location Description"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-text-box-outline"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Project Description -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-text-box-multiple-outline</v-icon>
              Project Description
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.overall_goal"
                    label="Overall Goal"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-target"
                  />
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.description"
                    label="Description"
                    variant="outlined"
                    rows="4"
                    prepend-inner-icon="mdi-text-long"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Submit Button -->
          <v-card-actions class="justify-end pa-4">
            <v-btn
              variant="outlined"
              @click="navigateTo('projects.show', { id: project.id })"
              prepend-icon="mdi-arrow-left"
            >
              Cancel
            </v-btn>
            <v-btn
              type="submit"
              color="primary"
              :loading="submitting"
              prepend-icon="mdi-content-save"
            >
              {{ submitting ? 'Updating...' : 'Update Project' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

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
  sector: string;
  implementing_organization: string;
  lga_id?: number;
  ward_id?: number;
  latitude?: number;
  longitude?: number;
  address?: string;
  location_description?: string;
  overall_goal?: string;
  description?: string;
}

interface LGA {
  id: number;
  name: string;
  code: string;
}

interface Ward {
  id: number;
  name: string;
  code: string;
  lga_id: number;
}

const project = ref<Project | null>(null);
const lgas = ref<LGA[]>([]);
const wards = ref<Ward[]>([]);
const loading = ref(true);
const submitting = ref(false);

const statusOptions = [
  { label: 'Not Started', value: 'not_started' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'On Hold', value: 'on_hold' },
  { label: 'Completed', value: 'completed' },
  { label: 'Cancelled', value: 'cancelled' },
];

const form = reactive({
  name: '',
  id_code: '',
  status: 'not_started',
  progress_percentage: 0,
  total_budget: 0,
  cumulative_expenditure: 0,
  start_date: '',
  end_date: '',
  sector: '',
  implementing_organization: '',
  lga_id: null as number | null,
  ward_id: null as number | null,
  latitude: null as number | null,
  longitude: null as number | null,
  address: '',
  location_description: '',
  overall_goal: '',
  description: '',
});

const navigateTo = (routeName: string, params?: any) => {
  router.push({ name: routeName, params });
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

const fetchProject = async () => {
  try {
    const projectId = route.params.id;
    const response = await axios.get(`/api/projects/${projectId}`);
    project.value = response.data;

    // Populate form with project data
    Object.assign(form, {
      name: project.value.name,
      id_code: project.value.id_code,
      status: project.value.status,
      progress_percentage: project.value.progress_percentage,
      total_budget: project.value.total_budget,
      cumulative_expenditure: project.value.cumulative_expenditure,
      start_date: project.value.start_date,
      end_date: project.value.end_date,
      sector: project.value.sector || '',
      implementing_organization: project.value.implementing_organization || '',
      lga_id: project.value.lga_id,
      ward_id: project.value.ward_id,
      latitude: project.value.latitude,
      longitude: project.value.longitude,
      address: project.value.address || '',
      location_description: project.value.location_description || '',
      overall_goal: project.value.overall_goal || '',
      description: project.value.description || '',
    });

    // Load wards if LGA is selected
    if (form.lga_id) {
      await fetchWards(form.lga_id);
    }
  } catch (error) {
    console.error('Error fetching project:', error);
    project.value = null;
  } finally {
    loading.value = false;
  }
};

const fetchLGAs = async () => {
  try {
    const response = await axios.get('/api/lgas');
    lgas.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching LGAs:', error);
  }
};

const fetchWards = async (lgaId: number) => {
  try {
    const response = await axios.get(`/api/lgas/${lgaId}/wards`);
    wards.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching wards:', error);
    wards.value = [];
  }
};

const onLgaChange = () => {
  form.ward_id = null;
  wards.value = [];
  if (form.lga_id) {
    fetchWards(form.lga_id);
  }
};

const updateProject = async () => {
  submitting.value = true;
  try {
    const response = await axios.put(`/api/projects/${project.value?.id}`, form);
    console.log('Project updated successfully:', response.data);
    navigateTo('projects.show', { id: project.value?.id });
  } catch (error) {
    console.error('Error updating project:', error);
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      const errorMessages = Object.values(errors).flat().join('\n');
      alert(`Validation errors:\n${errorMessages}`);
    } else {
      alert('Error updating project. Please try again.');
    }
  } finally {
    submitting.value = false;
  }
};

onMounted(async () => {
  await Promise.all([
    fetchProject(),
    fetchLGAs()
  ]);
});
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
