<template>
  <v-card class="elevation-2 mb-6">
    <v-card-title class="text-h6 font-weight-bold d-flex align-center">
      <v-icon class="mr-2">mdi-clipboard-list</v-icon>
      Work Plan Activities
      <v-spacer />
      <v-btn
        v-if="workPlanEnabled"
        color="primary"
        size="small"
        @click="addActivity"
        prepend-icon="mdi-plus"
      >
        Add Activity
      </v-btn>
    </v-card-title>
    
    <v-card-text>
      <!-- Work Plan Presentation Toggle -->
      <v-row class="mb-4">
        <v-col cols="12">
          <v-switch
            v-model="workPlanEnabled"
            label="Enable Work Plan Presentation"
            color="primary"
            hide-details
            @update:model-value="onWorkPlanToggle"
          />
          <div class="text-caption text-grey-darken-1 mt-1">
            Enable this to add detailed planned activities with tracking
          </div>
        </v-col>
      </v-row>

      <!-- Activities Table -->
      <div v-if="workPlanEnabled && activities.length > 0" class="activities-container">
        <div class="table-responsive">
          <v-table class="work-plan-table" fixed-header height="400px">
            <thead>
              <tr>
                <th class="table-header" style="min-width: 100px;">Activity No.</th>
                <th class="table-header" style="min-width: 200px;">Planned Activity</th>
                <th class="table-header" style="min-width: 150px;">Start Date (Planned)</th>
                <th class="table-header" style="min-width: 150px;">End Date (Planned)</th>
                <th class="table-header" style="min-width: 150px;">Actual Start Date</th>
                <th class="table-header" style="min-width: 150px;">Actual End Date</th>
                <th class="table-header" style="min-width: 180px;">Status</th>
                <th class="table-header" style="min-width: 120px;">% Complete</th>
                <th class="table-header" style="min-width: 150px;">Responsible Person</th>
                <th class="table-header" style="min-width: 200px;">Variance/Comments</th>
                <th class="table-header" style="min-width: 80px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(activity, index) in activities" :key="index" class="activity-row">
                <td class="table-cell">
                  <v-text-field
                    v-model="activity.activity_number"
                    variant="outlined"
                    density="compact"
                    hide-details
                    placeholder="e.g., 1.2"
                    class="activity-input"
                  />
                </td>
                <td class="table-cell">
                  <v-textarea
                    v-model="activity.activity_name"
                    variant="outlined"
                    density="compact"
                    rows="2"
                    hide-details
                    placeholder="Describe the planned activity"
                    class="activity-input"
                  />
                </td>
                <td class="table-cell">
                  <v-menu
                    v-model="activity.plannedStartMenu"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template #activator="{ props }">
                      <v-text-field
                        v-model="activity.planned_start_date"
                        v-bind="props"
                        variant="outlined"
                        density="compact"
                        hide-details
                        readonly
                        prepend-inner-icon="mdi-calendar"
                        placeholder="Select date"
                        class="activity-input"
                      />
                    </template>
                    <v-date-picker
                      v-model="activity.planned_start_date"
                      @update:model-value="activity.plannedStartMenu = false"
                    />
                  </v-menu>
                </td>
                <td class="table-cell">
                  <v-menu
                    v-model="activity.plannedEndMenu"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template #activator="{ props }">
                      <v-text-field
                        v-model="activity.planned_end_date"
                        v-bind="props"
                        variant="outlined"
                        density="compact"
                        hide-details
                        readonly
                        prepend-inner-icon="mdi-calendar"
                        placeholder="Select date"
                        class="activity-input"
                      />
                    </template>
                    <v-date-picker
                      v-model="activity.planned_end_date"
                      @update:model-value="activity.plannedEndMenu = false"
                    />
                  </v-menu>
                </td>
                <td class="table-cell">
                  <v-menu
                    v-model="activity.actualStartMenu"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template #activator="{ props }">
                      <v-text-field
                        v-model="activity.actual_start_date"
                        v-bind="props"
                        variant="outlined"
                        density="compact"
                        hide-details
                        readonly
                        prepend-inner-icon="mdi-calendar"
                        placeholder="Select date"
                        class="activity-input"
                        clearable
                      />
                    </template>
                    <v-date-picker
                      v-model="activity.actual_start_date"
                      @update:model-value="activity.actualStartMenu = false"
                    />
                  </v-menu>
                </td>
                <td class="table-cell">
                  <v-menu
                    v-model="activity.actualEndMenu"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template #activator="{ props }">
                      <v-text-field
                        v-model="activity.actual_end_date"
                        v-bind="props"
                        variant="outlined"
                        density="compact"
                        hide-details
                        readonly
                        prepend-inner-icon="mdi-calendar"
                        placeholder="Select date"
                        class="activity-input"
                        clearable
                      />
                    </template>
                    <v-date-picker
                      v-model="activity.actual_end_date"
                      @update:model-value="activity.actualEndMenu = false"
                    />
                  </v-menu>
                </td>
                <td class="table-cell">
                  <v-select
                    v-model="activity.status"
                    :items="statusOptions"
                    variant="outlined"
                    density="compact"
                    hide-details
                    class="activity-input"
                  />
                </td>
                <td class="table-cell">
                  <v-text-field
                    v-model.number="activity.percentage_complete"
                    type="number"
                    min="0"
                    max="100"
                    variant="outlined"
                    density="compact"
                    hide-details
                    suffix="%"
                    class="activity-input"
                  />
                </td>
                <td class="table-cell">
                  <v-text-field
                    v-model="activity.responsible_person"
                    variant="outlined"
                    density="compact"
                    hide-details
                    placeholder="Person responsible"
                    class="activity-input"
                  />
                </td>
                <td class="table-cell">
                  <v-textarea
                    v-model="activity.variance_comments"
                    variant="outlined"
                    density="compact"
                    rows="2"
                    hide-details
                    placeholder="Comments on progress or delays"
                    class="activity-input"
                  />
                </td>
                <td class="table-cell">
                  <v-btn
                    color="error"
                    size="small"
                    icon="mdi-delete"
                    variant="text"
                    @click="removeActivity(index)"
                  />
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>

        <!-- Activities Summary -->
        <v-card class="mt-4" variant="tonal" color="primary">
          <v-card-text>
            <v-row>
              <v-col cols="12" md="3">
                <div class="text-center">
                  <div class="text-h5 font-weight-bold">{{ activities.length }}</div>
                  <div class="text-body-2">Total Activities</div>
                </div>
              </v-col>
              <v-col cols="12" md="3">
                <div class="text-center">
                  <div class="text-h5 font-weight-bold">{{ completedCount }}</div>
                  <div class="text-body-2">Completed</div>
                </div>
              </v-col>
              <v-col cols="12" md="3">
                <div class="text-center">
                  <div class="text-h5 font-weight-bold">{{ inProgressCount }}</div>
                  <div class="text-body-2">In Progress</div>
                </div>
              </v-col>
              <v-col cols="12" md="3">
                <div class="text-center">
                  <div class="text-h5 font-weight-bold">{{ averageProgress }}%</div>
                  <div class="text-body-2">Avg Progress</div>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </div>

      <!-- Empty State -->
      <div v-else-if="workPlanEnabled && activities.length === 0" class="empty-state">
        <v-icon size="64" color="grey-lighten-1">mdi-clipboard-list-outline</v-icon>
        <div class="text-h6 text-grey-darken-1 mt-2">No activities added yet</div>
        <div class="text-body-2 text-grey-darken-1 mb-4">
          Add planned activities to track project progress in detail
        </div>
        <v-btn color="primary" @click="addActivity" prepend-icon="mdi-plus" size="large">
          Add First Activity
        </v-btn>

        <!-- Helper text -->
        <v-card class="mt-6 pa-4" variant="tonal" color="info">
          <div class="text-body-2">
            <strong>Tips for adding activities:</strong>
            <ul class="mt-2 ml-4">
              <li>Use activity numbers like 1.1, 1.2, 2.1 to organize tasks</li>
              <li>Be specific in activity descriptions</li>
              <li>Set realistic planned dates</li>
              <li>Assign responsible persons for accountability</li>
            </ul>
          </div>
        </v-card>
      </div>

      <!-- Disabled State -->
      <div v-else class="text-center py-6">
        <v-icon size="48" color="grey-lighten-2">mdi-clipboard-off-outline</v-icon>
        <div class="text-body-1 text-grey-darken-1 mt-2">
          Work plan presentation is disabled
        </div>
        <div class="text-body-2 text-grey-darken-1">
          Enable the toggle above to add planned activities
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface WorkPlanActivity {
  activity_number: string;
  activity_name: string;
  planned_start_date: string;
  planned_end_date: string;
  actual_start_date: string;
  actual_end_date: string;
  status: string;
  percentage_complete: number;
  responsible_person: string;
  variance_comments: string;
  is_tracked: boolean;
  is_completed: boolean;
  // Menu states for date pickers
  plannedStartMenu?: boolean;
  plannedEndMenu?: boolean;
  actualStartMenu?: boolean;
  actualEndMenu?: boolean;
}

