<template>
  <div class="project-map">
    <div ref="mapContainer" class="map-container" :style="{ height: height + 'px' }"></div>
    
    <!-- Map Controls -->
    <div class="map-controls">
      <v-card class="pa-2 ma-2" style="position: absolute; top: 10px; right: 10px; z-index: 1000;">
        <v-btn-toggle v-model="mapView" mandatory>
          <v-btn value="all" size="small">All</v-btn>
          <v-btn value="active" size="small">Active</v-btn>
          <v-btn value="completed" size="small">Completed</v-btn>
        </v-btn-toggle>
      </v-card>

      <!-- Zone Legend -->
      <v-card
        v-if="showLgaMarkers && lgas && lgas.length > 0"
        class="pa-2 ma-2"
        style="position: absolute; bottom: 10px; right: 10px; z-index: 1000;"
      >
        <div class="zone-legend">
          <div class="legend-title text-caption font-weight-bold mb-1">LGA Zones</div>
          <div class="legend-items">
            <div class="legend-item d-flex align-center mb-1">
              <div class="legend-color mr-2" :style="{
                backgroundColor: '#3B82F6',
                width: '12px',
                height: '12px',
                borderRadius: '50%'
              }"></div>
              <span class="text-caption">Zone A</span>
            </div>
            <div class="legend-item d-flex align-center mb-1">
              <div class="legend-color mr-2" :style="{
                backgroundColor: '#10B981',
                width: '12px',
                height: '12px',
                borderRadius: '50%'
              }"></div>
              <span class="text-caption">Zone B</span>
            </div>
            <div class="legend-item d-flex align-center">
              <div class="legend-color mr-2" :style="{
                backgroundColor: '#F59E0B',
                width: '12px',
                height: '12px',
                borderRadius: '50%'
              }"></div>
              <span class="text-caption">Zone C</span>
            </div>
          </div>
        </div>
      </v-card>
    </div>

    <!-- Project Info Popup -->
    <v-dialog v-model="showProjectInfo" max-width="500">
      <v-card v-if="selectedProject">
        <v-card-title>{{ selectedProject.name }}</v-card-title>
        <v-card-text>
          <div class="mb-2">
            <strong>Code:</strong> {{ selectedProject.id_code }}
          </div>
          <div class="mb-2">
            <strong>Status:</strong> 
            <v-chip :color="getStatusColor(selectedProject.status)" size="small">
              {{ formatStatus(selectedProject.status) }}
            </v-chip>
          </div>
          <div class="mb-2">
            <strong>Budget:</strong> ₦{{ formatCurrency(selectedProject.total_budget) }}
          </div>
          <div class="mb-2">
            <strong>Location:</strong> {{ selectedProject.lga_name }}, {{ selectedProject.ward_name }}
          </div>
          <div class="mb-2">
            <strong>Progress:</strong> {{ selectedProject.progress_percentage }}%
          </div>
          <v-progress-linear 
            :model-value="selectedProject.progress_percentage" 
            :color="getProgressColor(selectedProject.progress_percentage)"
            height="8"
            rounded
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn @click="showProjectInfo = false">Close</v-btn>
          <v-btn color="primary" @click="viewProject(selectedProject)">View Details</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- LGA Info Dialog -->
    <v-dialog v-model="showLgaInfo" max-width="500">
      <v-card v-if="selectedLga">
        <v-card-title class="d-flex align-center">
          <v-icon class="mr-2">mdi-map-marker</v-icon>
          {{ selectedLga.name }} LGA
          <v-spacer />
          <v-chip :color="getLgaZoneColor(selectedLga.zone)" size="small" variant="flat">
            {{ selectedLga.zone }}
          </v-chip>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="6">
              <div class="text-caption text-grey-darken-1">Code</div>
              <div class="text-body-2 font-weight-medium">{{ selectedLga.code }}</div>
            </v-col>
            <v-col cols="6">
              <div class="text-caption text-grey-darken-1">Headquarters</div>
              <div class="text-body-2 font-weight-medium">{{ selectedLga.headquarters }}</div>
            </v-col>
            <v-col cols="6" v-if="selectedLga.population_estimate">
              <div class="text-caption text-grey-darken-1">Population</div>
              <div class="text-body-2 font-weight-medium">{{ selectedLga.population_estimate.toLocaleString() }}</div>
            </v-col>
            <v-col cols="6" v-if="selectedLga.area_km2">
              <div class="text-caption text-grey-darken-1">Area</div>
              <div class="text-body-2 font-weight-medium">{{ selectedLga.area_km2 }} km²</div>
            </v-col>
            <v-col cols="6" v-if="selectedLga.projects_count">
              <div class="text-caption text-grey-darken-1">Projects</div>
              <div class="text-body-2 font-weight-medium">{{ selectedLga.projects_count }}</div>
            </v-col>
            <v-col cols="6" v-if="selectedLga.average_progress">
              <div class="text-caption text-grey-darken-1">Avg Progress</div>
              <div class="text-body-2 font-weight-medium">{{ selectedLga.average_progress }}%</div>
            </v-col>
          </v-row>
          <div v-if="selectedLga.description" class="mt-3">
            <div class="text-caption text-grey-darken-1">Description</div>
            <div class="text-body-2">{{ selectedLga.description }}</div>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn @click="showLgaInfo = false">Close</v-btn>
          <v-btn color="primary" @click="viewLgaProjects(selectedLga)">View Projects</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix for default markers in Leaflet
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
});

