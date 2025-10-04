<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { useRouter, useRoute } from 'vue-router';

interface Props {
    items: NavItem[];
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Platform'
});

const router = useRouter();
const route = useRoute();

const navigateTo = (routeName: string) => {
  router.push({ name: routeName });
};

const isActive = (href: string) => {
  return route.path === href;
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ props.title }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    :is-active="isActive(item.href)"
                    :tooltip="item.title"
                    @click="navigateTo(item.routeName || 'dashboard')"
                >
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
