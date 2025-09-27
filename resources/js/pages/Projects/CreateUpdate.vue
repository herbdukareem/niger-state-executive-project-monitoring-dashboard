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
              <span class="ml-4 text-sm font-medium text-gray-500">Create Update</span>
            </div>
          </li>
        </ol>
      </nav>
    </template>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
        <p class="mt-1 text-sm text-gray-500">The project you're looking for doesn't exist.</p>
        <div class="mt-6">
          <button @click="navigateTo('projects.index')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Back to Projects
          </button>
        </div>
      </div>

      <!-- Create Update Form -->
      <div v-else>
        <!-- Header -->
        <div class="mb-8">
          <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
              <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Create Project Update
              </h2>
              <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  {{ project.name }} ({{ project.id_code }})
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                  Current Progress: {{ project.progress_percentage }}%
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
                Back to Project
              </button>
            </div>
          </div>
        </div>

        <!-- Update Form -->
        <form @submit.prevent="createUpdate" class="space-y-8">
          <!-- Basic Update Information -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-6">Update Information</h3>
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label for="update_type" class="block text-sm font-medium text-gray-700">Update Type</label>
                  <select
                    v-model="form.update_type"
                    id="update_type"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="progress">Progress Update</option>
                    <option value="financial">Financial Update</option>
                    <option value="quality">Quality Assessment</option>
                    <option value="site_visit">Site Visit Report</option>
                    <option value="milestone">Milestone Achievement</option>
                  </select>
                </div>
                <div>
                  <label for="progress_percentage" class="block text-sm font-medium text-gray-700">Updated Progress (%)</label>
                  <input
                    v-model.number="form.progress_percentage"
                    type="number"
                    id="progress_percentage"
                    min="0"
                    max="100"
                    step="0.01"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
                <div class="sm:col-span-2">
                  <label for="title" class="block text-sm font-medium text-gray-700">Update Title</label>
                  <input
                    v-model="form.title"
                    type="text"
                    id="title"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Brief title describing this update"
                  />
                </div>
                <div class="sm:col-span-2">
                  <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    v-model="form.description"
                    id="description"
                    rows="4"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Detailed description of the update, progress made, challenges faced, etc."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Financial Information (if financial update) -->
          <div v-if="form.update_type === 'financial'" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-6">Financial Information</h3>
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label for="budget_spent_period" class="block text-sm font-medium text-gray-700">Budget Spent This Period (₦)</label>
                  <input
                    v-model.number="form.budget_spent_period"
                    type="number"
                    id="budget_spent_period"
                    min="0"
                    step="0.01"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label for="cumulative_budget_spent" class="block text-sm font-medium text-gray-700">Cumulative Budget Spent (₦)</label>
                  <input
                    v-model.number="form.cumulative_budget_spent"
                    type="number"
                    id="cumulative_budget_spent"
                    min="0"
                    step="0.01"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
                <div class="sm:col-span-2">
                  <label for="financial_comments" class="block text-sm font-medium text-gray-700">Financial Comments</label>
                  <textarea
                    v-model="form.financial_comments"
                    id="financial_comments"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Comments on budget utilization, variances, etc."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Site Visit Information (if site visit) -->
          <div v-if="form.update_type === 'site_visit'" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-6">Site Visit Details</h3>
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label for="visit_date" class="block text-sm font-medium text-gray-700">Visit Date</label>
                  <input
                    v-model="form.visit_date"
                    type="date"
                    id="visit_date"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label for="weather_conditions" class="block text-sm font-medium text-gray-700">Weather Conditions</label>
                  <input
                    v-model="form.weather_conditions"
                    type="text"
                    id="weather_conditions"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="e.g., Sunny, Rainy, Cloudy"
                  />
                </div>
                <div class="sm:col-span-2">
                  <label for="site_conditions" class="block text-sm font-medium text-gray-700">Site Conditions</label>
                  <textarea
                    v-model="form.site_conditions"
                    id="site_conditions"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Description of current site conditions, accessibility, etc."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Photo Upload Section -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-6">Project Photos</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photos</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="photos" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                          <span>Upload photos</span>
                          <input
                            id="photos"
                            name="photos"
                            type="file"
                            multiple
                            accept="image/*"
                            @change="handlePhotoUpload"
                            class="sr-only"
                          />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB each</p>
                    </div>
                  </div>
                </div>

                <!-- Photo Previews -->
                <div v-if="selectedPhotos.length > 0" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                  <div v-for="(photo, index) in selectedPhotos" :key="index" class="relative">
                    <img :src="photo.preview" :alt="photo.file.name" class="h-24 w-full object-cover rounded-lg" />
                    <button
                      @click="removePhoto(index)"
                      type="button"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                    <div class="mt-1">
                      <input
                        v-model="photo.description"
                        type="text"
                        placeholder="Photo description"
                        class="block w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Comments/Notes Section -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-6">Additional Comments & Notes</h3>
              <div class="space-y-6">
                <div>
                  <label for="challenges_faced" class="block text-sm font-medium text-gray-700">Challenges Faced</label>
                  <textarea
                    v-model="form.challenges_faced"
                    id="challenges_faced"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Describe any challenges or obstacles encountered"
                  ></textarea>
                </div>
                <div>
                  <label for="next_steps" class="block text-sm font-medium text-gray-700">Next Steps</label>
                  <textarea
                    v-model="form.next_steps"
                    id="next_steps"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Outline the planned next steps and activities"
                  ></textarea>
                </div>
                <div>
                  <label for="recommendations" class="block text-sm font-medium text-gray-700">Recommendations</label>
                  <textarea
                    v-model="form.recommendations"
                    id="recommendations"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Any recommendations for improving project execution"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="navigateTo('projects.show', { id: project.id })"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="submitting" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ submitting ? 'Creating Update...' : 'Create Update' }}
            </button>
          </div>
        </form>
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
  progress_percentage: number;
}

