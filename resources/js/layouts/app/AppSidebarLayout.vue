<script setup lang="ts">
import { ref } from 'vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppNavbar from '@/components/AppNavbar.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    title?: string;
}

const { breadcrumbs = [], title = '' } = defineProps<Props>();

// Mobile sidebar state
const isMobileSidebarOpen = ref(false);

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppSidebar />

        <!-- Main content -->
        <div class="lg:pl-72">
            <!-- Top navigation bar -->
            <AppNavbar :title="title" @toggle-mobile-sidebar="toggleMobileSidebar" />

            <!-- Breadcrumbs -->
            <div v-if="breadcrumbs && breadcrumbs.length > 0" class="bg-white border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <nav class="flex py-3" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li v-for="(breadcrumb, index) in breadcrumbs" :key="index">
                                <div class="flex items-center">
                                    <svg v-if="index > 0" class="flex-shrink-0 h-4 w-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <a
                                        :href="breadcrumb.href"
                                        :class="[
                                            'text-sm font-medium',
                                            index === breadcrumbs.length - 1
                                                ? 'text-gray-900'
                                                : 'text-gray-500 hover:text-gray-700'
                                        ]"
                                    >
                                        {{ breadcrumb.title }}
                                    </a>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
