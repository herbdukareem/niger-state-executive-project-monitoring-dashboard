<template>
  <div class="department-performance">
    <div 
      v-for="(dept, index) in departments" 
      :key="dept.name"
      class="department-item"
      :style="{ '--delay': (index * 100) + 'ms' }"
    >
      <div class="department-info">
        <div class="department-header">
          <div class="department-indicator" :style="{ backgroundColor: dept.color }"></div>
          <span class="department-name">{{ dept.name }}</span>
        </div>
        <div class="department-meta">
          <span class="project-count">{{ dept.projectCount }} projects</span>
        </div>
      </div>
      
      <div class="department-performance-value">
        <span class="performance-percentage">{{ dept.performance }}%</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Department {
  name: string;
  performance: number;
  projectCount: number;
  color: string;
}

interface Props {
  departments: Department[];
}

defineProps<Props>();
</script>

<style scoped>
.department-performance {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.department-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #F3F4F6;
  animation: fadeInLeft 0.6s ease-out var(--delay) both;
}

.department-item:last-child {
  border-bottom: none;
}

.department-info {
  flex: 1;
}

.department-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.department-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex-shrink: 0;
}

.department-name {
  font-weight: 500;
  color: #374151;
  font-size: 14px;
}

.department-meta {
  margin-left: 20px;
}

.project-count {
  font-size: 12px;
  color: #6B7280;
}

.department-performance-value {
  text-align: right;
}

.performance-percentage {
  font-size: 16px;
  font-weight: 600;
  color: #1F2937;
}

@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
