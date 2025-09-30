<template>
  <AppLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
          <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
              Create New Project
            </h2>
          </div>
        </div>

        <!-- Form -->
        <v-card class="elevation-2">
          <v-card-title class="text-h6 font-weight-bold">
            Project Information
          </v-card-title>
          <v-card-text>
            <v-form ref="formRef" @submit.prevent="submitForm">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.name"
                    label="Project Name"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Project name is required']"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.id_code"
                    label="Project Code"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Project code is required']"
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.description"
                    label="Description"
                    variant="outlined"
                    rows="3"
                    required
                    :rules="[v => !!v || 'Description is required']"
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.project_manager_id"
                    label="Project Manager"
                    variant="outlined"
                    :items="projectManagerOptions"
                    item-title="name"
                    item-value="id"
                    :loading="loadingProjectManagers"
                    required
                    :rules="[v => !!v || 'Project Manager is required']"
                    prepend-inner-icon="mdi-account-supervisor"
                    clearable
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.implementing_organization"
                    label="Implementing Organization"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Implementing Organization is required']"
                    prepend-inner-icon="mdi-office-building"
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.sector"
                    label="Sector"
                    variant="outlined"
                    :items="sectorOptions"
                    :loading="loadingSectors"
                    required
                    :rules="[v => !!v || 'Sector is required']"
                    prepend-inner-icon="mdi-domain"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.project_location"
                    label="Project Location"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Project Location is required']"
                    prepend-inner-icon="mdi-map-marker"
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.overall_goal"
                    label="Overall Goal"
                    variant="outlined"
                    rows="2"
                    required
                    :rules="[v => !!v || 'Overall Goal is required']"
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.total_budget"
                    label="Total Budget"
                    variant="outlined"
                    type="number"
                    prefix="₦"
                    required
                    :rules="[v => !!v || 'Budget is required', v => v > 0 || 'Budget must be greater than 0']"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.status"
                    label="Status"
                    variant="outlined"
                    :items="statusOptions"
                    item-title="text"
                    item-value="value"
                  />
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.start_date"
                    label="Start Date"
                    variant="outlined"
                    type="date"
                    required
                    :rules="[v => !!v || 'Start date is required']"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.end_date"
                    label="End Date"
                    variant="outlined"
                    type="date"
                    required
                    :rules="[v => !!v || 'End date is required']"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12">
                  <v-switch
                    v-model="form.work_plan_presentation"
                    label="Enable Work Plan Presentation"
                    color="primary"
                    hide-details
                  />
                  <div class="text-caption text-grey-darken-1 mt-1">
                    Enable this to allow detailed work plan activities tracking through project updates
                  </div>
                </v-col>
              </v-row>

              <!-- Location Section -->
              <v-divider class="my-6"></v-divider>
              <h3 class="text-h6 font-weight-bold mb-6">Project Location</h3>

              <!-- Current Location Button -->
              <div class="mb-6">
                <button
                  type="button"
                  @click="getCurrentLocation"
                  :disabled="gettingLocation"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="gettingLocation" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  {{ gettingLocation ? 'Getting Location...' : 'Use Current Location' }}
                </button>

                <div v-if="locationError" class="mt-2 text-sm text-red-600">
                  {{ locationError }}
                </div>

                <div v-if="locationMatch && showLocationSuggestions" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-md">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm text-green-800">
                        <strong>Location detected:</strong> {{ locationMatcher.formatLocation(locationMatch) }}
                      </p>
                      <p class="text-xs text-green-600 mt-1">
                        Coordinates: {{ form.latitude ? parseFloat(String(form.latitude)).toFixed(6) : 'N/A' }}, {{ form.longitude ? parseFloat(String(form.longitude)).toFixed(6) : 'N/A' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <v-row>
                <!-- LGA Selection -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.lga_id"
                    label="Local Government Area"
                    variant="outlined"
                    :items="lgaOptions"
                    :loading="loadingLgas"
                    item-title="name"
                    item-value="id"
                    required
                    :rules="[v => !!v || 'LGA is required']"
                    @update:model-value="onLGAChange"
                  >
                    <template #item="{ props, item }">
                      <v-list-item v-bind="props" :subtitle="item.raw.code" />
                    </template>
                  </v-select>
                </v-col>

                <!-- Ward Selection -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.ward_id"
                    label="Ward"
                    variant="outlined"
                    :items="wardOptions"
                    :loading="loadingWards"
                    item-title="name"
                    item-value="id"
                    :disabled="!selectedLGA"
                    required
                    :rules="[v => !!v || 'Ward is required']"
                    :placeholder="selectedLGA ? 'Select Ward' : 'Select LGA first'"
                  >
                    <template #item="{ props, item }">
                      <v-list-item v-bind="props" :subtitle="item.raw.code" />
                    </template>
                  </v-select>
                </v-col>
              </v-row>

              <!-- Coordinates (Manual Entry) -->
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.latitude"
                    label="Latitude"
                    variant="outlined"
                    type="number"
                    step="any"
                    placeholder="e.g., 9.6167"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model.number="form.longitude"
                    label="Longitude"
                    variant="outlined"
                    type="number"
                    step="any"
                    placeholder="e.g., 6.5500"
                  />
                </v-col>
              </v-row>

              <!-- Address -->
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="form.address"
                    label="Address"
                    variant="outlined"
                    placeholder="Street address or landmark"
                  />
                </v-col>
              </v-row>

              <!-- Location Description -->
              <v-row>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.location_description"
                    label="Location Description"
                    variant="outlined"
                    rows="3"
                    placeholder="Additional details about the project location..."
                  />
                </v-col>
              </v-row>

            </v-form>
          </v-card-text>
        </v-card>

        <!-- Work Plan Activities Info -->
        <v-card v-if="form.work_plan_presentation" variant="tonal" color="info" class="mb-6">
          <v-card-text>
            <div class="d-flex align-center">
              <v-icon class="mr-2">mdi-information</v-icon>
              <div>
                <div class="font-weight-medium">Work Plan Activities Enabled</div>
                <div class="text-body-2">
                  After creating this project, you can add and manage detailed work plan activities through project updates.
                  Use the "Work Plan Activities" update type to track progress on specific tasks and milestones.
                </div>
              </div>
            </div>
          </v-card-text>
        </v-card>

        <!-- Form Actions -->
        <v-card class="elevation-2">
          <v-card-actions class="px-6 py-6">
            <v-spacer />
            <v-btn
              variant="outlined"
              @click="navigateTo('projects.index')"
            >
              Cancel
            </v-btn>
            <v-btn
              type="submit"
              color="primary"
              :loading="submitting"
              @click="submitForm"
            >
              Create Project
            </v-btn>
          </v-card-actions>
        </v-card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import type { LocalGovernmentArea } from '@/data/nigerState';
import { geolocationService, type LocationResult } from '@/utils/geolocation';
import { locationMatcher, type LocationMatch } from '@/utils/locationMatcher';

const router = useRouter();
const submitting = ref(false);
const gettingLocation = ref(false);
const locationError = ref('');
const formRef = ref();

// API data loading states
const loadingLgas = ref(false);
const loadingWards = ref(false);
const loadingSectors = ref(false);
const loadingProjectManagers = ref(false);
const lgaOptions = ref<any[]>([]);
const wardOptions = ref<any[]>([]);
const sectorOptions = ref<string[]>([]);
const projectManagerOptions = ref<any[]>([]);

// Status options for select
const statusOptions = ref([
  { text: 'Not Started', value: 'not_started' },
  { text: 'In Progress', value: 'in_progress' },
  { text: 'On Hold', value: 'on_hold' },
  { text: 'Completed', value: 'completed' },
  { text: 'Cancelled', value: 'cancelled' }
]);

const form = ref({
  name: '',
  id_code: '',
  description: '',
  total_budget: 0,
  status: 'not_started',
  start_date: '',
  end_date: '',
  // Required fields that were missing
  project_manager_id: null as number | null,
  implementing_organization: '',
  project_location: '',
  sector: '',
  overall_goal: '',
  // Location fields
  lga_id: null as number | null,
  ward_id: null as number | null,
  latitude: null as number | null,
  longitude: null as number | null,
  address: '',
  location_description: '',
  // Work plan presentation
  work_plan_presentation: false
});

// Work plan activities


// Location-related reactive data
const selectedLGA = ref<LocalGovernmentArea | null>(null);

const locationMatch = ref<LocationMatch | null>(null);
const showLocationSuggestions = ref(false);

const navigateTo = (routeName: string) => {
  router.push({ name: routeName });
};

// Location methods
const getCurrentLocation = async () => {
  gettingLocation.value = true;
  locationError.value = '';

  try {
    const result: LocationResult = await geolocationService.getCurrentPosition({
      enableHighAccuracy: true,
      timeout: 15000,
      maximumAge: 300000
    });

    if (result.success && result.position) {
      const { latitude, longitude } = result.position.coords;

      // Update form with coordinates
      form.value.latitude = latitude;
      form.value.longitude = longitude;

      // Find matching LGA and ward
      const match = await locationMatcher.getLGAFromCoordinates(latitude, longitude);
      locationMatch.value = match;

      if (match.lga) {
        selectedLGA.value = match.lga;
        // Find the LGA in our API data by code
        const apiLga = lgaOptions.value.find((lga: any) => lga.code === match.lga!.code);
        if (apiLga) {
          form.value.lga_id = apiLga.id;

          // Fetch wards for this LGA
          await fetchWards(apiLga.id);

          // If we have a ward match, select it
          if (match.ward) {
            const apiWard = wardOptions.value.find((ward: any) => ward.code === match.ward!.code);
            if (apiWard) {
              form.value.ward_id = apiWard.id;
            }
          }
        }
      }

      // Try to get address using reverse geocoding
      try {
        const geocodeResult = await geolocationService.reverseGeocode(latitude, longitude);
        if (geocodeResult.address) {
          form.value.address = geocodeResult.address;
        }
      } catch (geocodeError) {
        console.warn('Reverse geocoding failed:', geocodeError);
      }

      showLocationSuggestions.value = true;
    } else {
      locationError.value = result.error?.message || 'Failed to get location';
    }
  } catch (error) {
    locationError.value = 'Failed to get current location';
    console.error('Geolocation error:', error);
  } finally {
    gettingLocation.value = false;
  }
};

// Fetch LGAs from API
const fetchLGAs = async () => {
  loadingLgas.value = true;
  try {
    const response = await axios.get('/api/lgas');
    lgaOptions.value = response.data.data;
  } catch (error) {
    console.error('Error fetching LGAs:', error);
  } finally {
    loadingLgas.value = false;
  }
};

// Fetch Sectors from API
const fetchSectors = async () => {
  loadingSectors.value = true;
  try {
    const response = await axios.get('/api/form-data/sectors');
    sectorOptions.value = response.data.data;
  } catch (error) {
    console.error('Error fetching sectors:', error);
  } finally {
    loadingSectors.value = false;
  }
};

// Fetch Project Managers from API
const fetchProjectManagers = async () => {
  loadingProjectManagers.value = true;
  try {
    const response = await axios.get('/api/form-data/project-managers');
    projectManagerOptions.value = response.data.data;
  } catch (error) {
    console.error('Error fetching project managers:', error);
  } finally {
    loadingProjectManagers.value = false;
  }
};

// Fetch Wards for selected LGA
const fetchWards = async (lgaId: number) => {
  loadingWards.value = true;
  try {
    const response = await axios.get(`/api/lgas/${lgaId}/wards`);
    wardOptions.value = response.data.data;
  } catch (error) {
    console.error('Error fetching wards:', error);
    wardOptions.value = [];
  } finally {
    loadingWards.value = false;
  }
};

const onLGAChange = () => {
  const lga = lgaOptions.value.find((l: any) => l.id === form.value.lga_id);
  selectedLGA.value = lga || null;
  form.value.ward_id = null; // Reset ward selection when LGA changes
  wardOptions.value = []; // Clear ward options

  if (form.value.lga_id) {
    fetchWards(form.value.lga_id);
  }
};

const submitForm = async () => {
  submitting.value = true;
  try {
    // Create project first
    const projectData = { ...form.value };

    const response = await axios.post('/api/projects', projectData);
    const project = response.data.data;

    // Work plan activities will be managed through project updates

    console.log('Project created successfully:', project);
    navigateTo('projects.index');
  } catch (error: any) {
    console.error('Error creating project:', error);
    if (error.response?.data?.errors) {
      // Handle validation errors
      const errors = error.response.data.errors;
      const errorMessages = Object.values(errors).flat().join('\n');
      alert(`Validation errors:\n${errorMessages}`);
    } else {
      alert('Error creating project. Please try again.');
    }
  } finally {
    submitting.value = false;
  }
};

// Initialize component
onMounted(() => {
  // Auto-generate project code based on current date
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
  form.value.id_code = `PRJ-${year}${month}${day}-${random}`;

  // Fetch form data from API
  fetchLGAs();
  fetchSectors();
  fetchProjectManagers();
});
</script>