interface Project {
  id: number;
  name: string;
  id_code: string;
  status: string;
  progress_percentage: number;
  total_budget: number;
  latitude?: number;
  longitude?: number;
  lga_name?: string;
  ward_name?: string;
}

interface LGA {
  id: number;
  name: string;
  code: string;
  headquarters: string;
  zone: string;
  latitude: number;
  longitude: number;
  population_estimate?: number;
  area_km2?: number;
  description?: string;
  projects_count?: number;
  total_budget?: number;
  average_progress?: number;
}

interface Props {
  projects: Project[];
  lgas?: LGA[];
  height?: number;
  center?: [number, number];
  zoom?: number;
  showLgaMarkers?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  lgas: () => [],
  height: 400,
  center: () => [9.6167, 6.5500], // Niger State center
  zoom: 8,
  showLgaMarkers: true
});

const router = useRouter();
const mapContainer = ref<HTMLElement>();
const map = ref<L.Map>();
const markers = ref<L.Marker[]>([]);
const lgaMarkers = ref<L.Marker[]>([]);
const mapView = ref('all');
const showProjectInfo = ref(false);
const selectedProject = ref<Project | null>(null);
const showLgaInfo = ref(false);
const selectedLga = ref<LGA | null>(null);

// Computed properties
const filteredProjects = computed(() => {
  if (mapView.value === 'all') return props.projects;
  if (mapView.value === 'active') return props.projects.filter(p => p.status === 'in_progress');
  if (mapView.value === 'completed') return props.projects.filter(p => p.status === 'completed');
  return props.projects;
});

// Methods
const initMap = () => {
  if (!mapContainer.value) return;

  map.value = L.map(mapContainer.value).setView(props.center, props.zoom);

  // Add tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map.value);

  // Add Niger State boundary (simplified)
  const nigerStateBounds = L.polygon([
    [11.8, 3.0],
    [11.8, 7.8],
    [8.2, 7.8],
    [8.2, 3.0]
  ], {
    color: '#4F46E5',
    weight: 2,
    opacity: 0.5,
    fillOpacity: 0.1
  }).addTo(map.value);

  updateMarkers();
};

const updateMarkers = () => {
  if (!map.value) return;

  // Clear existing markers
  markers.value.forEach(marker => map.value?.removeLayer(marker));
  markers.value = [];
  lgaMarkers.value.forEach(marker => map.value?.removeLayer(marker));
  lgaMarkers.value = [];

  // Add LGA markers if enabled (only for LGAs with projects)
  if (props.showLgaMarkers && props.lgas) {
    props.lgas.forEach(lga => {
      if (lga.latitude && lga.longitude && lga.projects_count && lga.projects_count > 0) {
        const marker = createLgaMarker(lga);
        lgaMarkers.value.push(marker);
        marker.addTo(map.value!);
      }
    });
  }

  // Add markers for filtered projects
  filteredProjects.value.forEach(project => {
    if (project.latitude && project.longitude) {
      const marker = createProjectMarker(project);
      markers.value.push(marker);
      marker.addTo(map.value!);
    }
  });

  // Fit map to markers if any exist
  const allMarkers = [...markers.value, ...lgaMarkers.value];
  if (allMarkers.length > 0) {
    const group = new L.FeatureGroup(allMarkers);
    map.value.fitBounds(group.getBounds().pad(0.1));
  }
};

