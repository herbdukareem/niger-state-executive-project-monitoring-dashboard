<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import ProjectMap from '@/components/ProjectMap.vue';
import ProjectStatistics from '@/components/ProjectStatistics.vue';
import { useRouter } from 'vue-router';
import { type BreadcrumbItemType } from '@/types';
import { sampleProjects, type Project } from '@/data/sampleProjects';

const router = useRouter();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const projects = ref<Project[]>([]);
const loading = ref(true);

const navigateTo = (routeName: string) => {
  router.push({ name: routeName });
};

const fetchProjects = async () => {
  try {
    loading.value = true;
    // In a real app, this would be an API call
    // const response = await axios.get('/api/projects');
    // projects.value = response.data;

    // For now, use sample data
    projects.value = sampleProjects;
  } catch (error) {
    console.error('Error fetching projects:', error);
    projects.value = sampleProjects; // Fallback to sample data
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
  fetchProjects();
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
        <!-- Project Statistics -->
        <v-row class="mb-6">
          <v-col cols="12">
            <ProjectStatistics :projects="projects" />
          </v-col>
        </v-row>

        <!-- Interactive Map -->
        <v-row class="mb-6">
          <v-col cols="12">
            <v-card>
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-map</v-icon>
                Project Locations Map
                <v-spacer />
                <v-chip color="primary" size="small">
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
        <!-- Recent Activity -->
        <v-row class="mb-6">
          <v-col cols="12" lg="8">
            <v-card>
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-clock-outline</v-icon>
                Recent Project Activity
              </v-card-title>
              <v-card-text>
                <v-list>
                  <v-list-item
                    v-for="project in projects.slice(0, 5)"
                    :key="project.id"
                    @click="navigateTo(`projects.show`)"
                    class="cursor-pointer"
                  >
                    <template #prepend>
                      <v-avatar :color="getStatusColor(project.status)" size="40">
                        <v-icon color="white">{{ getStatusIcon(project.status) }}</v-icon>
                      </v-avatar>
                    </template>
                    <v-list-item-title>{{ project.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ project.lga_name }} • {{ project.progress_percentage }}% complete
                    </v-list-item-subtitle>
                    <template #append>
                      <v-chip :color="getStatusColor(project.status)" size="small">
                        {{ formatStatus(project.status) }}
                      </v-chip>
                    </template>
                  </v-list-item>
                </v-list>
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn @click="navigateTo('projects.index')" color="primary">
                  View All Projects
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>

          <v-col cols="12" lg="4">
            <v-card>
              <v-card-title class="d-flex align-center">
                <v-icon class="mr-2">mdi-alert-circle-outline</v-icon>
                Alerts & Notifications
              </v-card-title>
              <v-card-text>
                <v-alert
                  type="warning"
                  variant="tonal"
                  class="mb-3"
                  density="compact"
                >
                  3 projects behind schedule
                </v-alert>
                <v-alert
                  type="info"
                  variant="tonal"
                  class="mb-3"
                  density="compact"
                >
                  2 budget reviews pending
                </v-alert>
                <v-alert
                  type="success"
                  variant="tonal"
                  density="compact"
                >
                  1 project completed this week
                </v-alert>
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
</style>