interface Props {
  modelValue: boolean;
  activities: WorkPlanActivity[];
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void;
  (e: 'update:activities', value: WorkPlanActivity[]): void;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: false,
  activities: () => []
});

const emit = defineEmits<Emits>();

const workPlanEnabled = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const activities = computed({
  get: () => props.activities,
  set: (value) => emit('update:activities', value)
});

const statusOptions = ref([
  { title: 'Not Started', value: 'not_started' },
  { title: 'In Progress', value: 'in_progress' },
  { title: 'On Track', value: 'on_track' },
  { title: 'Delayed', value: 'delayed' },
  { title: 'Completed', value: 'completed' },
  { title: 'On Hold', value: 'on_hold' },
  { title: 'Cancelled', value: 'cancelled' }
]);

// Summary statistics
const completedCount = computed(() => {
  return activities.value.filter(activity => activity.status === 'completed').length;
});

const inProgressCount = computed(() => {
  return activities.value.filter(activity =>
    ['in_progress', 'on_track', 'delayed'].includes(activity.status)
  ).length;
});

const averageProgress = computed(() => {
  if (activities.value.length === 0) return 0;
  const total = activities.value.reduce((sum, activity) => sum + (activity.percentage_complete || 0), 0);
  return Math.round(total / activities.value.length);
});

