<template>
  <AppLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
          <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
              User Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
              Manage system users, roles, and permissions
            </p>
          </div>
          <div class="mt-4 flex md:ml-4 md:mt-0">
            <button
              @click="openCreateModal"
              class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add User
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Search</label>
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Search users..."
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  @input="debouncedSearch"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select
                  v-model="filters.role"
                  @change="fetchUsers"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="">All Roles</option>
                  <option v-for="role in roles" :key="role.id" :value="role.name">
                    {{ role.display_name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select
                  v-model="filters.status"
                  @change="fetchUsers"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
              <div class="flex items-end">
                <button
                  @click="clearFilters"
                  class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Users ({{ meta.total }})
            </h3>
          </div>
          
          <!-- Loading State -->
          <div v-if="loading" class="px-4 py-8 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-2 text-sm text-gray-500">Loading users...</p>
          </div>

          <!-- Users List -->
          <div v-else-if="users.length > 0" class="divide-y divide-gray-200">
            <div
              v-for="user in users"
              :key="user.id"
              class="px-4 py-4 hover:bg-gray-50"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700">
                        {{ user.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="flex items-center">
                      <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                      <span
                        v-if="user.role"
                        :class="`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${user.role.color}-100 text-${user.role.color}-800`"
                      >
                        <svg :class="`w-3 h-3 mr-1`" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                        </svg>
                        {{ user.role.display_name }}
                      </span>
                      <span
                        :class="`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                          user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }`"
                      >
                        {{ user.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                    <p class="text-sm text-gray-500">{{ user.organization }} • {{ user.position }}</p>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button
                    @click="editUser(user)"
                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                  >
                    Edit
                  </button>
                  <button
                    @click="toggleUserStatus(user)"
                    :class="`text-sm font-medium ${
                      user.is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'
                    }`"
                  >
                    {{ user.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button
                    @click="deleteUser(user)"
                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="px-4 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
          </div>

          <!-- Pagination -->
          <div v-if="meta.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <button
                  @click="changePage(meta.current_page - 1)"
                  :disabled="meta.current_page <= 1"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                >
                  Previous
                </button>
                <button
                  @click="changePage(meta.current_page + 1)"
                  :disabled="meta.current_page >= meta.last_page"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                >
                  Next
                </button>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing {{ (meta.current_page - 1) * meta.per_page + 1 }} to {{ Math.min(meta.current_page * meta.per_page, meta.total) }} of {{ meta.total }} results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <button
                      @click="changePage(meta.current_page - 1)"
                      :disabled="meta.current_page <= 1"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                    >
                      Previous
                    </button>
                    <button
                      @click="changePage(meta.current_page + 1)"
                      :disabled="meta.current_page >= meta.last_page"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                    >
                      Next
                    </button>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div v-if="showCreateDialog" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="submitForm">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    {{ editingUser ? 'Edit User' : 'Create New User' }}
                  </h3>

                  <!-- Form Fields -->
                  <div class="space-y-4">
                    <!-- Name -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                      <input
                        v-model="form.name"
                        type="text"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter full name"
                      />
                    </div>

                    <!-- Email -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Email Address *</label>
                      <input
                        v-model="form.email"
                        type="email"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter email address"
                      />
                    </div>

                    <!-- Password -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        Password {{ editingUser ? '' : '*' }}
                      </label>
                      <input
                        v-model="form.password"
                        type="password"
                        :required="!editingUser"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter password"
                      />
                      <p v-if="editingUser" class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        Confirm Password {{ editingUser ? '' : '*' }}
                      </label>
                      <input
                        v-model="form.password_confirmation"
                        type="password"
                        :required="!editingUser && form.password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Confirm password"
                      />
                    </div>

                    <!-- Role -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Role *</label>
                      <select
                        v-model="form.role_id"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      >
                        <option value="">Select a role</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                          {{ role.display_name }}
                        </option>
                      </select>
                    </div>

                    <!-- Organization -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Organization</label>
                      <input
                        v-model="form.organization"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter organization"
                      />
                    </div>

                    <!-- Position -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Position</label>
                      <input
                        v-model="form.position"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter position/title"
                      />
                    </div>

                    <!-- Phone -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                      <input
                        v-model="form.phone"
                        type="tel"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter phone number"
                      />
                    </div>

                    <!-- Address -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Address</label>
                      <textarea
                        v-model="form.address"
                        rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Enter address"
                      ></textarea>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                      <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <label class="ml-2 block text-sm text-gray-900">
                        Active User
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Actions -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="submitting"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <svg v-if="submitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ submitting ? 'Saving...' : (editingUser ? 'Update User' : 'Create User') }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { debounce } from 'lodash';
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';

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
  role_id: '',
  organization: '',
  position: '',
  phone: '',
  address: '',
  is_active: true,
});

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
  form.role_id = '';
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
  form.role_id = user.role?.id.toString() || '';
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

      alert('User updated successfully!');
    } else {
      // Create new user
      const response = await axios.post('/api/users', form);
      const newUser = response.data.data;

      // Add new user to the list
      users.value.unshift(newUser);
      meta.total++;

      alert('User created successfully!');
    }

    closeModal();
  } catch (error: any) {
    console.error('Error saving user:', error);
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat();
      alert('Validation errors:\n' + errors.join('\n'));
    } else {
      alert('Error saving user. Please try again.');
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
    alert('Error updating user status');
  }
};

const deleteUser = async (user: User) => {
  if (confirm(`Are you sure you want to delete ${user.name}?`)) {
    try {
      await axios.delete(`/api/users/${user.id}`);
      fetchUsers(meta.current_page);
    } catch (error) {
      console.error('Error deleting user:', error);
      alert('Error deleting user');
    }
  }
};

onMounted(() => {
  fetchRoles();
  fetchUsers();
});
</script>
