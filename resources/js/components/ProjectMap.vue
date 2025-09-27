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

interface Props {
  projects: Project[];
  height?: number;
  center?: [number, number];
  zoom?: number;
}

const props = withDefaults(defineProps<Props>(), {
  height: 400,
  center: () => [9.6167, 6.5500], // Niger State center
  zoom: 8
});

const router = useRouter();
const mapContainer = ref<HTMLElement>();
const map = ref<L.Map>();
const markers = ref<L.Marker[]>([]);
const mapView = ref('all');
const showProjectInfo = ref(false);
const selectedProject = ref<Project | null>(null);

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

  // Add markers for filtered projects
  filteredProjects.value.forEach(project => {
    if (project.latitude && project.longitude) {
      const marker = createProjectMarker(project);
      markers.value.push(marker);
      marker.addTo(map.value!);
    }
  });

  // Fit map to markers if any exist
  if (markers.value.length > 0) {
    const group = new L.FeatureGroup(markers.value);
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

// Global function for popup buttons
(window as any).viewProjectDetails = (projectId: number) => {
  router.push({ name: 'projects.show', params: { id: projectId } });
};

// Watchers
watch(() => props.projects, updateMarkers, { deep: true });
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
</style>