const addActivity = () => {
  const newActivity: WorkPlanActivity = {
    activity_number: '',
    activity_name: '',
    planned_start_date: '',
    planned_end_date: '',
    actual_start_date: '',
    actual_end_date: '',
    status: 'not_started',
    percentage_complete: 0,
    responsible_person: '',
    variance_comments: '',
    is_tracked: true,
    is_completed: false,
    // Initialize menu states
    plannedStartMenu: false,
    plannedEndMenu: false,
    actualStartMenu: false,
    actualEndMenu: false
  };
  
  const updatedActivities = [...activities.value, newActivity];
  emit('update:activities', updatedActivities);
};

const removeActivity = (index: number) => {
  const updatedActivities = activities.value.filter((_, i) => i !== index);
  emit('update:activities', updatedActivities);
};

const onWorkPlanToggle = (enabled: boolean) => {
  if (!enabled) {
    // Clear activities when disabling work plan
    emit('update:activities', []);
  }
};

// Watch for status changes to auto-update completion
watch(activities, (newActivities) => {
  newActivities.forEach((activity) => {
    activity.is_completed = activity.status === 'completed';
    if (activity.is_completed && activity.percentage_complete < 100) {
      activity.percentage_complete = 100;
    }
  });
}, { deep: true });
</script>

<style scoped>
.activities-container {
  margin-top: 16px;
}

.table-responsive {
  overflow-x: auto;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 8px;
}

.work-plan-table {
  min-width: 1400px;
}

.table-header {
  background-color: #f8f9fa !important;
  font-weight: 600 !important;
  padding: 16px 12px !important;
  border-bottom: 2px solid rgba(0, 0, 0, 0.12) !important;
  color: #1a1a1a !important;
  font-size: 14px !important;
  white-space: nowrap;
  position: sticky;
  top: 0;
  z-index: 1;
}

.table-cell {
  padding: 12px !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
  vertical-align: top !important;
  background-color: white;
}

.table-cell:not(:last-child) {
  border-right: 1px solid rgba(0, 0, 0, 0.08) !important;
}

.activity-row:hover .table-cell {
  background-color: #f8f9fa;
}

.activity-input {
  min-width: 100%;
}

.activity-input :deep(.v-field) {
  font-size: 13px;
}

.activity-input :deep(.v-field__input) {
  min-height: 32px;
  padding: 4px 8px;
}

.activity-input :deep(.v-field__outline) {
  --v-field-border-width: 1px;
}

.activity-input :deep(.v-field--focused .v-field__outline) {
  --v-field-border-width: 2px;
}

/* Date picker menu styling */
:deep(.v-menu > .v-overlay__content) {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
  .table-responsive {
    overflow-x: scroll;
  }

  .work-plan-table {
    min-width: 1200px;
  }
}

/* Status select styling */
.activity-input :deep(.v-select .v-field) {
  min-height: 40px;
}

/* Textarea styling */
.activity-input :deep(.v-textarea .v-field) {
  min-height: 60px;
}

.activity-input :deep(.v-textarea .v-field__input) {
  padding: 8px;
}

/* Empty state styling */
.empty-state {
  text-align: center;
  padding: 48px 24px;
}

.empty-state ul {
  text-align: left;
  max-width: 400px;
  margin: 0 auto;
}

.empty-state li {
  margin-bottom: 4px;
}
</style>
