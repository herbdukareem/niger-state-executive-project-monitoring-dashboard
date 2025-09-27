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
                        Coordinates: {{ form.latitude?.toFixed(6) }}, {{ form.longitude?.toFixed(6) }}
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
                    :items="nigerStateLGAs"
                    item-title="name"
                    item-value="id"
                    required
                    :rules="[v => !!v || 'LGA is required']"
                    @update:model-value="onLGAChange"
                  >
                    <template #item="{ props, item }">
                      <v-list-item v-bind="props" :subtitle="item.raw.headquarters" />
                    </template>
                  </v-select>
                </v-col>

                <!-- Ward Selection -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.ward_id"
                    label="Ward"
                    variant="outlined"
                    :items="availableWards"
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
          <v-card-actions class="px-6 pb-6">
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { nigerStateLGAs, type LocalGovernmentArea, type Ward } from '@/data/nigerState';
import { geolocationService, type LocationResult } from '@/utils/geolocation';
import { locationMatcher, type LocationMatch } from '@/utils/locationMatcher';

const router = useRouter();
const submitting = ref(false);
const gettingLocation = ref(false);
const locationError = ref('');
const formRef = ref();

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
  // Location fields
  lga_id: '',
  ward_id: '',
  latitude: null as number | null,
  longitude: null as number | null,
  address: '',
  location_description: ''
});

// Location-related reactive data
const selectedLGA = ref<LocalGovernmentArea | null>(null);
const availableWards = computed(() => {
  return selectedLGA.value ? selectedLGA.value.wards : [];
});

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
        form.value.lga_id = match.lga.id;

        // If we have a ward match, select it
        if (match.ward) {
          form.value.ward_id = match.ward.id;
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

const onLGAChange = () => {
  const lga = nigerStateLGAs.find(l => l.id === form.value.lga_id);
  selectedLGA.value = lga || null;
  form.value.ward_id = ''; // Reset ward selection when LGA changes
};

const submitForm = async () => {
  submitting.value = true;
  try {
    const response = await axios.post('/api/projects', form.value);
    console.log('Project created successfully:', response.data);
    navigateTo('projects.index');
  } catch (error) {
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
});
</script>
