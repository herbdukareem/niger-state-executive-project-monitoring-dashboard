<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
          <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
              Project Locations
            </h2>
            <p class="mt-1 text-sm text-gray-500">
              Geographic distribution of projects across Niger State's 25 Local Government Areas
            </p>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total LGAs</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.totalLGAs }}</dd>
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
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Wards</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.totalWards }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Area</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ formatArea(statistics.totalArea) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Population</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ formatPopulation(statistics.totalPopulation) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white shadow rounded-lg mb-6">
          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search LGAs</label>
                <input
                  v-model="searchQuery"
                  type="text"
                  id="search"
                  placeholder="Search by name, headquarters, or code..."
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                <select
                  v-model="sortBy"
                  id="sort"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="name">Name</option>
                  <option value="population">Population</option>
                  <option value="area">Area</option>
                  <option value="wards">Number of Wards</option>
                </select>
              </div>
              <div>
                <label for="view" class="block text-sm font-medium text-gray-700">View</label>
                <select
                  v-model="viewMode"
                  id="view"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="grid">Grid View</option>
                  <option value="list">List View</option>
                  <option value="map">Map View</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- LGA Grid/List View -->
        <div v-if="viewMode !== 'map'" class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Local Government Areas</h3>
            
            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="lga in filteredAndSortedLGAs"
                :key="lga.id"
                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                @click="selectLGA(lga)"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h4 class="text-lg font-medium text-gray-900">{{ lga.name }}</h4>
                    <p class="text-sm text-gray-500">{{ lga.headquarters }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ lga.code }}</p>
                  </div>
                  <div class="text-right">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ lga.wards.length }} wards
                    </span>
                  </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-500">Area:</span>
                    <span class="font-medium ml-1">{{ formatArea(lga.area_km2) }}</span>
                  </div>
                  <div>
                    <span class="text-gray-500">Population:</span>
                    <span class="font-medium ml-1">{{ formatPopulation(lga.population_estimate) }}</span>
                  </div>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                  {{ lga.coordinates.latitude.toFixed(4) }}, {{ lga.coordinates.longitude.toFixed(4) }}
                </div>
              </div>
            </div>

            <!-- List View -->
            <div v-else-if="viewMode === 'list'" class="overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LGA</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Headquarters</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wards</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Population</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordinates</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="lga in filteredAndSortedLGAs"
                    :key="lga.id"
                    class="hover:bg-gray-50 cursor-pointer"
                    @click="selectLGA(lga)"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ lga.name }}</div>
                        <div class="text-sm text-gray-500">{{ lga.code }}</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ lga.headquarters }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ lga.wards.length }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatArea(lga.area_km2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatPopulation(lga.population_estimate) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ lga.coordinates.latitude.toFixed(4) }}, {{ lga.coordinates.longitude.toFixed(4) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Map View -->
        <div v-else class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Niger State Interactive Map</h3>
            <NigerStateMap @lga-selected="onLGASelected" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import NigerStateMap from '@/components/NigerStateMap.vue';
import { ref, computed } from 'vue';
import { nigerStateLGAs, type LocalGovernmentArea } from '@/data/nigerState';
import { locationMatcher } from '@/utils/locationMatcher';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Locations',
    href: '/locations',
  },
];

// Reactive data
const searchQuery = ref('');
const sortBy = ref('name');
const viewMode = ref('grid');

// Computed properties
const statistics = computed(() => locationMatcher.getStatistics());

const filteredAndSortedLGAs = computed(() => {
  let filtered = nigerStateLGAs;
  
  // Apply search filter
  if (searchQuery.value.trim()) {
    filtered = locationMatcher.searchLGAs(searchQuery.value);
  }
  
  // Apply sorting
  return filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'population':
        return b.population_estimate - a.population_estimate;
      case 'area':
        return b.area_km2 - a.area_km2;
      case 'wards':
        return b.wards.length - a.wards.length;
      default: // name
        return a.name.localeCompare(b.name);
    }
  });
});

// Methods
const selectLGA = (lga: LocalGovernmentArea) => {
  console.log('Selected LGA:', lga);
  // TODO: Navigate to LGA detail page or show modal
};

const onLGASelected = (lga: LocalGovernmentArea | null) => {
  if (lga) {
    selectLGA(lga);
  }
};

const formatArea = (area: number): string => {
  return `${area.toLocaleString()} km²`;
};

const formatPopulation = (population: number): string => {
  if (population >= 1000000) {
    return `${(population / 1000000).toFixed(1)}M`;
  } else if (population >= 1000) {
    return `${(population / 1000).toFixed(0)}K`;
  }
  return population.toLocaleString();
};
</script>