interface PhotoFile {
  file: File;
  preview: string;
  description: string;
}

const project = ref<Project | null>(null);
const loading = ref(true);
const submitting = ref(false);
const selectedPhotos = ref<PhotoFile[]>([]);

const form = reactive({
  update_type: 'progress',
  title: '',
  description: '',
  progress_percentage: 0,
  budget_spent_period: null as number | null,
  cumulative_budget_spent: null as number | null,
  financial_comments: '',
  visit_date: '',
  weather_conditions: '',
  site_conditions: '',
  challenges_faced: '',
  next_steps: '',
  recommendations: '',
});

const navigateTo = (routeName: string, params?: any) => {
  router.push({ name: routeName, params });
};

const fetchProject = async () => {
  try {
    const projectId = route.params.id;
    const response = await axios.get(`/api/projects/${projectId}`);
    project.value = response.data;

    // Set initial progress percentage to current project progress
    form.progress_percentage = project.value.progress_percentage;
  } catch (error) {
    console.error('Error fetching project:', error);
    project.value = null;
  } finally {
    loading.value = false;
  }
};

const handlePhotoUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const files = target.files;

  if (files) {
    Array.from(files).forEach(file => {
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          selectedPhotos.value.push({
            file,
            preview: e.target?.result as string,
            description: ''
          });
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Reset the input
  target.value = '';
};

const removePhoto = (index: number) => {
  selectedPhotos.value.splice(index, 1);
};

const createUpdate = async () => {
  submitting.value = true;
  try {
    // Create the project update
    const updateData = {
      ...form,
      activities_completed: form.description ? [form.description] : [],
      challenges_faced: form.challenges_faced ? [form.challenges_faced] : [],
      next_steps: form.next_steps ? [form.next_steps] : [],
      recommendations: form.recommendations ? [form.recommendations] : [],
    };

    const updateResponse = await axios.post(`/api/projects/${project.value?.id}/updates`, updateData);
    const createdUpdate = updateResponse.data.data;

    // Upload photos if any
    if (selectedPhotos.value.length > 0) {
      const formData = new FormData();

      selectedPhotos.value.forEach((photo, index) => {
        formData.append('files[]', photo.file);
        formData.append(`descriptions[${index}]`, photo.description || '');
      });

      formData.append('project_update_id', createdUpdate.id.toString());
      formData.append('category', 'project_update');
      formData.append('is_public', 'true');

      await axios.post(`/api/projects/${project.value?.id}/attachments`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
    }

    console.log('Project update created successfully:', createdUpdate);
    navigateTo('projects.show', { id: project.value?.id });
  } catch (error) {
    console.error('Error creating project update:', error);
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      const errorMessages = Object.values(errors).flat().join('\n');
      alert(`Validation errors:\n${errorMessages}`);
    } else {
      alert('Error creating project update. Please try again.');
    }
  } finally {
    submitting.value = false;
  }
};

onMounted(() => {
  fetchProject();
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
