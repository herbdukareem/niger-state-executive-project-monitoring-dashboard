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
        <v-form @submit.prevent="createUpdate">
          <!-- Basic Update Information -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-update</v-icon>
              Update Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.update_type"
                    label="Update Type"
                    variant="outlined"
                    required
                    :items="updateTypeOptions"
                    item-title="label"
                    item-value="value"
                    prepend-inner-icon="mdi-format-list-bulleted-type"
                    :rules="[v => !!v || 'Update type is required']"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.progress_percentage"
                    label="Updated Progress (%)"
                    variant="outlined"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    prepend-inner-icon="mdi-progress-clock"
                    suffix="%"
                  />
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="form.title"
                    label="Update Title"
                    variant="outlined"
                    required
                    prepend-inner-icon="mdi-format-title"
                    placeholder="Brief title describing this update"
                    :rules="[v => !!v || 'Title is required']"
                  />
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.description"
                    label="Description"
                    variant="outlined"
                    rows="4"
                    required
                    prepend-inner-icon="mdi-text-long"
                    placeholder="Detailed description of the update, progress made, challenges faced, etc."
                    :rules="[v => !!v || 'Description is required']"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Financial Information (if financial update) -->
          <v-card v-if="form.update_type === 'financial'" class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-currency-ngn</v-icon>
              Financial Information
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.budget_spent_period"
                    label="Budget Spent This Period"
                    variant="outlined"
                    type="number"
                    min="0"
                    step="0.01"
                    prepend-inner-icon="mdi-cash-minus"
                    prefix="₦"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.cumulative_budget_spent"
                    label="Cumulative Budget Spent"
                    variant="outlined"
                    type="number"
                    min="0"
                    step="0.01"
                    prepend-inner-icon="mdi-cash-multiple"
                    prefix="₦"
                  />
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.financial_comments"
                    label="Financial Comments"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-comment-text-outline"
                    placeholder="Comments on budget utilization, variances, etc."
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Site Visit Information (if site visit) -->
          <v-card v-if="form.update_type === 'site_visit'" class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-map-marker-check</v-icon>
              Site Visit Details
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.visit_date"
                    label="Visit Date"
                    variant="outlined"
                    type="date"
                    prepend-inner-icon="mdi-calendar"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.weather_conditions"
                    label="Weather Conditions"
                    variant="outlined"
                    prepend-inner-icon="mdi-weather-partly-cloudy"
                    placeholder="e.g., Sunny, Rainy, Cloudy"
                  />
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.site_conditions"
                    label="Site Conditions"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-construction"
                    placeholder="Description of current site conditions, accessibility, etc."
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Work Plan Activities Section (if work plan activities update) -->
          <v-card v-if="form.update_type === 'work_plan_activities'" class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-clipboard-list</v-icon>
              Work Plan Activities Update
            </v-card-title>
            <v-card-text>
              <v-alert
                type="info"
                variant="tonal"
                density="compact"
                class="mb-4"
              >
                <v-icon start>mdi-information</v-icon>
                Update the status and progress of your project activities. This helps track detailed progress against planned milestones.
              </v-alert>

              <WorkPlanActivities
                v-model="workPlanEnabled"
                v-model:activities="workPlanActivities"
              />

              <!-- Save Activities Button -->
              <div class="d-flex justify-end mt-4">
                <v-btn
                  color="primary"
                  variant="outlined"
                  :loading="savingActivities"
                  @click="saveWorkPlanActivities"
                  prepend-icon="mdi-content-save"
                >
                  Save Activities
                </v-btn>
              </div>
            </v-card-text>
          </v-card>

          <!-- Photo Upload Section -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-camera</v-icon>
              Project Photos
            </v-card-title>
            <v-card-text>
              <div class="space-y-4">
                <!-- File Upload Area -->
                <v-file-input
                  v-model="uploadFiles"
                  label="Upload Photos"
                  variant="outlined"
                  multiple
                  accept="image/*"
                  prepend-icon="mdi-camera"
                  @update:model-value="handlePhotoUpload"
                  show-size
                  chips
                  clearable
                  :rules="[v => !v || v.length <= 10 || 'Maximum 10 files allowed']"
                />

                <v-alert
                  type="info"
                  variant="tonal"
                  density="compact"
                  class="mb-4"
                >
                  <v-icon start>mdi-information</v-icon>
                  Upload PNG, JPG, or GIF files up to 10MB each
                </v-alert>

                <!-- Photo Previews -->
                <div v-if="selectedPhotos.length > 0">
                  <v-row>
                    <v-col
                      v-for="(photo, index) in selectedPhotos"
                      :key="index"
                      cols="12"
                      sm="6"
                      md="4"
                      lg="3"
                    >
                      <v-card elevation="2">
                        <v-img
                          :src="photo.preview"
                          :alt="photo.file.name"
                          height="150"
                          cover
                        />
                        <v-card-actions class="pa-2">
                          <v-text-field
                            v-model="photo.description"
                            label="Photo description"
                            variant="outlined"
                            density="compact"
                            hide-details
                          />
                          <v-btn
                            icon="mdi-delete"
                            color="error"
                            variant="text"
                            size="small"
                            @click="removePhoto(index)"
                          />
                        </v-card-actions>
                      </v-card>
                    </v-col>
                  </v-row>
                </div>

                <!-- Photos will be uploaded when update is created -->
              </div>
            </v-card-text>
          </v-card>

          <!-- Comments/Notes Section -->
          <v-card class="mb-6" elevation="2">
            <v-card-title class="text-h6 font-weight-medium">
              <v-icon class="mr-2">mdi-comment-text-multiple-outline</v-icon>
              Additional Comments & Notes
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.challenges_faced"
                    label="Challenges Faced"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-alert-circle-outline"
                    placeholder="Describe any challenges or obstacles encountered"
                  />
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.next_steps"
                    label="Next Steps"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-arrow-right-circle-outline"
                    placeholder="Outline the planned next steps and activities"
                  />
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.recommendations"
                    label="Recommendations"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-lightbulb-outline"
                    placeholder="Any recommendations for improving project execution"
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
              prepend-icon="mdi-plus-circle"
            >
              {{ submitting ? 'Creating Update...' : 'Create Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import WorkPlanActivities from '@/components/WorkPlanActivities.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

interface Project {
  id: number;
  name: string;
  id_code: string;
  progress_percentage: number;
  work_plan_presentation?: boolean;
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
const uploadFiles = ref<File[]>([]);
const workPlanActivities = ref<any[]>([]);
const workPlanEnabled = ref(true); // Always enabled in update context
const savingActivities = ref(false);

// Base update type options
const baseUpdateTypeOptions = [
  { label: 'Progress Update', value: 'progress' },
  { label: 'Financial Update', value: 'financial' },
  { label: 'Quality Assessment', value: 'quality' },
  { label: 'Site Visit Report', value: 'site_visit' },
  { label: 'Milestone Achievement', value: 'milestone' },
];

// Computed update type options based on project settings
const updateTypeOptions = computed(() => {
  const options = [...baseUpdateTypeOptions];

  // Only add Work Plan Activities if project has work plan presentation enabled
  if (project.value?.work_plan_presentation) {
    options.push({ label: 'Work Plan Activities', value: 'work_plan_activities' });
  }

  return options;
});

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

// Watch for changes in available update types and reset if current type becomes invalid
watch(updateTypeOptions, (newOptions) => {
  const currentType = form.update_type;
  const isCurrentTypeValid = newOptions.some(option => option.value === currentType);

  // If current update type is no longer valid, reset to 'progress'
  if (!isCurrentTypeValid) {
    form.update_type = 'progress';
  }
}, { immediate: true });

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

    // Fetch work plan activities if project has work plan presentation enabled
    if (project.value?.work_plan_presentation) {
      await fetchWorkPlanActivities();
    }
  } catch (error) {
    console.error('Error fetching project:', error);
    project.value = null;
  } finally {
    loading.value = false;
  }
};

const handlePhotoUpload = (files: File[] | null) => {
  console.log('Photo upload triggered:', files);

  // Don't clear previous selections, allow adding more photos
  if (files && files.length > 0) {
    const newPhotos: PhotoFile[] = [];

    Array.from(files).forEach(file => {
      console.log('Processing file:', file.name, file.type, file.size);

      // Validate file size (max 10MB)
      if (file.size > 10 * 1024 * 1024) {
        alert(`File ${file.name} is too large. Maximum size is 10MB.`);
        return;
      }

      // Validate file type
      if (!file.type.startsWith('image/')) {
        alert(`File ${file.name} is not an image. Please select only image files.`);
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        const photoFile: PhotoFile = {
          file,
          preview: e.target?.result as string,
          description: ''
        };

        // Add to new photos array
        newPhotos.push(photoFile);

        // Update selected photos when all files are processed
        if (newPhotos.length === files.length) {
          selectedPhotos.value = [...selectedPhotos.value, ...newPhotos];
          console.log('All photos processed. Total photos:', selectedPhotos.value.length);
        }
      };
      reader.readAsDataURL(file);
    });
  } else {
    console.log('No files provided');
  }
};

const removePhoto = (index: number) => {
  selectedPhotos.value.splice(index, 1);
};

// Photos will be uploaded as part of the main update creation process

// Work Plan Activities functions

const fetchWorkPlanActivities = async () => {
  if (!project.value?.id) return;

  try {
    const response = await axios.get(`/api/projects/${project.value.id}/work-plan-activities`);
    workPlanActivities.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching work plan activities:', error);
    workPlanActivities.value = [];
  }
};

const saveWorkPlanActivities = async () => {
  if (!project.value?.id || workPlanActivities.value.length === 0) {
    alert('No activities to save');
    return;
  }

  savingActivities.value = true;
  try {
    await axios.post(`/api/projects/${project.value.id}/work-plan-activities`, {
      activities: workPlanActivities.value
    });
    alert('Work plan activities saved successfully!');
  } catch (error) {
    console.error('Error saving work plan activities:', error);
    alert('Failed to save work plan activities. Please try again.');
  } finally {
    savingActivities.value = false;
  }
};

const createUpdate = async () => {
  submitting.value = true;
  try {
    // Prepare update data - convert string fields to arrays where needed
    const updateData = {
      update_type: form.update_type,
      title: form.title,
      description: form.description,
      progress_percentage: form.progress_percentage,
      budget_spent_period: form.budget_spent_period,
      cumulative_budget_spent: form.cumulative_budget_spent,
      financial_comments: form.financial_comments,
      visit_date: form.visit_date,
      weather_conditions: form.weather_conditions,
      site_conditions: form.site_conditions,
      // Convert string fields to arrays for JSON storage
      activities_completed: form.description ? [form.description] : [],
      challenges_faced: form.challenges_faced ? [form.challenges_faced] : [],
      next_steps: form.next_steps ? [form.next_steps] : [],
      recommendations: form.recommendations ? [form.recommendations] : [],
    };

    console.log('Sending update data:', updateData);

    // Create the project update
    const updateResponse = await axios.post(`/api/projects/${project.value?.id}/updates`, updateData);
    const createdUpdate = updateResponse.data.data;

    console.log('Update created successfully:', createdUpdate);

    // Work plan activities are saved separately using the "Save Activities" button

    // Upload photos if any (associate with the created update)
    if (selectedPhotos.value.length > 0) {
      console.log('Uploading photos:', selectedPhotos.value.length);

      const formData = new FormData();

      // Add files to FormData with proper array syntax
      selectedPhotos.value.forEach((photo, index) => {
        console.log(`Adding file ${index}:`, photo.file.name, photo.file.size, photo.file.type);
        formData.append('files[]', photo.file, photo.file.name);
        formData.append(`descriptions[${index}]`, photo.description || '');
      });

      // Add metadata to associate photos with the created update
      formData.append('project_update_id', createdUpdate.id.toString());
      formData.append('category', 'project_update');
      formData.append('is_public', '1');

      // Log FormData contents for debugging
      console.log('FormData contents:');
      for (let pair of formData.entries()) {
        console.log(pair[0], pair[1]);
      }

      try {
        const attachmentResponse = await axios.post(`/api/projects/${project.value?.id}/attachments`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          timeout: 30000, // 30 second timeout
        });

        console.log('Photos uploaded successfully:', attachmentResponse.data);
      } catch (uploadError) {
        console.error('Photo upload failed:', uploadError);
        if ((uploadError as any).response) {
          console.error('Upload error response:', (uploadError as any).response.data);
        }
        // Don't fail the entire update if photo upload fails
        alert('Update created successfully, but some photos failed to upload. Please try uploading them again.');
      }
    }

    // Show success message
    alert('Project update created successfully!');

    // Navigate back to project details
    navigateTo('projects.show', { id: project.value?.id });

  } catch (error) {
    console.error('Error creating project update:', error);

    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      const errorMessages = Object.values(errors).flat().join('\n');
      alert(`Validation errors:\n${errorMessages}`);
    } else if (error.response?.data?.message) {
      alert(`Error: ${error.response.data.message}`);
    } else {
      alert('Error creating project update. Please try again.');
    }
  } finally {
    submitting.value = false;
  }
};

// Debug function to test API
const testAPI = async () => {
  try {
    const response = await axios.get('/api/test');
    console.log('API test successful:', response.data);
  } catch (error) {
    console.error('API test failed:', error);
  }
};

onMounted(() => {
  fetchProject();
  testAPI(); // Test API connection
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
