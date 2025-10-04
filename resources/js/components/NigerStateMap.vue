<template>
  <div class="niger-state-map">
    <div class="map-container" ref="mapContainer">
      <svg
        :width="mapWidth"
        :height="mapHeight"
        viewBox="0 0 800 600"
        class="w-full h-full"
      >
        <!-- Niger State Boundary (Simplified) -->
        <rect
          x="50"
          y="50"
          width="700"
          height="500"
          fill="none"
          stroke="#e5e7eb"
          stroke-width="2"
          stroke-dasharray="5,5"
        />
        
        <!-- LGA Markers -->
        <g v-for="lga in lgasWithPositions" :key="lga.id">
          <!-- LGA Circle -->
          <circle
            :cx="lga.x"
            :cy="lga.y"
            :r="getLGARadius(lga)"
            :fill="getLGAColor(lga)"
            :stroke="selectedLGA?.id === lga.id ? '#3b82f6' : '#6b7280'"
            :stroke-width="selectedLGA?.id === lga.id ? 3 : 1"
            class="cursor-pointer transition-all duration-200 hover:stroke-indigo-500 hover:stroke-2"
            @click="selectLGA(lga)"
            @mouseenter="showTooltip(lga, $event)"
            @mouseleave="hideTooltip"
          />
          
          <!-- LGA Label -->
          <text
            :x="lga.x"
            :y="lga.y + getLGARadius(lga) + 15"
            text-anchor="middle"
            class="text-xs fill-gray-600 pointer-events-none"
            :class="{ 'font-bold fill-blue-600': selectedLGA?.id === lga.id }"
          >
            {{ lga.name }}
          </text>
        </g>
        
        <!-- Legend -->
        <g transform="translate(600, 450)">
          <rect x="0" y="0" width="180" height="120" fill="white" stroke="#e5e7eb" stroke-width="1" rx="4"/>
          <text x="10" y="20" class="text-sm font-medium fill-gray-900">Population</text>
          
          <circle cx="20" cy="40" r="4" fill="#ef4444"/>
          <text x="35" y="45" class="text-xs fill-gray-600">&lt; 100K</text>
          
          <circle cx="20" cy="60" r="6" fill="#f97316"/>
          <text x="35" y="65" class="text-xs fill-gray-600">100K - 200K</text>
          
          <circle cx="20" cy="80" r="8" fill="#eab308"/>
          <text x="35" y="85" class="text-xs fill-gray-600">200K - 300K</text>
          
          <circle cx="20" cy="100" r="10" fill="#22c55e"/>
          <text x="35" y="105" class="text-xs fill-gray-600">&gt; 300K</text>
        </g>
      </svg>
      
      <!-- Tooltip -->
      <div
        v-if="tooltip.show"
        :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }"
        class="absolute z-10 bg-gray-900 text-white text-sm rounded-lg px-3 py-2 pointer-events-none transform -translate-x-1/2 -translate-y-full"
      >
        <div class="font-medium">{{ tooltip.lga?.name }}</div>
        <div class="text-xs text-gray-300">{{ tooltip.lga?.headquarters }}</div>
        <div class="text-xs text-gray-300">
          Population: {{ formatPopulation(tooltip.lga?.population_estimate || 0) }}
        </div>
        <div class="text-xs text-gray-300">
          Area: {{ formatArea(tooltip.lga?.area_km2 || 0) }}
        </div>
        <div class="text-xs text-gray-300">
          Wards: {{ tooltip.lga?.wards.length || 0 }}
        </div>
        <!-- Tooltip arrow -->
        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
      </div>
    </div>
    
    <!-- Selected LGA Info -->
    <div v-if="selectedLGA" class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <h4 class="text-lg font-medium text-blue-900">{{ selectedLGA.name }}</h4>
          <p class="text-sm text-blue-700">Headquarters: {{ selectedLGA.headquarters }}</p>
          <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-blue-600">Population:</span>
              <span class="font-medium ml-1">{{ formatPopulation(selectedLGA.population_estimate) }}</span>
            </div>
            <div>
              <span class="text-blue-600">Area:</span>
              <span class="font-medium ml-1">{{ formatArea(selectedLGA.area_km2) }}</span>
            </div>
            <div>
              <span class="text-blue-600">Wards:</span>
              <span class="font-medium ml-1">{{ selectedLGA.wards.length }}</span>
            </div>
            <div>
              <span class="text-blue-600">Code:</span>
              <span class="font-medium ml-1">{{ selectedLGA.code }}</span>
            </div>
          </div>
          <div class="mt-2 text-xs text-blue-600">
            Coordinates: {{ selectedLGA.coordinates.latitude.toFixed(4) }}, {{ selectedLGA.coordinates.longitude.toFixed(4) }}
          </div>
        </div>
        <button
          @click="selectedLGA = null"
          class="ml-4 text-blue-400 hover:text-blue-600"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <!-- Ward List -->
      <div class="mt-4">
        <h5 class="text-sm font-medium text-blue-900 mb-2">Wards ({{ selectedLGA.wards.length }})</h5>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
          <div
            v-for="ward in selectedLGA.wards"
            :key="ward.id"
            class="text-xs bg-white border border-blue-200 rounded px-2 py-1"
          >
            {{ ward.name }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { nigerStateLGAs, type LocalGovernmentArea } from '@/data/nigerState';

interface LGAPosition extends LocalGovernmentArea {
  x: number;
  y: number;
}

// Props
interface Props {
  width?: number;
  height?: number;
}

const props = withDefaults(defineProps<Props>(), {
  width: 800,
  height: 600
});

// Reactive data
const mapContainer = ref<HTMLElement>();
const selectedLGA = ref<LocalGovernmentArea | null>(null);
const tooltip = ref({
  show: false,
  x: 0,
  y: 0,
  lga: null as LocalGovernmentArea | null
});

// Computed properties
const mapWidth = computed(() => props.width);
const mapHeight = computed(() => props.height);

// Convert lat/lng to SVG coordinates (simplified projection)
const lgasWithPositions = computed((): LGAPosition[] => {
  // Niger State approximate bounds
  const bounds = {
    north: 11.8,
    south: 8.2,
    east: 7.8,
    west: 3.0
  };
  
  const mapBounds = {
    left: 50,
    right: 750,
    top: 50,
    bottom: 550
  };
  
  return nigerStateLGAs.map(lga => {
    // Simple linear projection
    const x = mapBounds.left + 
      ((lga.coordinates.longitude - bounds.west) / (bounds.east - bounds.west)) * 
      (mapBounds.right - mapBounds.left);
    
    const y = mapBounds.bottom - 
      ((lga.coordinates.latitude - bounds.south) / (bounds.north - bounds.south)) * 
      (mapBounds.bottom - mapBounds.top);
    
    return {
      ...lga,
      x: Math.max(mapBounds.left, Math.min(mapBounds.right, x)),
      y: Math.max(mapBounds.top, Math.min(mapBounds.bottom, y))
    };
  });
});

// Methods
const getLGARadius = (lga: LocalGovernmentArea): number => {
  const population = lga.population_estimate;
  if (population > 300000) return 10;
  if (population > 200000) return 8;
  if (population > 100000) return 6;
  return 4;
};

const getLGAColor = (lga: LocalGovernmentArea): string => {
  const population = lga.population_estimate;
  if (population > 300000) return '#22c55e'; // green
  if (population > 200000) return '#eab308'; // yellow
  if (population > 100000) return '#f97316'; // orange
  return '#ef4444'; // red
};

const selectLGA = (lga: LocalGovernmentArea) => {
  selectedLGA.value = selectedLGA.value?.id === lga.id ? null : lga;
  hideTooltip();
};

const showTooltip = (lga: LocalGovernmentArea, event: MouseEvent) => {
  if (selectedLGA.value?.id === lga.id) return;
  
  const rect = mapContainer.value?.getBoundingClientRect();
  if (rect) {
    tooltip.value = {
      show: true,
      x: event.clientX - rect.left,
      y: event.clientY - rect.top,
      lga
    };
  }
};

const hideTooltip = () => {
  tooltip.value.show = false;
};

const formatPopulation = (population: number): string => {
  if (population >= 1000000) {
    return `${(population / 1000000).toFixed(1)}M`;
  } else if (population >= 1000) {
    return `${(population / 1000).toFixed(0)}K`;
  }
  return population.toLocaleString();
};

const formatArea = (area: number): string => {
  return `${area.toLocaleString()} km²`;
};

// Emit events
const emit = defineEmits<{
  lgaSelected: [lga: LocalGovernmentArea | null];
}>();

// Watch for LGA selection changes
import { watch } from 'vue';
watch(selectedLGA, (newLGA) => {
  emit('lgaSelected', newLGA);
});
</script>

<style scoped>
.niger-state-map {
  @apply relative;
}

.map-container {
  @apply relative bg-gray-50 rounded-lg border border-gray-200;
}
</style>
