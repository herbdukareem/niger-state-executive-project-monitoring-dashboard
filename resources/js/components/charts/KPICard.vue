<template>
  <div class="kpi-card" :class="[`kpi-${variant}`, { 'kpi-animated': animate }]">
    <div class="kpi-content">
      <div class="kpi-value" :style="{ color: valueColor }">
        {{ formattedValue }}
      </div>
      <div class="kpi-label">
        {{ label }}
      </div>
      <div v-if="subtitle" class="kpi-subtitle">
        {{ subtitle }}
      </div>
    </div>
    
    <div v-if="icon" class="kpi-icon" :style="{ color: iconColor }">
      <v-icon :icon="icon" size="24" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';

interface Props {
  value: number | string;
  label: string;
  subtitle?: string;
  icon?: string;
  variant?: 'primary' | 'success' | 'warning' | 'error' | 'info';
  format?: 'number' | 'currency' | 'percentage' | 'text';
  animate?: boolean;
  duration?: number;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  format: 'number',
  animate: true,
  duration: 1000
});

const animatedValue = ref(0);

const formattedValue = computed(() => {
  const currentValue = props.animate && typeof props.value === 'number' 
    ? animatedValue.value 
    : props.value;

  if (props.format === 'currency') {
    const numValue = typeof currentValue === 'number' ? currentValue : 0;
    if (numValue >= 1000000000) {
      return '₦' + (numValue / 1000000000).toFixed(1) + 'B';
    } else if (numValue >= 1000000) {
      return '₦' + (numValue / 1000000).toFixed(1) + 'M';
    } else if (numValue >= 1000) {
      return '₦' + (numValue / 1000).toFixed(0) + 'K';
    }
    return '₦' + numValue.toLocaleString();
  } else if (props.format === 'percentage') {
    return Math.round(typeof currentValue === 'number' ? currentValue : 0) + '%';
  } else if (props.format === 'number') {
    return Math.round(typeof currentValue === 'number' ? currentValue : 0).toLocaleString();
  }
  
  return currentValue.toString();
});

const valueColor = computed(() => {
  const colors = {
    primary: '#4F46E5',
    success: '#10B981',
    warning: '#F59E0B',
    error: '#EF4444',
    info: '#3B82F6'
  };
  return colors[props.variant];
});

const iconColor = computed(() => {
  const colors = {
    primary: '#4F46E5',
    success: '#10B981',
    warning: '#F59E0B',
    error: '#EF4444',
    info: '#3B82F6'
  };
  return colors[props.variant];
});

const animateValue = () => {
  if (!props.animate || typeof props.value !== 'number') {
    animatedValue.value = typeof props.value === 'number' ? props.value : 0;
    return;
  }
  
  const startTime = Date.now();
  const startValue = 0;
  const endValue = props.value;
  
  const animate = () => {
    const elapsed = Date.now() - startTime;
    const progress = Math.min(elapsed / props.duration, 1);
    
    // Easing function (ease-out)
    const easeOut = 1 - Math.pow(1 - progress, 3);
    animatedValue.value = startValue + (endValue - startValue) * easeOut;
    
    if (progress < 1) {
      requestAnimationFrame(animate);
    }
  };
  
  requestAnimationFrame(animate);
};

onMounted(() => {
  setTimeout(animateValue, 100);
});
</script>

<style scoped>
.kpi-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #F3F4F6;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.kpi-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.kpi-animated {
  animation: slideInUp 0.6s ease-out;
}

.kpi-content {
  flex: 1;
}

.kpi-value {
  font-size: 28px;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 4px;
}

.kpi-label {
  font-size: 14px;
  font-weight: 500;
  color: #6B7280;
  margin-bottom: 2px;
}

.kpi-subtitle {
  font-size: 12px;
  color: #9CA3AF;
}

.kpi-icon {
  flex-shrink: 0;
  margin-left: 16px;
  opacity: 0.8;
}

/* Variant styles */
.kpi-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: #4F46E5;
}

.kpi-success::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: #10B981;
}

.kpi-warning::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: #F59E0B;
}

.kpi-error::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: #EF4444;
}

.kpi-info::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: #3B82F6;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .kpi-card {
    padding: 16px;
  }
  
  .kpi-value {
    font-size: 24px;
  }
  
  .kpi-icon {
    margin-left: 12px;
  }
}
</style>
