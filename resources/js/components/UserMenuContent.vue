<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { useRouter } from 'vue-router';
import { LogOut, Settings } from 'lucide-vue-next';

interface Props {
    user: User;
}

defineProps<Props>();

const router = useRouter();

const handleSettings = () => {
    // Navigate to settings page or implement settings logic
    console.log('Settings clicked');
};

const handleLogout = () => {
    // Implement logout logic
    console.log('Logout clicked');
    // For now, just redirect to home
    router.push({ name: 'home' });
};
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem @click="handleSettings">
            <Settings class="mr-2 h-4 w-4" />
            Settings
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem @click="handleLogout" data-test="logout-button">
        <LogOut class="mr-2 h-4 w-4" />
        Log out
    </DropdownMenuItem>
</template>
