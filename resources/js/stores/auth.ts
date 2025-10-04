import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import axios from 'axios';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  is_active: boolean;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(localStorage.getItem('auth_token'));
  const loading = ref(false);

  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const isAdmin = computed(() => {
    const role = user.value?.role;
    return role === 'admin' || role === 'super_admin' || role === 'governor';
  });
  const isSuperAdmin = computed(() => user.value?.role === 'super_admin');
  const isGovernor = computed(() => user.value?.role === 'governor');

  // Set up axios interceptor for authentication
  const setupAxiosInterceptors = () => {
    // Request interceptor to add token
    axios.interceptors.request.use(
      (config) => {
        if (token.value) {
          config.headers.Authorization = `Bearer ${token.value}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor to handle 401 errors
    axios.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          logout();
        }
        return Promise.reject(error);
      }
    );
  };

  const login = async (email: string, password: string) => {
    loading.value = true;
    try {
      // Get CSRF token first
      await axios.get('/sanctum/csrf-cookie');

      // Attempt login
      const response = await axios.post('/api/login', {
        email,
        password,
      });

      if (response.data.token) {
        token.value = response.data.token;
        user.value = response.data.user;
        localStorage.setItem('auth_token', response.data.token);
        setupAxiosInterceptors();
        return { success: true };
      } else {
        return {
          success: false,
          message: 'No token received from server'
        };
      }
    } catch (error: any) {
      console.error('Login error:', error);
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed'
      };
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    try {
      if (token.value) {
        await axios.post('/api/logout');
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('auth_token');
      // Redirect to login
      window.location.href = '/login';
    }
  };

  const fetchUser = async () => {
    if (!token.value) return false;

    try {
      const response = await axios.get('/api/auth/user');
      user.value = response.data;
      return true;
    } catch (error) {
      console.error('Fetch user error:', error);
      logout();
      return false;
    }
  };

  const register = async (userData: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
  }) => {
    loading.value = true;
    try {
      await axios.get('/sanctum/csrf-cookie');

      const response = await axios.post('/api/register', userData);
      
      if (response.data.token) {
        token.value = response.data.token;
        user.value = response.data.user;
        localStorage.setItem('auth_token', response.data.token);
        setupAxiosInterceptors();
        return { success: true };
      }
    } catch (error: any) {
      console.error('Registration error:', error);
      return {
        success: false,
        message: error.response?.data?.message || 'Registration failed',
        errors: error.response?.data?.errors || {}
      };
    } finally {
      loading.value = false;
    }
  };

  const initializeAuth = async () => {
    if (token.value) {
      setupAxiosInterceptors();
      await fetchUser();
    }
  };

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    isSuperAdmin,
    isGovernor,
    login,
    logout,
    register,
    fetchUser,
    initializeAuth,
    setupAxiosInterceptors
  };
});
