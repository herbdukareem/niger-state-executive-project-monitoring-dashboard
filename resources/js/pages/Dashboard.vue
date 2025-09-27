<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import ProjectMap from '@/components/ProjectMap.vue';
import { useRouter } from 'vue-router';
import { type BreadcrumbItemType } from '@/types';
import axios from 'axios';

const router = useRouter();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface Project {
  id: number;
  name: string;
  id_code: string;
  status: 'not_started' | 'in_progress' | 'on_hold' | 'completed' | 'cancelled';
  progress_percentage: number;
  total_budget: number;
  cumulative_expenditure: number;
  start_date: string;
  end_date: string;
  latitude?: number;
  longitude?: number;
  lga_id?: number;
  lga_name?: string;
  ward_id?: number;
  ward_name?: string;
  address?: string;
  location_description?: string;
  project_manager?: {
    id: number;
    name: string;
  };
  sector?: string;
  implementing_organization?: string;
}

interface DashboardStats {
  overview: {
    total_projects: number;
    active_projects: number;
    completed_projects: number;
    overdue_projects: number;
    total_budget: number;
    total_expenditure: number;
    budget_utilization: number;
    average_progress: number;
  };
  lga_stats: Array<{
    id: number;
    name: string;
    zone: string;
    projects_count: number;
    total_budget: number;
    average_progress: number;
  }>;
  zone_stats: Array<{
    name: string;
    projects_count: number;
    total_budget: number;
    average_progress: number;
  }>;
  recent_activity: Array<{
    id: number;
    name: string;
    id_code: string;
    status: string;
    progress_percentage: number;
    lga_name?: string;
    project_manager?: string;
    updated_at: string;
  }>;
  alerts: {
    overdue_projects: number;
    low_progress_projects: number;
    high_budget_utilization: number;
  };
}

const projects = ref<Project[]>([]);
const dashboardStats = ref<DashboardStats | null>(null);
const loading = ref(true);

