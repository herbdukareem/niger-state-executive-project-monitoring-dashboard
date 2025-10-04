<template>
  <div class="project-statistics">
    <v-row>
      <!-- Overview Cards -->
      <v-col cols="12" md="3" v-for="stat in overviewStats" :key="stat.title">
        <v-card class="text-center pa-4" :color="stat.color" variant="tonal">
          <v-card-text>
            <div class="text-h4 font-weight-bold">{{ stat.value }}</div>
            <div class="text-subtitle-1">{{ stat.title }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <!-- Projects by LGA -->
      <v-col cols="12" lg="6">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2">mdi-map-marker-multiple</v-icon>
            Projects by Local Government Area
          </v-card-title>
          <v-card-text>
            <div class="lga-stats">
              <div 
                v-for="lga in lgaStats" 
                :key="lga.name"
                class="lga-item mb-3"
              >
                <div class="d-flex justify-space-between align-center mb-1">
                  <span class="font-weight-medium">{{ lga.name }}</span>
                  <span class="text-primary font-weight-bold">{{ lga.count }}</span>
                </div>
                <v-progress-linear
                  :model-value="(lga.count / totalProjects) * 100"
                  color="primary"
                  height="6"
                  rounded
                />
                <div class="text-caption text-grey mt-1">
                  Budget: ₦{{ formatCurrency(lga.totalBudget) }}
                </div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Projects by Status -->
      <v-col cols="12" lg="6">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2">mdi-chart-donut</v-icon>
            Projects by Status
          </v-card-title>
          <v-card-text>
            <div class="status-stats">
              <div 
                v-for="status in statusStats" 
                :key="status.status"
                class="status-item mb-3"
              >
                <div class="d-flex justify-space-between align-center mb-1">
                  <div class="d-flex align-center">
                    <v-chip 
                      :color="getStatusColor(status.status)" 
                      size="small" 
                      class="mr-2"
                    >
                      {{ formatStatus(status.status) }}
                    </v-chip>
                  </div>
                  <span class="text-primary font-weight-bold">{{ status.count }}</span>
                </div>
                <v-progress-linear
                  :model-value="(status.count / totalProjects) * 100"
                  :color="getStatusColor(status.status)"
                  height="6"
                  rounded
                />
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <!-- Projects by Zone -->
      <v-col cols="12" lg="4">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2">mdi-map-outline</v-icon>
            Projects by Zone
          </v-card-title>
          <v-card-text>
            <div class="zone-stats">
              <div 
                v-for="zone in zoneStats" 
                :key="zone.name"
                class="zone-item mb-3"
              >
                <div class="d-flex justify-space-between align-center mb-1">
                  <span class="font-weight-medium">{{ zone.name }}</span>
                  <span class="text-primary font-weight-bold">{{ zone.count }}</span>
                </div>
                <v-progress-linear
                  :model-value="(zone.count / totalProjects) * 100"
                  color="secondary"
                  height="6"
                  rounded
                />
                <div class="text-caption text-grey mt-1">
                  LGAs: {{ zone.lgas.join(', ') }}
                </div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Budget Distribution -->
      <v-col cols="12" lg="4">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2">mdi-currency-ngn</v-icon>
            Budget Distribution
          </v-card-title>
          <v-card-text>
            <div class="budget-stats">
              <div class="mb-4">
                <div class="text-h6 text-center">₦{{ formatCurrency(totalBudget) }}</div>
                <div class="text-caption text-center text-grey">Total Budget</div>
              </div>
              
              <div 
                v-for="range in budgetRanges" 
                :key="range.label"
                class="budget-item mb-3"
              >
                <div class="d-flex justify-space-between align-center mb-1">
                  <span class="text-caption">{{ range.label }}</span>
                  <span class="text-primary font-weight-bold">{{ range.count }}</span>
                </div>
                <v-progress-linear
                  :model-value="(range.count / totalProjects) * 100"
                  color="success"
                  height="6"
                  rounded
                />
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Progress Overview -->
      <v-col cols="12" lg="4">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2">mdi-progress-check</v-icon>
            Progress Overview
          </v-card-title>
          <v-card-text>
            <div class="progress-stats">
              <div class="mb-4">
                <div class="text-h6 text-center">{{ averageProgress.toFixed(1) }}%</div>
                <div class="text-caption text-center text-grey">Average Progress</div>
              </div>
              
              <div 
                v-for="range in progressRanges" 
                :key="range.label"
                class="progress-item mb-3"
              >
                <div class="d-flex justify-space-between align-center mb-1">
                  <span class="text-caption">{{ range.label }}</span>
                  <span class="text-primary font-weight-bold">{{ range.count }}</span>
                </div>
                <v-progress-linear
                  :model-value="(range.count / totalProjects) * 100"
                  :color="range.color"
                  height="6"
                  rounded
                />
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Top Performing LGAs -->
    <v-row class="mt-4">
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2">mdi-trophy</v-icon>
            Top Performing LGAs by Progress
          </v-card-title>
          <v-card-text>
            <v-data-table
              :headers="performanceHeaders"
              :items="lgaPerformance"
              :items-per-page="10"
              class="elevation-0"
            >
              <template #item.averageProgress="{ item }">
                <div class="d-flex align-center">
                  <v-progress-linear
                    :model-value="item.averageProgress"
                    :color="getProgressColor(item.averageProgress)"
                    height="8"
                    rounded
                    class="mr-2"
                    style="min-width: 100px;"
                  />
                  <span class="font-weight-bold">{{ item.averageProgress.toFixed(1) }}%</span>
                </div>
              </template>
              <template #item.totalBudget="{ item }">
                ₦{{ formatCurrency(item.totalBudget) }}
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { nigerStateLGAs } from '@/data/nigerState';

interface Project {
  id: number;
  name: string;
  status: string;
  progress_percentage: number;
  total_budget: number;
  lga_id?: number;
  lga_name?: string;
  ward_name?: string;
}

interface Props {
  projects: Project[];
}

const props = defineProps<Props>();

// Niger State zones mapping
const nigerStateZones = {
  'Zone A': ['Agaie', 'Bida', 'Edati', 'Gbako', 'Katcha', 'Lapai', 'Lavun'],
  'Zone B': ['Agwara', 'Borgu', 'Mariga', 'Mashegu', 'Wushishi'],
  'Zone C': ['Bosso', 'Chanchaga', 'Gurara', 'Minna', 'Paikoro', 'Shiroro', 'Suleja', 'Tafa']
};

// Computed properties
const totalProjects = computed(() => props.projects.length);
const totalBudget = computed(() => props.projects.reduce((sum, p) => sum + p.total_budget, 0));
const averageProgress = computed(() => {
  if (totalProjects.value === 0) return 0;
  return props.projects.reduce((sum, p) => sum + p.progress_percentage, 0) / totalProjects.value;
});

const overviewStats = computed(() => [
  { title: 'Total Projects', value: totalProjects.value, color: 'primary' },
  { title: 'Active Projects', value: props.projects.filter(p => p.status === 'in_progress').length, color: 'info' },
  { title: 'Completed', value: props.projects.filter(p => p.status === 'completed').length, color: 'success' },
  { title: 'Total Budget', value: `₦${formatCurrency(totalBudget.value)}`, color: 'warning' }
]);

const lgaStats = computed(() => {
  const stats = new Map();
  
  props.projects.forEach(project => {
    const lgaName = project.lga_name || 'Unknown';
    if (!stats.has(lgaName)) {
      stats.set(lgaName, { name: lgaName, count: 0, totalBudget: 0 });
    }
    const stat = stats.get(lgaName);
    stat.count++;
    stat.totalBudget += project.total_budget;
  });
  
  return Array.from(stats.values()).sort((a, b) => b.count - a.count);
});

const statusStats = computed(() => {
  const stats = new Map();
  
  props.projects.forEach(project => {
    if (!stats.has(project.status)) {
      stats.set(project.status, { status: project.status, count: 0 });
    }
    stats.get(project.status).count++;
  });
  
  return Array.from(stats.values()).sort((a, b) => b.count - a.count);
});

const zoneStats = computed(() => {
  const stats = Object.keys(nigerStateZones).map(zoneName => ({
    name: zoneName,
    count: 0,
    lgas: nigerStateZones[zoneName as keyof typeof nigerStateZones]
  }));
  
  props.projects.forEach(project => {
    const lgaName = project.lga_name;
    if (lgaName) {
      const zone = stats.find(z => z.lgas.includes(lgaName));
      if (zone) zone.count++;
    }
  });
  
  return stats.sort((a, b) => b.count - a.count);
});

const budgetRanges = computed(() => {
  const ranges = [
    { label: '< ₦1M', min: 0, max: 1000000, count: 0 },
    { label: '₦1M - ₦10M', min: 1000000, max: 10000000, count: 0 },
    { label: '₦10M - ₦50M', min: 10000000, max: 50000000, count: 0 },
    { label: '₦50M - ₦100M', min: 50000000, max: 100000000, count: 0 },
    { label: '> ₦100M', min: 100000000, max: Infinity, count: 0 }
  ];
  
  props.projects.forEach(project => {
    const range = ranges.find(r => project.total_budget >= r.min && project.total_budget < r.max);
    if (range) range.count++;
  });
  
  return ranges;
});

const progressRanges = computed(() => {
  const ranges = [
    { label: '0-25%', min: 0, max: 25, count: 0, color: 'red' },
    { label: '26-50%', min: 26, max: 50, count: 0, color: 'orange' },
    { label: '51-75%', min: 51, max: 75, count: 0, color: 'yellow' },
    { label: '76-100%', min: 76, max: 100, count: 0, color: 'green' }
  ];
  
  props.projects.forEach(project => {
    const range = ranges.find(r => project.progress_percentage >= r.min && project.progress_percentage <= r.max);
    if (range) range.count++;
  });
  
  return ranges;
});

const lgaPerformance = computed(() => {
  const performance = new Map();
  
  props.projects.forEach(project => {
    const lgaName = project.lga_name || 'Unknown';
    if (!performance.has(lgaName)) {
      performance.set(lgaName, {
        name: lgaName,
        projectCount: 0,
        totalProgress: 0,
        totalBudget: 0,
        averageProgress: 0
      });
    }
    const perf = performance.get(lgaName);
    perf.projectCount++;
    perf.totalProgress += project.progress_percentage;
    perf.totalBudget += project.total_budget;
  });
  
  return Array.from(performance.values())
    .map(perf => ({
      ...perf,
      averageProgress: perf.totalProgress / perf.projectCount
    }))
    .sort((a, b) => b.averageProgress - a.averageProgress);
});

const performanceHeaders = [
  { title: 'LGA', key: 'name', sortable: true },
  { title: 'Projects', key: 'projectCount', sortable: true },
  { title: 'Average Progress', key: 'averageProgress', sortable: true },
  { title: 'Total Budget', key: 'totalBudget', sortable: true }
];

// Methods
const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-NG').format(amount);
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
  if (progress >= 60) return 'yellow';
  if (progress >= 40) return 'orange';
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
</script>

<style scoped>
.lga-item, .status-item, .zone-item, .budget-item, .progress-item {
  padding: 8px 0;
}
</style>