const createProjectMarker = (project: Project): L.Marker => {
  const icon = L.divIcon({
    className: 'custom-marker',
    html: `
      <div class="marker-pin ${getMarkerClass(project.status)}" style="
        width: 30px;
        height: 30px;
        border-radius: 50% 50% 50% 0;
        background: ${getMarkerColor(project.status)};
        position: relative;
        transform: rotate(-45deg);
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
      ">
        <div style="
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%) rotate(45deg);
          color: white;
          font-size: 12px;
          font-weight: bold;
        ">
          ${getMarkerIcon(project.status)}
        </div>
      </div>
    `,
    iconSize: [30, 30],
    iconAnchor: [15, 30]
  });

  const marker = L.marker([project.latitude!, project.longitude!], { icon });
  
  marker.bindPopup(`
    <div style="min-width: 200px;">
      <h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: bold;">${project.name}</h4>
      <p style="margin: 4px 0; font-size: 12px;"><strong>Code:</strong> ${project.id_code}</p>
      <p style="margin: 4px 0; font-size: 12px;"><strong>Status:</strong> ${formatStatus(project.status)}</p>
      <p style="margin: 4px 0; font-size: 12px;"><strong>Progress:</strong> ${project.progress_percentage}%</p>
      <button onclick="window.viewProjectDetails(${project.id})" style="
        background: #4F46E5;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        margin-top: 8px;
      ">View Details</button>
    </div>
  `);

  marker.on('click', () => {
    selectedProject.value = project;
    showProjectInfo.value = true;
  });

  return marker;
};

