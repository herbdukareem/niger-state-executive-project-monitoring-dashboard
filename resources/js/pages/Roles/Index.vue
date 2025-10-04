<template>
  <AppLayout>
    <template #header>
      <div class="d-flex align-center justify-space-between">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
            Roles & Permissions Management
          </h1>
          <p class="text-body-2 text-grey-darken-1">
            Manage system roles and their permissions
          </p>
        </div>
        <v-btn
          @click="openCreateRoleModal"
          color="primary"
          prepend-icon="mdi-plus"
          variant="elevated"
        >
          Add Role
        </v-btn>
      </div>
    </template>

    <v-container fluid class="pa-6">
      <!-- Tabs for Roles and Permissions -->
      <v-tabs v-model="activeTab" class="mb-6">
        <v-tab value="roles">
          <v-icon class="mr-2">mdi-account-group</v-icon>
          Roles
        </v-tab>
        <v-tab value="permissions">
          <v-icon class="mr-2">mdi-key</v-icon>
          Permissions
        </v-tab>
      </v-tabs>

      <v-tabs-window v-model="activeTab">
        <!-- Roles Tab -->
        <v-tabs-window-item value="roles">
          <!-- Filters -->
          <v-card class="mb-6" elevation="2">
            <v-card-text>
              <v-row>
                <v-col cols="12" sm="6" md="4">
                  <v-text-field
                    v-model="roleFilters.search"
                    label="Search roles..."
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    clearable
                    @input="debouncedSearchRoles"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="4">
                  <v-select
                    v-model="roleFilters.status"
                    :items="statusOptions"
                    label="Status"
                    variant="outlined"
                    density="compact"
                    clearable
                    @update:model-value="() => fetchRoles(1)"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="4">
                  <v-btn
                    @click="clearRoleFilters"
                    variant="outlined"
                    prepend-icon="mdi-filter-off"
                    class="mt-1"
                  >
                    Clear Filters
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Roles List -->
          <v-card elevation="2">
            <v-card-text>
              <div v-if="rolesLoading" class="text-center py-8">
                <v-progress-circular indeterminate color="primary" />
                <p class="mt-4 text-grey-darken-1">Loading roles...</p>
              </div>

              <div v-else-if="roles.length === 0" class="text-center py-8">
                <v-icon size="64" color="grey-lighten-1">mdi-account-group-outline</v-icon>
                <p class="text-h6 mt-4 text-grey-darken-1">No roles found</p>
                <p class="text-body-2 text-grey-darken-1">
                  {{ roleFilters.search ? 'Try adjusting your search criteria' : 'Create your first role to get started' }}
                </p>
              </div>

              <v-list v-else class="py-0">
                <v-list-item
                  v-for="role in roles"
                  :key="role.id"
                  class="mb-3 border rounded"
                  elevation="1"
                >
                  <template #prepend>
                    <v-avatar :color="getRoleColor(role.name)" size="48" class="mr-4">
                      <v-icon color="white">mdi-account-group</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="text-h6 font-weight-medium">
                    {{ role.display_name }}
                    <v-chip
                      :color="role.is_active ? 'success' : 'error'"
                      size="small"
                      class="ml-2"
                    >
                      {{ role.is_active ? 'Active' : 'Inactive' }}
                    </v-chip>
                  </v-list-item-title>

                  <v-list-item-subtitle class="mt-1">
                    <div class="text-body-2 text-grey-darken-1 mb-1">
                      {{ role.description || 'No description provided' }}
                    </div>
                    <div class="d-flex align-center gap-4">
                      <span class="text-caption">
                        <v-icon size="small" class="mr-1">mdi-shield-account</v-icon>
                        Level: {{ role.level }}
                      </span>
                      <span class="text-caption">
                        <v-icon size="small" class="mr-1">mdi-key</v-icon>
                        {{ role.permissions?.length || 0 }} permissions
                      </span>
                      <span class="text-caption">
                        <v-icon size="small" class="mr-1">mdi-account</v-icon>
                        {{ role.users?.length || 0 }} users
                      </span>
                    </div>
                  </v-list-item-subtitle>

                  <template #append>
                    <div class="d-flex align-center gap-2">
                      <v-btn
                        @click="editRole(role)"
                        icon="mdi-pencil"
                        size="small"
                        variant="text"
                        color="primary"
                      />
                      <v-btn
                        @click="toggleRoleStatus(role)"
                        :icon="role.is_active ? 'mdi-pause' : 'mdi-play'"
                        size="small"
                        variant="text"
                        :color="role.is_active ? 'warning' : 'success'"
                        :disabled="isSystemRole(role.name)"
                      />
                      <v-btn
                        @click="deleteRole(role)"
                        icon="mdi-delete"
                        size="small"
                        variant="text"
                        color="error"
                        :disabled="isSystemRole(role.name) || role.users?.length > 0"
                      />
                    </div>
                  </template>
                </v-list-item>
              </v-list>

              <!-- Pagination -->
              <div v-if="rolesMeta.total > rolesMeta.per_page" class="d-flex justify-center mt-6">
                <v-pagination
                  v-model="rolesMeta.current_page"
                  :length="Math.ceil(rolesMeta.total / rolesMeta.per_page)"
                  @update:model-value="fetchRoles"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-tabs-window-item>

        <!-- Permissions Tab -->
        <v-tabs-window-item value="permissions">
          <!-- Permission Filters -->
          <v-card class="mb-6" elevation="2">
            <v-card-text>
              <v-row>
                <v-col cols="12" sm="6" md="3">
                  <v-text-field
                    v-model="permissionFilters.search"
                    label="Search permissions..."
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    clearable
                    @input="debouncedSearchPermissions"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-select
                    v-model="permissionFilters.category"
                    :items="categoryOptions"
                    label="Category"
                    variant="outlined"
                    density="compact"
                    clearable
                    @update:model-value="() => fetchPermissions(1)"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-select
                    v-model="permissionFilters.status"
                    :items="statusOptions"
                    label="Status"
                    variant="outlined"
                    density="compact"
                    clearable
                    @update:model-value="() => fetchPermissions(1)"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-btn
                    @click="openCreatePermissionModal"
                    color="secondary"
                    prepend-icon="mdi-plus"
                    variant="elevated"
                    class="mt-1"
                  >
                    Add Permission
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Permissions List -->
          <v-card elevation="2">
            <v-card-text>
              <div v-if="permissionsLoading" class="text-center py-8">
                <v-progress-circular indeterminate color="primary" />
                <p class="mt-4 text-grey-darken-1">Loading permissions...</p>
              </div>

              <div v-else-if="permissions.length === 0" class="text-center py-8">
                <v-icon size="64" color="grey-lighten-1">mdi-key-outline</v-icon>
                <p class="text-h6 mt-4 text-grey-darken-1">No permissions found</p>
                <p class="text-body-2 text-grey-darken-1">
                  {{ permissionFilters.search ? 'Try adjusting your search criteria' : 'Create your first permission to get started' }}
                </p>
              </div>

              <v-list v-else class="py-0">
                <v-list-item
                  v-for="permission in permissions"
                  :key="permission.id"
                  class="mb-3 border rounded"
                  elevation="1"
                >
                  <template #prepend>
                    <v-avatar :color="getCategoryColor(permission.category)" size="48" class="mr-4">
                      <v-icon color="white">{{ getPermissionIcon(permission.category) }}</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="text-h6 font-weight-medium">
                    {{ permission.display_name }}
                    <v-chip
                      :color="permission.is_active ? 'success' : 'error'"
                      size="small"
                      class="ml-2"
                    >
                      {{ permission.is_active ? 'Active' : 'Inactive' }}
                    </v-chip>
                  </v-list-item-title>

                  <v-list-item-subtitle class="mt-1">
                    <div class="text-body-2 text-grey-darken-1 mb-1">
                      {{ permission.description || 'No description provided' }}
                    </div>
                    <div class="d-flex align-center gap-4">
                      <v-chip size="small" variant="outlined">
                        {{ permission.category }}
                      </v-chip>
                      <span class="text-caption">
                        <v-icon size="small" class="mr-1">mdi-account-group</v-icon>
                        {{ permission.roles?.length || 0 }} roles
                      </span>
                    </div>
                  </v-list-item-subtitle>

                  <template #append>
                    <div class="d-flex align-center gap-2">
                      <v-btn
                        @click="editPermission(permission)"
                        icon="mdi-pencil"
                        size="small"
                        variant="text"
                        color="primary"
                      />
                      <v-btn
                        @click="togglePermissionStatus(permission)"
                        :icon="permission.is_active ? 'mdi-pause' : 'mdi-play'"
                        size="small"
                        variant="text"
                        :color="permission.is_active ? 'warning' : 'success'"
                      />
                      <v-btn
                        @click="deletePermission(permission)"
                        icon="mdi-delete"
                        size="small"
                        variant="text"
                        color="error"
                        :disabled="permission.roles?.length > 0"
                      />
                    </div>
                  </template>
                </v-list-item>
              </v-list>

              <!-- Pagination -->
              <div v-if="permissionsMeta.total > permissionsMeta.per_page" class="d-flex justify-center mt-6">
                <v-pagination
                  v-model="permissionsMeta.current_page"
                  :length="Math.ceil(permissionsMeta.total / permissionsMeta.per_page)"
                  @update:model-value="fetchPermissions"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-tabs-window-item>
      </v-tabs-window>
    </v-container>

    <!-- Role Create/Edit Modal -->
    <v-dialog v-model="showRoleDialog" max-width="800px" persistent>
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon class="mr-2">mdi-account-group</v-icon>
          {{ editingRole ? 'Edit Role' : 'Create Role' }}
        </v-card-title>

        <v-card-text>
          <v-form ref="roleFormRef" v-model="roleFormValid">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="roleForm.name"
                  label="Role Name"
                  :rules="roleNameRules"
                  variant="outlined"
                  density="compact"
                  :disabled="editingRole && isSystemRole(editingRole.name)"
                  hint="Unique identifier for the role (lowercase, no spaces)"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="roleForm.display_name"
                  label="Display Name"
                  :rules="requiredRules"
                  variant="outlined"
                  density="compact"
                  hint="Human-readable name for the role"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="roleForm.description"
                  label="Description"
                  variant="outlined"
                  density="compact"
                  rows="3"
                  hint="Brief description of the role's purpose"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="roleForm.level"
                  label="Level"
                  type="number"
                  :rules="levelRules"
                  variant="outlined"
                  density="compact"
                  hint="Higher level = more permissions (0-100)"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-checkbox
                  v-model="roleForm.is_active"
                  label="Active"
                  :disabled="editingRole && isSystemRole(editingRole.name)"
                />
              </v-col>
            </v-row>

            <!-- Permissions Section -->
            <v-divider class="my-4" />
            <h3 class="text-h6 mb-4">Permissions</h3>

            <div v-if="permissionsLoading" class="text-center py-4">
              <v-progress-circular indeterminate size="24" />
              <span class="ml-2">Loading permissions...</span>
            </div>

            <div v-else>
              <div
                v-for="(categoryPermissions, category) in groupedPermissions"
                :key="category"
                class="mb-4"
              >
                <v-card variant="outlined">
                  <v-card-title class="text-subtitle-1 py-2">
                    <v-icon class="mr-2">{{ getPermissionIcon(category) }}</v-icon>
                    {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                    <v-spacer />
                    <v-btn
                      @click="toggleCategoryPermissions(category)"
                      size="small"
                      variant="text"
                    >
                      {{ isCategorySelected(category) ? 'Deselect All' : 'Select All' }}
                    </v-btn>
                  </v-card-title>
                  <v-card-text class="pt-0">
                    <v-row>
                      <v-col
                        v-for="permission in categoryPermissions"
                        :key="permission.id"
                        cols="12" sm="6" md="4"
                      >
                        <v-checkbox
                          v-model="roleForm.permissions"
                          :value="permission.id"
                          :label="permission.display_name"
                          density="compact"
                          hide-details
                        />
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </div>
            </div>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn @click="closeRoleDialog" variant="text">
            Cancel
          </v-btn>
          <v-btn
            @click="saveRole"
            color="primary"
            :loading="submittingRole"
            :disabled="!roleFormValid"
          >
            {{ editingRole ? 'Update' : 'Create' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Permission Create/Edit Modal -->
    <v-dialog v-model="showPermissionDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon class="mr-2">mdi-key</v-icon>
          {{ editingPermission ? 'Edit Permission' : 'Create Permission' }}
        </v-card-title>

        <v-card-text>
          <v-form ref="permissionFormRef" v-model="permissionFormValid">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="permissionForm.name"
                  label="Permission Name"
                  :rules="permissionNameRules"
                  variant="outlined"
                  density="compact"
                  hint="Unique identifier (lowercase, underscores)"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="permissionForm.display_name"
                  label="Display Name"
                  :rules="requiredRules"
                  variant="outlined"
                  density="compact"
                  hint="Human-readable name"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="permissionForm.description"
                  label="Description"
                  variant="outlined"
                  density="compact"
                  rows="3"
                  hint="Brief description of what this permission allows"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-select
                  v-model="permissionForm.category"
                  :items="permissionCategories"
                  label="Category"
                  :rules="requiredRules"
                  variant="outlined"
                  density="compact"
                  hint="Group this permission belongs to"
                  persistent-hint
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-checkbox
                  v-model="permissionForm.is_active"
                  label="Active"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn @click="closePermissionDialog" variant="text">
            Cancel
          </v-btn>
          <v-btn
            @click="savePermission"
            color="primary"
            :loading="submittingPermission"
            :disabled="!permissionFormValid"
          >
            {{ editingPermission ? 'Update' : 'Create' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar for notifications -->
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      :timeout="4000"
      location="top right"
    >
      {{ snackbar.message }}
      <template #actions>
        <v-btn
          variant="text"
          @click="snackbar.show = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';

// Types
interface Role {
  id: number;
  name: string;
  display_name: string;
  description?: string;
  level: number;
  is_active: boolean;
  permissions?: Permission[];
  users?: any[];
  created_at: string;
  updated_at: string;
}

interface Permission {
  id: number;
  name: string;
  display_name: string;
  description?: string;
  category: string;
  is_active: boolean;
  roles?: Role[];
  created_at: string;
  updated_at: string;
}

interface Meta {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
}

// Reactive data
const activeTab = ref('roles');
const roles = ref<Role[]>([]);
const permissions = ref<Permission[]>([]);
const rolesLoading = ref(true);
const permissionsLoading = ref(true);

const rolesMeta = reactive<Meta>({
  current_page: 1,
  per_page: 15,
  total: 0,
  last_page: 1
});

const permissionsMeta = reactive<Meta>({
  current_page: 1,
  per_page: 15,
  total: 0,
  last_page: 1
});

const roleFilters = reactive({
  search: '',
  status: ''
});

const permissionFilters = reactive({
  search: '',
  category: '',
  status: ''
});

// Modal states
const showRoleDialog = ref(false);
const showPermissionDialog = ref(false);
const editingRole = ref<Role | null>(null);
const editingPermission = ref<Permission | null>(null);
const submittingRole = ref(false);
const submittingPermission = ref(false);
const roleFormValid = ref(false);
const permissionFormValid = ref(false);

// Form data
const roleForm = reactive({
  name: '',
  display_name: '',
  description: '',
  level: 0,
  is_active: true,
  permissions: [] as number[]
});

const permissionForm = reactive({
  name: '',
  display_name: '',
  description: '',
  category: '',
  is_active: true
});

// Additional data
const groupedPermissions = ref<Record<string, Permission[]>>({});
const permissionCategories = ref<string[]>([]);

const snackbar = reactive({
  show: false,
  message: '',
  color: 'success'
});

// Computed properties
const statusOptions = computed(() => [
  { title: 'All Status', value: '' },
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' }
]);

const categoryOptions = computed(() => [
  { title: 'All Categories', value: '' },
  { title: 'Users', value: 'users' },
  { title: 'Projects', value: 'projects' },
  { title: 'Updates', value: 'updates' },
  { title: 'Settings', value: 'settings' },
  { title: 'Reports', value: 'reports' },
  { title: 'Dashboard', value: 'dashboard' }
]);

// Validation rules
const requiredRules = [
  (v: string) => !!v || 'This field is required'
];

const roleNameRules = [
  (v: string) => !!v || 'Role name is required',
  (v: string) => /^[a-z_]+$/.test(v) || 'Role name must be lowercase with underscores only',
  (v: string) => v.length >= 3 || 'Role name must be at least 3 characters'
];

const permissionNameRules = [
  (v: string) => !!v || 'Permission name is required',
  (v: string) => /^[a-z_]+$/.test(v) || 'Permission name must be lowercase with underscores only',
  (v: string) => v.length >= 3 || 'Permission name must be at least 3 characters'
];

const levelRules = [
  (v: number) => v >= 0 || 'Level must be 0 or greater',
  (v: number) => v <= 100 || 'Level must be 100 or less'
];

// Methods
const fetchRoles = async (page = 1) => {
  try {
    rolesLoading.value = true;
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: rolesMeta.per_page.toString(),
      ...(roleFilters.search && { search: roleFilters.search }),
      ...(roleFilters.status && { status: roleFilters.status })
    });

    const response = await axios.get(`/api/roles?${params}`);
    roles.value = response.data.data;
    
    Object.assign(rolesMeta, {
      current_page: response.data.current_page,
      per_page: response.data.per_page,
      total: response.data.total,
      last_page: response.data.last_page
    });
  } catch (error) {
    console.error('Error fetching roles:', error);
  } finally {
    rolesLoading.value = false;
  }
};

const fetchPermissions = async (page = 1) => {
  try {
    permissionsLoading.value = true;
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: permissionsMeta.per_page.toString(),
      ...(permissionFilters.search && { search: permissionFilters.search }),
      ...(permissionFilters.category && { category: permissionFilters.category }),
      ...(permissionFilters.status && { status: permissionFilters.status })
    });

    const response = await axios.get(`/api/permissions?${params}`);
    permissions.value = response.data.data;
    
    Object.assign(permissionsMeta, {
      current_page: response.data.current_page,
      per_page: response.data.per_page,
      total: response.data.total,
      last_page: response.data.last_page
    });
  } catch (error) {
    console.error('Error fetching permissions:', error);
  } finally {
    permissionsLoading.value = false;
  }
};

// Debounced search functions
let roleSearchTimeout: number | null = null;
const debouncedSearchRoles = () => {
  if (roleSearchTimeout) clearTimeout(roleSearchTimeout);
  roleSearchTimeout = window.setTimeout(() => {
    fetchRoles(1);
  }, 300);
};

let permissionSearchTimeout: number | null = null;
const debouncedSearchPermissions = () => {
  if (permissionSearchTimeout) clearTimeout(permissionSearchTimeout);
  permissionSearchTimeout = window.setTimeout(() => {
    fetchPermissions(1);
  }, 300);
};

// Utility functions
const isSystemRole = (roleName: string) => {
  return ['governor', 'super_admin', 'admin'].includes(roleName);
};

const getRoleColor = (roleName: string) => {
  const colors: Record<string, string> = {
    governor: 'purple',
    super_admin: 'red',
    admin: 'blue',
    project_manager: 'green',
    monitoring_and_evaluation_officers: 'orange'
  };
  return colors[roleName] || 'grey';
};

const getCategoryColor = (category: string) => {
  const colors: Record<string, string> = {
    users: 'blue',
    projects: 'green',
    updates: 'orange',
    settings: 'purple',
    reports: 'teal',
    dashboard: 'indigo'
  };
  return colors[category] || 'grey';
};

const getPermissionIcon = (category: string) => {
  const icons: Record<string, string> = {
    users: 'mdi-account-group',
    projects: 'mdi-folder-multiple',
    updates: 'mdi-update',
    settings: 'mdi-cog',
    reports: 'mdi-chart-line',
    dashboard: 'mdi-view-dashboard'
  };
  return icons[category] || 'mdi-key';
};

// Helper methods
const showNotification = (message: string, color: string = 'success') => {
  snackbar.message = message;
  snackbar.color = color;
  snackbar.show = true;
};

const fetchGroupedPermissions = async () => {
  try {
    const response = await axios.get('/api/permissions/grouped');
    groupedPermissions.value = response.data;
  } catch (error) {
    console.error('Error fetching grouped permissions:', error);
  }
};

const fetchPermissionCategories = async () => {
  try {
    const response = await axios.get('/api/permissions/categories');
    permissionCategories.value = response.data;
  } catch (error) {
    console.error('Error fetching permission categories:', error);
  }
};

const isCategorySelected = (category: string) => {
  const categoryPermissions = groupedPermissions.value[category] || [];
  return categoryPermissions.every(p => roleForm.permissions.includes(p.id));
};

const toggleCategoryPermissions = (category: string) => {
  const categoryPermissions = groupedPermissions.value[category] || [];
  const allSelected = isCategorySelected(category);

  if (allSelected) {
    // Remove all permissions from this category
    categoryPermissions.forEach(p => {
      const index = roleForm.permissions.indexOf(p.id);
      if (index > -1) {
        roleForm.permissions.splice(index, 1);
      }
    });
  } else {
    // Add all permissions from this category
    categoryPermissions.forEach(p => {
      if (!roleForm.permissions.includes(p.id)) {
        roleForm.permissions.push(p.id);
      }
    });
  }
};

// Action methods
const openCreateRoleModal = async () => {
  editingRole.value = null;
  resetRoleForm();
  await fetchGroupedPermissions();
  showRoleDialog.value = true;
};

const openCreatePermissionModal = async () => {
  editingPermission.value = null;
  resetPermissionForm();
  await fetchPermissionCategories();
  showPermissionDialog.value = true;
};

const editRole = async (role: Role) => {
  editingRole.value = role;
  roleForm.name = role.name;
  roleForm.display_name = role.display_name;
  roleForm.description = role.description || '';
  roleForm.level = role.level;
  roleForm.is_active = role.is_active;
  roleForm.permissions = role.permissions?.map(p => p.id) || [];

  await fetchGroupedPermissions();
  showRoleDialog.value = true;
};

const editPermission = async (permission: Permission) => {
  editingPermission.value = permission;
  permissionForm.name = permission.name;
  permissionForm.display_name = permission.display_name;
  permissionForm.description = permission.description || '';
  permissionForm.category = permission.category;
  permissionForm.is_active = permission.is_active;

  await fetchPermissionCategories();
  showPermissionDialog.value = true;
};

const saveRole = async () => {
  try {
    submittingRole.value = true;

    const url = editingRole.value ? `/api/roles/${editingRole.value.id}` : '/api/roles';
    const method = editingRole.value ? 'put' : 'post';

    await axios[method](url, roleForm);

    showNotification(editingRole.value ? 'Role updated successfully!' : 'Role created successfully!');
    closeRoleDialog();
    fetchRoles(rolesMeta.current_page);
  } catch (error: any) {
    console.error('Error saving role:', error);
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat();
      showNotification('Validation errors: ' + errors.join(', '), 'error');
    } else {
      showNotification('Error saving role. Please try again.', 'error');
    }
  } finally {
    submittingRole.value = false;
  }
};