const navigateTo = (routeName: string) => {
  router.push({ name: routeName });
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

// Computed properties for stats cards
const statsCards = computed(() => {
  if (!dashboardStats.value) return [];

  const stats = dashboardStats.value.overview;
  return [
    {
      title: 'Total Projects',
      value: stats.total_projects,
      icon: 'mdi-folder-multiple',
      color: 'primary',
      subtitle: 'All registered projects'
    },
    {
      title: 'Active Projects',
      value: stats.active_projects,
      icon: 'mdi-progress-clock',
      color: 'info',
      subtitle: 'Currently in progress'
    },
    {
      title: 'Completed',
      value: stats.completed_projects,
      icon: 'mdi-check-circle',
      color: 'success',
      subtitle: 'Successfully completed'
    },
    {
      title: 'Total Budget',
      value: formatCurrency(stats.total_budget),
      icon: 'mdi-currency-ngn',
      color: 'warning',
      subtitle: `${stats.budget_utilization}% utilized`
    }
  ];
});

const fetchDashboardData = async () => {
  try {
    const [projectsResponse, statsResponse] = await Promise.all([
      axios.get('/api/projects'),
      axios.get('/api/dashboard/stats')
    ]);

    projects.value = projectsResponse.data.data || [];
    dashboardStats.value = statsResponse.data;
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
    // Fallback to sample data for development
    projects.value = [];
    dashboardStats.value = {
      overview: {
        total_projects: 0,
        active_projects: 0,
        completed_projects: 0,
        overdue_projects: 0,
        total_budget: 0,
        total_expenditure: 0,
        budget_utilization: 0,
        average_progress: 0,
      },
      lga_stats: [],
      zone_stats: [],
      recent_activity: [],
      alerts: {
        overdue_projects: 0,
        low_progress_projects: 0,
        high_budget_utilization: 0,
      }
    };
  } finally {
    loading.value = false;
  }
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

const getStatusIcon = (status: string): string => {
  const icons = {
    not_started: 'mdi-clock-outline',
    in_progress: 'mdi-play-circle',
    on_hold: 'mdi-pause-circle',
    completed: 'mdi-check-circle',
    cancelled: 'mdi-close-circle'
  };
  return icons[status as keyof typeof icons] || 'mdi-clock-outline';
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

onMounted(() => {
  fetchDashboardData();
});
</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs" title="Executive Dashboard">
    <v-container fluid class="pa-4">
      <!-- Header -->
      <v-row class="mb-6">
        <v-col cols="12" md="8">
          <div>
            <h1 class="text-h4 font-weight-bold text-primary mb-2">Executive Dashboard</h1>
            <p class="text-subtitle-1 text-grey-darken-1">Project monitoring and oversight dashboard for Niger State</p>
          </div>
        </v-col>
        <v-col cols="12" md="4" class="d-flex align-center justify-end">
          <v-btn
            color="primary"
            size="large"
            @click="navigateTo('projects.create')"
            prepend-icon="mdi-plus"
          >
            New Project
          </v-btn>
        </v-col>
      </v-row>

      <!-- Loading State -->
      <v-row v-if="loading" class="mb-6">
        <v-col cols="12">
          <v-card>
            <v-card-text class="text-center pa-8">
              <v-progress-circular indeterminate color="primary" size="64" />
              <div class="mt-4 text-h6">Loading dashboard data...</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Dashboard Content -->
      <div v-else>
        <!-- Enhanced Stats Cards -->
        <v-row class="mb-6">
          <v-col
            v-for="(card, index) in statsCards"
            :key="card.title"
            cols="12"
            sm="6"
            lg="3"
          >
            <v-card
              :color="card.color"
              variant="tonal"
              class="h-100 animate__animated animate__fadeInUp stats-card-hover"
              :class="`stagger-${index + 1}`"
            >
              <v-card-text>
                <div class="d-flex align-center justify-space-between">
                  <div>
                    <div class="text-h4 font-weight-bold mb-1">
                      {{ card.value }}
                    </div>
                    <div class="text-subtitle-1 font-weight-medium">
                      {{ card.title }}
                    </div>
                    <div class="text-caption text-medium-emphasis">
                      {{ card.subtitle }}
                    </div>
                  </div>
                  <v-avatar :color="card.color" size="56" variant="flat">
                    <v-icon :icon="card.icon" size="28" />
                  </v-avatar>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Interactive Map -->
        <v-row class="mb-6">
          <v-col cols="12">
            <v-card class="animate__animated animate__fadeInUp stagger-5">
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-map-marker-multiple</v-icon>
                Project Locations Map
                <v-spacer />
                <v-chip color="primary" size="small" variant="flat">
                  <v-icon start>mdi-map-marker</v-icon>
                  {{ projects.filter(p => p.latitude && p.longitude).length }} Projects Mapped
                </v-chip>
              </v-card-title>
              <v-card-text class="pa-4">
                <ProjectMap
                  :projects="projects"
                  :height="500"
                  :center="[9.6167, 6.5500]"
                  :zoom="8"
                />
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
        <!-- Recent Activity and Alerts -->
        <v-row class="mb-6">
          <v-col cols="12" lg="8">
            <v-card class="animate__animated animate__fadeInUp stagger-6">
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-clock-outline</v-icon>
                Recent Project Activity
              </v-card-title>
              <v-card-text>
                <v-list v-if="dashboardStats?.recent_activity.length">
                  <v-list-item
                    v-for="activity in dashboardStats.recent_activity.slice(0, 5)"
                    :key="activity.id"
                    @click="navigateTo('projects.show', { id: activity.id })"
                    class="cursor-pointer"
                    rounded
                  >
                    <template #prepend>
                      <v-avatar :color="getStatusColor(activity.status)" size="40" variant="flat">
                        <v-icon color="white">{{ getStatusIcon(activity.status) }}</v-icon>
                      </v-avatar>
                    </template>
                    <v-list-item-title>{{ activity.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ activity.lga_name }} • {{ activity.progress_percentage }}% complete
                      <br>
                      <small class="text-caption">Updated {{ new Date(activity.updated_at).toLocaleDateString() }}</small>
                    </v-list-item-subtitle>
                    <template #append>
                      <v-chip :color="getStatusColor(activity.status)" size="small" variant="flat">
                        {{ formatStatus(activity.status) }}
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
                <div v-else class="text-center py-8 text-medium-emphasis">
                  <v-icon size="48" class="mb-2">mdi-clock-outline</v-icon>
                  <div>No recent activity</div>
                </div>
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn @click="navigateTo('projects.index')" color="primary" variant="flat">
                  <v-icon start>mdi-folder-multiple</v-icon>
                  View All Projects
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>

          <v-col cols="12" lg="4">
            <v-card class="animate__animated animate__fadeInUp stagger-6">
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-alert-circle-outline</v-icon>
                Alerts & Notifications
              </v-card-title>
              <v-card-text>
                <v-alert
                  v-if="dashboardStats?.alerts.overdue_projects > 0"
                  type="warning"
                  variant="tonal"
                  class="mb-3"
                  density="compact"
                >
                  <v-icon start>mdi-clock-alert</v-icon>
                  {{ dashboardStats.alerts.overdue_projects }} projects behind schedule
                </v-alert>
                <v-alert
                  v-if="dashboardStats?.alerts.low_progress_projects > 0"
                  type="info"
                  variant="tonal"
                  class="mb-3"
                  density="compact"
                >
                  <v-icon start>mdi-progress-alert</v-icon>
                  {{ dashboardStats.alerts.low_progress_projects }} projects with low progress
                </v-alert>
                <v-alert
                  v-if="dashboardStats?.alerts.high_budget_utilization > 0"
                  type="error"
                  variant="tonal"
                  class="mb-3"
                  density="compact"
                >
                  <v-icon start>mdi-currency-usd-off</v-icon>
                  {{ dashboardStats.alerts.high_budget_utilization }} projects over budget
                </v-alert>
                <v-alert
                  v-if="!dashboardStats?.alerts.overdue_projects && !dashboardStats?.alerts.low_progress_projects && !dashboardStats?.alerts.high_budget_utilization"
                  type="success"
                  variant="tonal"
                  density="compact"
                >
                  <v-icon start>mdi-check-circle</v-icon>
                  All projects are on track
                </v-alert>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Zone and LGA Performance -->
        <v-row v-if="dashboardStats?.zone_stats.length" class="mb-6">
          <v-col cols="12" md="6">
            <v-card class="animate__animated animate__fadeInUp stagger-4">
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-map-outline</v-icon>
                Zone Performance
              </v-card-title>
              <v-card-text>
                <v-list>
                  <v-list-item
                    v-for="zone in dashboardStats.zone_stats.slice(0, 3)"
                    :key="zone.name"
                  >
                    <v-list-item-title>{{ zone.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ zone.projects_count }} projects • {{ formatCurrency(zone.total_budget) }}
                    </v-list-item-subtitle>
                    <template #append>
                      <v-chip color="primary" size="small" variant="flat">
                        {{ zone.average_progress }}%
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <v-card class="animate__animated animate__fadeInUp stagger-5">
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-trophy</v-icon>
                Top Performing LGAs
              </v-card-title>
              <v-card-text>
                <v-list>
                  <v-list-item
                    v-for="lga in dashboardStats.lga_stats.slice(0, 3)"
                    :key="lga.id"
                  >
                    <v-list-item-title>{{ lga.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ lga.projects_count }} projects • {{ lga.zone }}
                    </v-list-item-subtitle>
                    <template #append>
                      <v-chip color="success" size="small" variant="flat">
                        {{ lga.average_progress }}%
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </div>
    </v-container>
  </AppSidebarLayout>
</template>


<style scoped>
.cursor-pointer {
  cursor: pointer;
}

/* Animation delays are now handled by the custom animations.css file */

/* Custom card hover effects */
.v-card {
  transition: all 0.3s ease;
}

.v-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Stats card enhancements */
.v-card[variant="tonal"] {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
}

/* List item hover effects */
.v-list-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.04);
}
</style>