const createLgaMarker = (lga: LGA): L.Marker => {
  const projectCount = lga.projects_count || 0;
  const markerSize = Math.min(Math.max(20 + (projectCount * 2), 24), 36); // Size based on project count

  const icon = L.divIcon({
    className: 'custom-lga-marker',
    html: `
      <div class="lga-marker" style="
        width: ${markerSize}px;
        height: ${markerSize}px;
        border-radius: 50%;
        background: ${getLgaMarkerColor(lga.zone)};
        border: 3px solid white;
        box-shadow: 0 3px 8px rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: ${Math.max(9, markerSize * 0.3)}px;
        font-weight: bold;
        color: white;
        position: relative;
      ">
        ${projectCount}
        <div style="
          position: absolute;
          bottom: -8px;
          left: 50%;
          transform: translateX(-50%);
          background: ${getLgaMarkerColor(lga.zone)};
          color: white;
          font-size: 8px;
          padding: 1px 4px;
          border-radius: 8px;
          white-space: nowrap;
          box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        ">
          ${lga.code}
        </div>
      </div>
    `,
    iconSize: [markerSize, markerSize + 12],
    iconAnchor: [markerSize / 2, markerSize / 2]
  });

  const marker = L.marker([lga.latitude, lga.longitude], { icon });

  marker.bindPopup(`
    <div style="min-width: 240px;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
        <h4 style="margin: 0; font-size: 14px; font-weight: bold;">${lga.name} LGA</h4>
        <span style="
          background: ${getLgaMarkerColor(lga.zone)};
          color: white;
          padding: 2px 8px;
          border-radius: 12px;
          font-size: 10px;
          font-weight: bold;
        ">${lga.zone}</span>
      </div>

      <div style="
        background: #f8f9fa;
        padding: 8px;
        border-radius: 6px;
        margin-bottom: 8px;
        text-align: center;
      ">
        <div style="font-size: 18px; font-weight: bold; color: ${getLgaMarkerColor(lga.zone)};">
          ${lga.projects_count || 0}
        </div>
        <div style="font-size: 11px; color: #666;">Active Projects</div>
      </div>

      <p style="margin: 4px 0; font-size: 12px;"><strong>Code:</strong> ${lga.code}</p>
      <p style="margin: 4px 0; font-size: 12px;"><strong>Headquarters:</strong> ${lga.headquarters}</p>
      ${lga.population_estimate ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Population:</strong> ${lga.population_estimate.toLocaleString()}</p>` : ''}
      ${lga.area_km2 ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Area:</strong> ${lga.area_km2} km²</p>` : ''}
      ${lga.average_progress ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Avg Progress:</strong> ${lga.average_progress}%</p>` : ''}

      <button onclick="window.viewLgaProjects(${lga.id})" style="
        background: ${getLgaMarkerColor(lga.zone)};
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
        margin-top: 8px;
        width: 100%;
      ">View Projects</button>
    </div>
  `);

  marker.on('click', () => {
    selectedLga.value = lga;
    showLgaInfo.value = true;
  });

  return marker;
};

const getLgaMarkerColor = (zone: string): string => {
  const colors = {
    'Zone A': '#3B82F6',
    'Zone B': '#10B981',
    'Zone C': '#F59E0B'
  };
  return colors[zone as keyof typeof colors] || '#6B7280';
};

const getMarkerColor = (status: string): string => {
  const colors = {
    not_started: '#6B7280',
    in_progress: '#3B82F6',
    on_hold: '#F59E0B',
    completed: '#10B981',
    cancelled: '#EF4444'
  };
  return colors[status as keyof typeof colors] || '#6B7280';
};

const getMarkerClass = (status: string): string => {
  return `marker-${status}`;
};

const getMarkerIcon = (status: string): string => {
  const icons = {
    not_started: '○',
    in_progress: '▶',
    on_hold: '⏸',
    completed: '✓',
    cancelled: '✕'
  };
  return icons[status as keyof typeof icons] || '○';
};

const getStatusColor = (status: string): string => {
  const colors = {
    not_started: 'grey',
    in_progress: 'blue',
    on_hold: 'orange',
    completed: 'green',
    cancelled: 'red'
  };
  return colors[status as keyof typeof colors] || 'grey';
};

const getProgressColor = (progress: number): string => {
  if (progress >= 80) return 'green';
  if (progress >= 50) return 'orange';
  return 'red';
};

const formatStatus = (status: string): string => {
  const labels = {
    not_started: 'Not Started',
    in_progress: 'In Progress',
    on_hold: 'On Hold',
    completed: 'Completed',
    cancelled: 'Cancelled'
  };
  return labels[status as keyof typeof labels] || status;
};

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-NG').format(amount);
};

const viewProject = (project: Project) => {
  router.push({ name: 'projects.show', params: { id: project.id } });
  showProjectInfo.value = false;
};

// Global functions for popup buttons
(window as any).viewProjectDetails = (projectId: number) => {
  router.push({ name: 'projects.show', params: { id: projectId } });
};

(window as any).viewLgaProjects = (lgaId: number) => {
  router.push({
    name: 'projects.index',
    query: { lga: lgaId }
  });
};

const getLgaZoneColor = (zone: string): string => {
  const colors = {
    'Zone A': 'primary',
    'Zone B': 'success',
    'Zone C': 'warning'
  };
  return colors[zone as keyof typeof colors] || 'grey';
};

const viewLgaProjects = (lga: LGA) => {
  // Navigate to projects filtered by LGA
  router.push({
    name: 'projects.index',
    query: { lga: lga.id }
  });
  showLgaInfo.value = false;
};

// Watchers
watch(() => props.projects, updateMarkers, { deep: true });
watch(() => props.lgas, updateMarkers, { deep: true });
watch(mapView, updateMarkers);

// Lifecycle
onMounted(() => {
  initMap();
});

onUnmounted(() => {
  if (map.value) {
    map.value.remove();
  }
});
</script>

<style scoped>
.project-map {
  position: relative;
}

.map-container {
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
}

.map-controls {
  position: relative;
}

:deep(.custom-marker) {
  background: transparent !important;
  border: none !important;
}

:deep(.custom-lga-marker) {
  background: transparent !important;
  border: none !important;
}

.lga-marker {
  transition: all 0.3s ease;
}

.lga-marker:hover {
  transform: scale(1.1);
}
</style>