const savePermission = async () => {
  try {
    submittingPermission.value = true;

    const url = editingPermission.value ? `/api/permissions/${editingPermission.value.id}` : '/api/permissions';
    const method = editingPermission.value ? 'put' : 'post';

    await axios[method](url, permissionForm);

    showNotification(editingPermission.value ? 'Permission updated successfully!' : 'Permission created successfully!');
    closePermissionDialog();
    fetchPermissions(permissionsMeta.current_page);
  } catch (error: any) {
    console.error('Error saving permission:', error);
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat();
      showNotification('Validation errors: ' + errors.join(', '), 'error');
    } else {
      showNotification('Error saving permission. Please try again.', 'error');
    }
  } finally {
    submittingPermission.value = false;
  }
};

const toggleRoleStatus = async (role: Role) => {
  try {
    await axios.patch(`/api/roles/${role.id}/toggle-status`);
    showNotification('Role status updated successfully!');
    fetchRoles(rolesMeta.current_page);
  } catch (error: any) {
    console.error('Error toggling role status:', error);
    showNotification(error.response?.data?.message || 'Error updating role status', 'error');
  }
};

const togglePermissionStatus = async (permission: Permission) => {
  try {
    await axios.patch(`/api/permissions/${permission.id}/toggle-status`);
    showNotification('Permission status updated successfully!');
    fetchPermissions(permissionsMeta.current_page);
  } catch (error: any) {
    console.error('Error toggling permission status:', error);
    showNotification(error.response?.data?.message || 'Error updating permission status', 'error');
  }
};

