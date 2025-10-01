<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const navigateTo = (routeName: string) => {
  if (authStore.isAuthenticated) {
    router.push({ name: routeName });
  } else {
    router.push({ name: 'login' });
  }
};

const goToLogin = () => {
  router.push({ name: 'login' });
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              Executive Dashboard
            </h1>
          </div>
          <nav class="flex space-x-3">
            <button
              v-if="authStore.isAuthenticated"
              @click="navigateTo('dashboard')"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium shadow-lg hover:shadow-xl transition-all duration-200"
            >
              Dashboard
            </button>
            <button
              v-if="authStore.isAuthenticated"
              @click="navigateTo('projects.index')"
              class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium shadow-sm hover:shadow-md transition-all duration-200"
            >
              Projects
            </button>
            <button
              v-if="!authStore.isAuthenticated"
              @click="goToLogin"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium shadow-lg hover:shadow-xl transition-all duration-200"
            >
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
              </svg>
              Login
            </button>
          </nav>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-16">
          <h2 class="text-5xl font-bold text-gray-900 mb-6">
            Project Monitoring & 
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              Executive Dashboard
            </span>
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
            Comprehensive project tracking and monitoring system designed for executive oversight, 
            real-time progress monitoring, and data-driven decision making.
          </p>
          <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
            <button
              v-if="authStore.isAuthenticated"
              @click="navigateTo('dashboard')"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl text-lg font-semibold shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-200"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              View Dashboard
            </button>
            <button
              v-if="authStore.isAuthenticated"
              @click="navigateTo('projects.index')"
              class="bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-300 hover:border-gray-400 px-8 py-4 rounded-xl text-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v0a2 2 0 002 2h14a2 2 0 002-2v0a2 2 0 00-2-2"></path>
              </svg>
              Browse Projects
            </button>
            <button
              v-if="!authStore.isAuthenticated"
              @click="goToLogin"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl text-lg font-semibold shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-200"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
              </svg>
              Login to Access Dashboard
            </button>
            <button
              v-if="!authStore.isAuthenticated"
              @click="goToLogin"
              class="bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-300 hover:border-gray-400 px-8 py-4 rounded-xl text-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v0a2 2 0 002 2h14a2 2 0 002-2v0a2 2 0 00-2-2"></path>
              </svg>
              View Projects
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Project Showcase Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h3 class="text-3xl font-bold text-gray-900 mb-4">Featured Infrastructure Projects</h3>
          <p class="text-lg text-gray-600">Real-time monitoring of major development initiatives</p>
        </div>
        
        <!-- Project Image Gallery -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
          <!-- Main Project Image -->
          <div class="relative group">
            <div class="aspect-w-16 aspect-h-10 rounded-2xl overflow-hidden shadow-2xl">
              <img
                src="/images/roads-constructions.jpeg"
                alt="Construction of Roads Network in Agaie Town"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
              <div class="absolute bottom-6 left-6 text-white">
                <h4 class="text-2xl font-bold mb-2">Road Network Construction</h4>
                <p class="text-lg opacity-90">Agaie Town Infrastructure Development</p>
                <div class="mt-3 flex items-center space-x-4">
                  <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">In Progress</span>
                  <span class="text-sm opacity-75">75% Complete</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Project Details -->
          <div class="space-y-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8">
              <h4 class="text-2xl font-bold text-gray-900 mb-4">Project Overview</h4>
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">Project Status</span>
                  <span class="font-semibold text-green-600">Active</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">Completion</span>
                  <span class="font-semibold">75%</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">Budget Utilization</span>
                  <span class="font-semibold">₦2.5B / ₦3.2B</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">Timeline</span>
                  <span class="font-semibold">18 months</span>
                </div>
              </div>
              
              <!-- Progress Bar -->
              <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                  <span>Progress</span>
                  <span>75%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-3 rounded-full" style="width: 75%"></div>
                </div>
              </div>
            </div>
            
            <!-- Key Features -->
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <h5 class="font-semibold text-gray-900 mb-2">Real-time Tracking</h5>
                <p class="text-sm text-gray-600">Live progress monitoring and updates</p>
              </div>
              
              <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
                <h5 class="font-semibold text-gray-900 mb-2">Budget Control</h5>
                <p class="text-sm text-gray-600">Financial oversight and cost management</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Project Gallery -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="relative group rounded-xl overflow-hidden shadow-lg">
            <img
              src="/images/road-network.jpg"
              alt="Road Network Development"
              class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute bottom-4 left-4 text-white">
              <h5 class="font-bold">Road Network</h5>
              <p class="text-sm opacity-90">Infrastructure Development</p>
            </div>
          </div>

          <div class="relative group rounded-xl overflow-hidden shadow-lg">
            <img
              src="/images/bago-enhances-agriculture.jpeg"
              alt="Agricultural Enhancement Project"
              class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute bottom-4 left-4 text-white">
              <h5 class="font-bold">Agriculture Enhancement</h5>
              <p class="text-sm opacity-90">Bago Development Initiative</p>
            </div>
          </div>

          <div class="relative group rounded-xl overflow-hidden shadow-lg">
            <img
              src="/images/niger-state-setup-claim-committee-for-bida-road.jpg"
              alt="Bida Road Project"
              class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute bottom-4 left-4 text-white">
              <h5 class="font-bold">Bida Road Project</h5>
              <p class="text-sm opacity-90">Niger State Initiative</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h3 class="text-3xl font-bold text-gray-900 mb-4">System Capabilities</h3>
          <p class="text-lg text-gray-600">Comprehensive tools for project management and executive oversight</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-4">Advanced Analytics</h4>
            <p class="text-gray-600">Comprehensive charts and reports for data-driven insights and decision making.</p>
          </div>

          <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-4">Progress Monitoring</h4>
            <p class="text-gray-600">Real-time tracking of project milestones, deliverables, and performance metrics.</p>
          </div>

          <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-4">Team Collaboration</h4>
            <p class="text-gray-600">Seamless communication and file sharing between project stakeholders.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-indigo-600">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h3 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h3>
        <p class="text-xl text-blue-100 mb-8">
          Access comprehensive project insights and executive-level reporting tools.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <button
            v-if="authStore.isAuthenticated"
            @click="navigateTo('dashboard')"
            class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl text-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200"
          >
            Access Dashboard
          </button>
          <button
            v-if="authStore.isAuthenticated"
            @click="navigateTo('projects.index')"
            class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-200"
          >
            View All Projects
          </button>
          <button
            v-if="!authStore.isAuthenticated"
            @click="goToLogin"
            class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl text-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200"
          >
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Login to Get Started
          </button>
        </div>
      </div>
    </section>
  </div>
</template>
