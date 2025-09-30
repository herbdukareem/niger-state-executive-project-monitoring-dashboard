<template>
  <div class="login-page">
    <v-container fluid class="fill-height">
      <v-row justify="center" align="center" class="fill-height">
        <v-col cols="12" sm="8" md="6" lg="4">
          <v-card class="elevation-8 pa-6">
            <!-- Header -->
            <div class="text-center mb-6">
              <v-img
                src="/images/niger-state-logo.png"
                alt="Niger State Logo"
                max-width="80"
                class="mx-auto mb-4"
              ></v-img>
              <h1 class="text-h4 font-weight-bold text-primary mb-2">
                Executive Dashboard
              </h1>
              <p class="text-subtitle-1 text-grey-darken-1">
                Niger State Project Monitoring System
              </p>
            </div>

            <!-- Login Form -->
            <v-form @submit.prevent="handleLogin" ref="loginForm">
              <v-text-field
                v-model="form.email"
                label="Email Address"
                type="email"
                variant="outlined"
                prepend-inner-icon="mdi-email"
                :rules="emailRules"
                :error-messages="errors.email"
                required
                class="mb-3"
              ></v-text-field>

              <v-text-field
                v-model="form.password"
                label="Password"
                :type="showPassword ? 'text' : 'password'"
                variant="outlined"
                prepend-inner-icon="mdi-lock"
                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showPassword = !showPassword"
                :rules="passwordRules"
                :error-messages="errors.password"
                required
                class="mb-4"
              ></v-text-field>

              <v-row class="mb-4">
                <v-col cols="6">
                  <v-checkbox
                    v-model="form.remember"
                    label="Remember me"
                    density="compact"
                  ></v-checkbox>
                </v-col>
                <v-col cols="6" class="text-right">
                  <v-btn
                    variant="text"
                    color="primary"
                    size="small"
                    @click="showForgotPassword = true"
                  >
                    Forgot Password?
                  </v-btn>
                </v-col>
              </v-row>

              <v-btn
                type="submit"
                color="primary"
                size="large"
                block
                :loading="authStore.loading"
                class="mb-4"
              >
                Sign In
              </v-btn>

              <!-- Error Message -->
              <v-alert
                v-if="errorMessage"
                type="error"
                variant="tonal"
                class="mb-4"
              >
                {{ errorMessage }}
              </v-alert>

              <!-- Demo Credentials -->
              <v-expansion-panels variant="accordion" class="mt-4">
                <v-expansion-panel>
                  <v-expansion-panel-title>
                    <v-icon class="mr-2">mdi-account-key</v-icon>
                    Demo Credentials
                  </v-expansion-panel-title>
                  <v-expansion-panel-text>
                    <div class="demo-credentials">
                      <div class="mb-3">
                        <strong>Governor:</strong><br>
                        Email: governor@nigerstate.gov.ng<br>
                        Password: governor2024
                        <v-btn
                          size="small"
                          variant="outlined"
                          color="primary"
                          class="ml-2"
                          @click="fillCredentials('governor@nigerstate.gov.ng', 'governor2024')"
                        >
                          Use
                        </v-btn>
                      </div>
                      <div class="mb-3">
                        <strong>Super Admin:</strong><br>
                        Email: admin@nigerstate.gov.ng<br>
                        Password: admin2024
                        <v-btn
                          size="small"
                          variant="outlined"
                          color="primary"
                          class="ml-2"
                          @click="fillCredentials('admin@nigerstate.gov.ng', 'admin2024')"
                        >
                          Use
                        </v-btn>
                      </div>
                      <div class="mb-3">
                        <strong>Admin:</strong><br>
                        Email: coordinator@nigerstate.gov.ng<br>
                        Password: admin2024
                        <v-btn
                          size="small"
                          variant="outlined"
                          color="primary"
                          class="ml-2"
                          @click="fillCredentials('coordinator@nigerstate.gov.ng', 'admin2024')"
                        >
                          Use
                        </v-btn>
                      </div>
                    </div>
                  </v-expansion-panel-text>
                </v-expansion-panel>
              </v-expansion-panels>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Forgot Password Dialog -->
    <v-dialog v-model="showForgotPassword" max-width="400">
      <v-card>
        <v-card-title>Reset Password</v-card-title>
        <v-card-text>
          <p class="mb-4">Enter your email address and we'll send you a link to reset your password.</p>
          <v-text-field
            v-model="forgotEmail"
            label="Email Address"
            type="email"
            variant="outlined"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="showForgotPassword = false">Cancel</v-btn>
          <v-btn color="primary" @click="handleForgotPassword">Send Reset Link</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const loginForm = ref();
const showPassword = ref(false);
const showForgotPassword = ref(false);
const forgotEmail = ref('');
const errorMessage = ref('');

const form = reactive({
  email: '',
  password: '',
  remember: false
});

const errors = reactive({
  email: [],
  password: []
});

const emailRules = [
  (v: string) => !!v || 'Email is required',
  (v: string) => /.+@.+\..+/.test(v) || 'Email must be valid'
];

const passwordRules = [
  (v: string) => !!v || 'Password is required',
  (v: string) => v.length >= 6 || 'Password must be at least 6 characters'
];

const fillCredentials = (email: string, password: string) => {
  form.email = email;
  form.password = password;
};

const handleLogin = async () => {
  const { valid } = await loginForm.value.validate();
  if (!valid) return;

  errorMessage.value = '';
  errors.email = [];
  errors.password = [];

  const result = await authStore.login(form.email, form.password);
  
  if (result.success) {
    router.push({ name: 'dashboard' });
  } else {
    errorMessage.value = result.message || 'Login failed';
  }
};

const handleForgotPassword = () => {
  // TODO: Implement forgot password functionality
  showForgotPassword.value = false;
  // For now, just show a message
  alert('Password reset functionality will be implemented soon.');
};
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  background: white;
}

.demo-credentials {
  font-size: 0.875rem;
}

.demo-credentials strong {
  color: #1976d2;
}
</style>