const deleteRole = (role: Role) => {
  if (confirm(`Are you sure you want to delete the role "${role.display_name}"?`)) {
    performDeleteRole(role);
  }
};

const deletePermission = (permission: Permission) => {
  if (confirm(`Are you sure you want to delete the permission "${permission.display_name}"?`)) {
    performDeletePermission(permission);
  }
};

const performDeleteRole = async (role: Role) => {
  try {
    await axios.delete(`/api/roles/${role.id}`);
    showNotification('Role deleted successfully!');
    fetchRoles(rolesMeta.current_page);
  } catch (error: any) {
    console.error('Error deleting role:', error);
    showNotification(error.response?.data?.message || 'Error deleting role', 'error');
  }
};

const performDeletePermission = async (permission: Permission) => {
  try {
    await axios.delete(`/api/permissions/${permission.id}`);
    showNotification('Permission deleted successfully!');
    fetchPermissions(permissionsMeta.current_page);
  } catch (error: any) {
    console.error('Error deleting permission:', error);
    showNotification(error.response?.data?.message || 'Error deleting permission', 'error');
  }
};

const closeRoleDialog = () => {
  showRoleDialog.value = false;
  editingRole.value = null;
  resetRoleForm();
};

const closePermissionDialog = () => {
  showPermissionDialog.value = false;
  editingPermission.value = null;
  resetPermissionForm();
};

const resetRoleForm = () => {
  roleForm.name = '';
  roleForm.display_name = '';
  roleForm.description = '';
  roleForm.level = 0;
  roleForm.is_active = true;
  roleForm.permissions = [];
};

const resetPermissionForm = () => {
  permissionForm.name = '';
  permissionForm.display_name = '';
  permissionForm.description = '';
  permissionForm.category = '';
  permissionForm.is_active = true;
};

const clearRoleFilters = () => {
  roleFilters.search = '';
  roleFilters.status = '';
  fetchRoles(1);
};

// Lifecycle
onMounted(() => {
  fetchRoles();
  fetchPermissions();
});
</script>
