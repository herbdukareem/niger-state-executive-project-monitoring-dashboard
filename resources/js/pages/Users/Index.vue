<template>
  <AppLayout>
    <v-container fluid class="pa-6">
      <!-- Header -->
      <div class="d-flex align-center justify-space-between mb-6">
        <div>
          <h1 class="text-h4 font-weight-bold text-grey-darken-3 mb-2">
            User Management
          </h1>
          <p class="text-body-2 text-grey-darken-1">
            Manage system users, roles, and permissions
          </p>
        </div>
        <v-btn
          @click="openCreateModal"
          color="primary"
          prepend-icon="mdi-plus"
          variant="elevated"
        >
          Add User
        </v-btn>
      </div>

      <!-- Filters -->
      <v-card class="mb-6" elevation="2">
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="filters.search"
                label="Search users..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                clearable
                @input="debouncedSearch"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-select
                v-model="filters.role"
                :items="roleOptions"
                label="Role"
                variant="outlined"
                density="compact"
                clearable
                @update:model-value="() => fetchUsers(1)"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-select
                v-model="filters.status"
                :items="statusOptions"
                label="Status"
                variant="outlined"
                density="compact"
                clearable
                @update:model-value="() => fetchUsers(1)"
              />
            </v-col>
            <v-col cols="12" sm="6" md="3" class="d-flex align-end">
              <v-btn
                @click="clearFilters"
                variant="outlined"
                color="grey"
                block
                prepend-icon="mdi-filter-remove"
              >
                Clear Filters
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Users Table -->
      <v-card elevation="2">
        <v-card-title class="d-flex align-center">
          <v-icon class="me-2">mdi-account-group</v-icon>
          Users ({{ meta.total }})
        </v-card-title>

        <!-- Loading State -->
        <div v-if="loading" class="text-center pa-8">
          <v-progress-circular
            indeterminate
            color="primary"
            size="64"
          />
          <p class="mt-4 text-body-2 text-grey-darken-1">Loading users...</p>
        </div>

        <!-- Users List -->
        <v-list v-else-if="users.length > 0" lines="three">
          <v-list-item
            v-for="user in users"
            :key="user.id"
            class="border-b"
          >
            <template #prepend>
              <v-avatar color="grey-lighten-2" size="40">
                <span class="text-h6">{{ user.name.charAt(0).toUpperCase() }}</span>
              </v-avatar>
            </template>

            <v-list-item-title class="d-flex align-center">
              <span class="font-weight-medium">{{ user.name }}</span>
              <v-chip
                v-if="user.role"
                :color="user.role.color || 'primary'"
                size="small"
                class="ml-2"
                variant="tonal"
              >
                <v-icon start size="small">{{ user.role.icon || 'mdi-account' }}</v-icon>
                {{ user.role.display_name }}
              </v-chip>
              <v-chip
                :color="user.is_active ? 'success' : 'error'"
                size="small"
                class="ml-2"
                variant="tonal"
              >
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </v-chip>
            </v-list-item-title>

            <v-list-item-subtitle>
              <div>{{ user.email }}</div>
              <div v-if="user.organization || user.position">
                {{ user.organization }}{{ user.organization && user.position ? ' • ' : '' }}{{ user.position }}
              </div>
            </v-list-item-subtitle>

            <template #append>
              <div class="d-flex align-center">
                <v-btn
                  @click="editUser(user)"
                  icon="mdi-pencil"
                  variant="text"
                  color="primary"
                  size="small"
                />
                <v-btn
                  @click="toggleUserStatus(user)"
                  :icon="user.is_active ? 'mdi-account-off' : 'mdi-account-check'"
                  variant="text"
                  :color="user.is_active ? 'error' : 'success'"
                  size="small"
                />
                <v-btn
                  @click="deleteUser(user)"
                  icon="mdi-delete"
                  variant="text"
                  color="error"
                  size="small"
                />
              </div>
            </template>
          </v-list-item>
        </v-list>

        <!-- Empty State -->
        <div v-else class="text-center pa-8">
          <v-icon size="64" color="grey-lighten-1">mdi-account-group-outline</v-icon>
          <h3 class="text-h6 mt-4 text-grey-darken-2">No users found</h3>
          <p class="text-body-2 text-grey-darken-1 mt-2">Get started by creating a new user.</p>
        </div>

        <!-- Pagination -->
        <v-card-actions v-if="meta.last_page > 1" class="justify-space-between">
          <div class="text-body-2 text-grey-darken-1">
            Showing {{ (meta.current_page - 1) * meta.per_page + 1 }} to {{ Math.min(meta.current_page * meta.per_page, meta.total) }} of {{ meta.total }} results
          </div>
          <v-pagination
            v-model="meta.current_page"
            :length="meta.last_page"
            :total-visible="7"
            @update:model-value="changePage"
          />
        </v-card-actions>
      </v-card>
    </v-container>

    <!-- Create/Edit User Modal -->
    <v-dialog
      v-model="showCreateDialog"
      max-width="600px"
      persistent
    >
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon class="me-2">{{ editingUser ? 'mdi-account-edit' : 'mdi-account-plus' }}</v-icon>
          {{ editingUser ? 'Edit User' : 'Create New User' }}
        </v-card-title>

        <v-form @submit.prevent="submitForm">
          <v-card-text>
            <v-container>
              <v-row>
                <!-- Name -->
                <v-col cols="12">
                  <v-text-field
                    v-model="form.name"
                    label="Full Name"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Name is required']"
                  />
                </v-col>

                <!-- Email -->
                <v-col cols="12">
                  <v-text-field
                    v-model="form.email"
                    label="Email Address"
                    type="email"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Email must be valid']"
                  />
                </v-col>

                <!-- Password -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.password"
                    :label="`Password${editingUser ? '' : ' *'}`"
                    type="password"
                    variant="outlined"
                    :required="!editingUser"
                    :rules="!editingUser ? [v => !!v || 'Password is required'] : []"
                    :hint="editingUser ? 'Leave blank to keep current password' : ''"
                    persistent-hint
                  />
                </v-col>

                <!-- Confirm Password -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.password_confirmation"
                    :label="`Confirm Password${editingUser ? '' : ' *'}`"
                    type="password"
                    variant="outlined"
                    :required="!editingUser && !!form.password"
                    :rules="passwordConfirmationRules"
                  />
                </v-col>

                <!-- Role -->
                <v-col cols="12">
                  <v-select
                    v-model="form.role_id"
                    :items="roleSelectOptions"
                    label="Role"
                    variant="outlined"
                    required
                    :rules="[v => !!v || 'Role is required']"
                  />
                </v-col>

                <!-- Organization -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.organization"
                    label="Organization"
                    variant="outlined"
                  />
                </v-col>

                <!-- Position -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.position"
                    label="Position/Title"
                    variant="outlined"
                  />
                </v-col>

                <!-- Phone -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.phone"
                    label="Phone Number"
                    type="tel"
                    variant="outlined"
                  />
                </v-col>

                <!-- Address -->
                <v-col cols="12" md="6">
                  <v-textarea
                    v-model="form.address"
                    label="Address"
                    variant="outlined"
                    rows="2"
                    auto-grow
                  />
                </v-col>

                <!-- Active Status -->
                <v-col cols="12">
                  <v-checkbox
                    v-model="form.is_active"
                    label="Active User"
                    color="primary"
                  />
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              @click="closeModal"
              variant="text"
              color="grey"
            >
              Cancel
            </v-btn>
            <v-btn
              type="submit"
              :loading="submitting"
              color="primary"
              variant="elevated"
            >
              {{ editingUser ? 'Update User' : 'Create User' }}
            </v-btn>
          </v-card-actions>
        </v-form>
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
import { ref, reactive, onMounted, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';

// Simple debounce function to avoid lodash dependency
function debounce<T extends (...args: any[]) => any>(func: T, wait: number): (...args: Parameters<T>) => void {
  let timeout: number;
  return (...args: Parameters<T>) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
}

interface User {
  id: number;
  name: string;
  email: string;
  organization: string;
  position: string;
  phone: string;
  address: string;
  is_active: boolean;
  role: {
    id: number;
    name: string;
    display_name: string;
    color: string;
    icon: string;
  } | null;
  projects_count: number;
  project_updates_count: number;
}

interface Role {
  id: number;
  name: string;
  display_name: string;
  description: string;
  color: string;
  icon: string;
}

const users = ref<User[]>([]);
const roles = ref<Role[]>([]);
const loading = ref(true);
const showCreateDialog = ref(false);
const editingUser = ref<User | null>(null);
const submitting = ref(false);

const snackbar = reactive({
  show: false,
  message: '',
  color: 'success'
});

const filters = reactive({
  search: '',
  role: '',
  status: '',
});

const meta = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
});

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: null as number | null,
  organization: '',
  position: '',
  phone: '',
  address: '',
  is_active: true,
});

