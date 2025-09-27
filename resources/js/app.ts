import '../css/app.css';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { initializeTheme } from './composables/useAppearance';

// Vuetify
import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css';

// Animate.css
import 'animate.css';

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#4F46E5',
          secondary: '#6B7280',
          accent: '#10B981',
          error: '#EF4444',
          warning: '#F59E0B',
          info: '#3B82F6',
          success: '#10B981',
        },
      },
    },
  },
});

const app = createApp(App);

app.use(router);
app.use(vuetify);

app.mount('#app');

// This will set light / dark mode on page load...
initializeTheme();
