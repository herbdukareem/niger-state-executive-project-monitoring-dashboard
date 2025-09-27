<script setup lang="ts">
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { useRouter } from 'vue-router';

interface BreadcrumbItemType {
    title: string;
    href?: string;
}

defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();

const router = useRouter();

const navigateToHref = (href: string) => {
  if (href.startsWith('http')) {
    window.open(href, '_blank');
  } else {
    router.push(href);
  }
};
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList>
            <template v-for="(item, index) in breadcrumbs" :key="index">
                <BreadcrumbItem>
                    <template v-if="index === breadcrumbs.length - 1">
                        <BreadcrumbPage>{{ item.title }}</BreadcrumbPage>
                    </template>
                    <template v-else>
                        <BreadcrumbLink @click="navigateToHref(item.href ?? '#')">
                            {{ item.title }}
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index !== breadcrumbs.length - 1" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