// Computed properties for Vuetify select options
const roleOptions = computed(() => [
  { title: 'All Roles', value: '' },
  ...roles.value.map(role => ({ title: role.display_name, value: role.name }))
]);

const statusOptions = computed(() => [
  { title: 'All Status', value: '' },
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' }
]);

const roleSelectOptions = computed(() =>
  roles.value.map(role => ({ title: role.display_name, value: role.id }))
);

const passwordConfirmationRules = computed(() => [
  (v: string) => {
    if (!editingUser.value && !form.password) return true; // Not required if editing and no password
    if (!editingUser.value && !v) return 'Password confirmation is required';
    if (v !== form.password) return 'Passwords do not match';
    return true;
  }
]);

const fetchUsers = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: meta.per_page.toString(),
    });

    if (filters.search) params.append('search', filters.search);
    if (filters.role) params.append('role', filters.role);
    if (filters.status) params.append('status', filters.status);

    const response = await axios.get(`/api/users?${params}`);
    users.value = response.data.data;
    Object.assign(meta, response.data.meta);
  } catch (error) {
    console.error('Error fetching users:', error);
    users.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchRoles = async () => {
  try {
    const response = await axios.get('/api/users/roles');
    roles.value = response.data;
  } catch (error) {
    console.error('Error fetching roles:', error);
  }
};

const debouncedSearch = debounce(() => {
  fetchUsers(1);
}, 300);

const showNotification = (message: string, color: string = 'success') => {
  snackbar.message = message;
  snackbar.color = color;
  snackbar.show = true;
};

const clearFilters = () => {
  filters.search = '';
  filters.role = '';
  filters.status = '';
  fetchUsers(1);
};

const changePage = (page: number) => {
  if (page >= 1 && page <= meta.last_page) {
    fetchUsers(page);
  }
};

const resetForm = () => {
  form.name = '';
  form.email = '';
  form.password = '';
  form.password_confirmation = '';
  form.role_id = null;
  form.organization = '';
  form.position = '';
  form.phone = '';
  form.address = '';
  form.is_active = true;
};

const openCreateModal = () => {
  editingUser.value = null;
  resetForm();
  showCreateDialog.value = true;
};

const editUser = (user: User) => {
  editingUser.value = user;
  form.name = user.name;
  form.email = user.email;
  form.password = '';
  form.password_confirmation = '';
  form.role_id = user.role?.id || null;
  form.organization = user.organization || '';
  form.position = user.position || '';
  form.phone = user.phone || '';
  form.address = user.address || '';
  form.is_active = user.is_active;
  showCreateDialog.value = true;
};

const closeModal = () => {
  showCreateDialog.value = false;
  editingUser.value = null;
  resetForm();
};

const submitForm = async () => {
  submitting.value = true;
  try {
    if (editingUser.value) {
      // Update existing user
      const response = await axios.put(`/api/users/${editingUser.value.id}`, form);
      const updatedUser = response.data.data;

      // Update user in the list
      const index = users.value.findIndex(u => u.id === editingUser.value!.id);
      if (index !== -1) {
        users.value[index] = updatedUser;
      }

      showNotification('User updated successfully!');
    } else {
      // Create new user
      const response = await axios.post('/api/users', form);
      const newUser = response.data.data;

      // Add new user to the list
      users.value.unshift(newUser);
      meta.total++;

      showNotification('User created successfully!');
    }

    closeModal();
  } catch (error: any) {
    console.error('Error saving user:', error);
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat();
      showNotification('Validation errors: ' + errors.join(', '), 'error');
    } else {
      showNotification('Error saving user. Please try again.', 'error');
    }
  } finally {
    submitting.value = false;
  }
};

const toggleUserStatus = async (user: User) => {
  try {
    await axios.patch(`/api/users/${user.id}/toggle-status`);
    user.is_active = !user.is_active;
  } catch (error) {
    console.error('Error toggling user status:', error);
    showNotification('Error updating user status', 'error');
  }
};

const deleteUser = async (user: User) => {
  if (confirm(`Are you sure you want to delete ${user.name}?`)) {
    try {
      await axios.delete(`/api/users/${user.id}`);
      showNotification('User deleted successfully!');
      fetchUsers(meta.current_page);
    } catch (error) {
      console.error('Error deleting user:', error);
      showNotification('Error deleting user', 'error');
    }
  }
};

onMounted(() => {
  fetchRoles();
  fetchUsers();
});
</script>
