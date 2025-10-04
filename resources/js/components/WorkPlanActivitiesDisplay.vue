<template>
  <v-card class="elevation-2 mb-6">
    <v-card-title class="text-h6 font-weight-bold d-flex align-center">
      <v-icon class="mr-2">mdi-clipboard-list</v-icon>
      Work Plan Activities
      <v-spacer />
      <v-chip
        v-if="project.work_plan_presentation"
        color="success"
        size="small"
        variant="tonal"
      >
        <v-icon start>mdi-check</v-icon>
        Enabled
      </v-chip>
      <v-chip
        v-else
        color="grey"
        size="small"
        variant="tonal"
      >
        <v-icon start>mdi-close</v-icon>
        Disabled
      </v-chip>
    </v-card-title>
    
    <v-card-text>
      <!-- Work Plan Enabled with Activities -->
      <div v-if="project.work_plan_presentation && activities.length > 0">
        <v-table class="work-plan-table">
          <thead>
            <tr>
              <th class="text-left">Activity No.</th>
              <th class="text-left">Planned Activity</th>
              <th class="text-left">Start Date (Planned)</th>
              <th class="text-left">End Date (Planned)</th>
              <th class="text-left">Actual Start Date</th>
              <th class="text-left">Actual End Date</th>
              <th class="text-left">Status</th>
              <th class="text-left">% Complete</th>
              <th class="text-left">Variance/Comments</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="activity in activities" :key="activity.id">
              <td class="font-mono">{{ activity.activity_number || '-' }}</td>
              <td>
                <div class="text-body-2">{{ activity.activity_name }}</div>
              </td>
              <td>{{ formatDate(activity.planned_start_date) }}</td>
              <td>{{ formatDate(activity.planned_end_date) }}</td>
              <td>{{ formatDate(activity.actual_start_date) || '-' }}</td>
              <td>{{ formatDate(activity.actual_end_date) || '-' }}</td>
              <td>
                <v-chip
                  :color="getStatusColor(activity.status)"
                  size="small"
                  variant="tonal"
                >
                  {{ getStatusText(activity.status) }}
                </v-chip>
              </td>
              <td>
                <div class="d-flex align-center">
                  <v-progress-circular
                    :model-value="activity.percentage_complete"
                    size="24"
                    width="3"
                    :color="getProgressColor(activity.percentage_complete)"
                    class="mr-2"
                  />
                  <span class="text-body-2">{{ activity.percentage_complete }}%</span>
                </div>
              </td>
              <td>
                <div class="text-body-2">{{ activity.variance_comments || '-' }}</div>
              </td>
            </tr>
          </tbody>
        </v-table>

        <!-- Summary Stats -->
        <v-row class="mt-4">
          <v-col cols="12" md="3">
            <v-card variant="tonal" color="primary">
              <v-card-text class="text-center">
                <div class="text-h4 font-weight-bold">{{ activities.length }}</div>
                <div class="text-body-2">Total Activities</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card variant="tonal" color="success">
              <v-card-text class="text-center">
                <div class="text-h4 font-weight-bold">{{ completedActivities }}</div>
                <div class="text-body-2">Completed</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card variant="tonal" color="warning">
              <v-card-text class="text-center">
                <div class="text-h4 font-weight-bold">{{ inProgressActivities }}</div>
                <div class="text-body-2">In Progress</div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card variant="tonal" color="info">
              <v-card-text class="text-center">
                <div class="text-h4 font-weight-bold">{{ averageProgress.toFixed(1) }}%</div>
                <div class="text-body-2">Avg Progress</div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </div>

      <!-- Work Plan Enabled but No Activities -->
      <div v-else-if="project.work_plan_presentation && activities.length === 0" class="text-center py-8">
        <v-icon size="64" color="grey-lighten-1">mdi-clipboard-list-outline</v-icon>
        <div class="text-h6 text-grey-darken-1 mt-2">No activities added yet</div>
        <div class="text-body-2 text-grey-darken-1 mb-4">
          Work plan presentation is enabled but no activities have been added
        </div>
      </div>

      <!-- Work Plan Disabled -->
      <div v-else class="text-center py-6">
        <v-icon size="48" color="grey-lighten-2">mdi-clipboard-off-outline</v-icon>
        <div class="text-body-1 text-grey-darken-1 mt-2">
          Work plan presentation is disabled
        </div>
        <div class="text-body-2 text-grey-darken-1">
          This project does not use detailed work plan tracking
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface WorkPlanActivity {
  id: number;
  activity_number: string;
  activity_name: string;
  planned_start_date: string;
  planned_end_date: string;
  actual_start_date: string | null;
  actual_end_date: string | null;
  status: string;
  percentage_complete: number;
  variance_comments: string | null;
  is_tracked: boolean;
  is_completed: boolean;
}

interface Project {
  id: number;
  name: string;
  work_plan_presentation: boolean;
}

interface Props {
  project: Project;
  activities: WorkPlanActivity[];
}

const props = defineProps<Props>();

const completedActivities = computed(() => {
  return props.activities.filter(activity => activity.status === 'completed').length;
});

const inProgressActivities = computed(() => {
  return props.activities.filter(activity => 
    activity.status === 'in_progress' || activity.status === 'on_track'
  ).length;
});

const averageProgress = computed(() => {
  if (props.activities.length === 0) return 0;
  const total = props.activities.reduce((sum, activity) => sum + activity.percentage_complete, 0);
  return total / props.activities.length;
});

const formatDate = (dateString: string | null): string => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString();
};

const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    'not_started': 'grey',
    'in_progress': 'blue',
    'on_track': 'green',
    'delayed': 'orange',
    'completed': 'success',
    'on_hold': 'warning',
    'cancelled': 'error'
  };
  return colors[status] || 'grey';
};

const getStatusText = (status: string): string => {
  const texts: Record<string, string> = {
    'not_started': 'Not Started',
    'in_progress': 'In Progress',
    'on_track': 'On Track',
    'delayed': 'Delayed',
    'completed': 'Completed',
    'on_hold': 'On Hold',
    'cancelled': 'Cancelled'
  };
  return texts[status] || status;
};

const getProgressColor = (progress: number): string => {
  if (progress >= 80) return 'success';
  if (progress >= 50) return 'warning';
  if (progress >= 25) return 'info';
  return 'error';
};
</script>

<style scoped>
.work-plan-table {
  border: 1px solid rgba(0, 0, 0, 0.12);
}

.work-plan-table th {
  background-color: #f5f5f5;
  font-weight: 600;
  padding: 12px 8px;
  border-bottom: 2px solid rgba(0, 0, 0, 0.12);
}

.work-plan-table td {
  padding: 8px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  vertical-align: top;
}

.work-plan-table td:not(:last-child) {
  border-right: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
