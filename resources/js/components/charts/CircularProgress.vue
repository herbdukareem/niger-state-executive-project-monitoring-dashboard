<template>
  <div class="circular-progress-container" :style="{ width: size + 'px', height: size + 'px' }">
    <!-- Error state -->
    <div v-if="!isValidData" class="progress-error">
      <v-icon color="error" size="small">mdi-alert-circle</v-icon>
      <span class="error-text">Invalid data</span>
    </div>

    <!-- Normal state -->
    <template v-else>
      <svg :width="size" :height="size" class="circular-progress-svg">
        <!-- Background circle -->
        <circle
          :cx="center"
          :cy="center"
          :r="radius"
          fill="none"
          :stroke="backgroundColor"
          :stroke-width="strokeWidth"
          class="progress-bg"
        />
        <!-- Progress circle -->
        <circle
          :cx="center"
          :cy="center"
        :r="radius"
        fill="none"
        :stroke="color"
        :stroke-width="strokeWidth"
        :stroke-dasharray="circumference"
        :stroke-dashoffset="strokeDashoffset"
        stroke-linecap="round"
        class="progress-circle"
        :class="{ 'animate-progress': animate }"
        />
      </svg>

      <!-- Center content -->
      <div class="progress-content">
        <div class="progress-value" :style="{ fontSize: valueSize + 'px' }">
          {{ displayValue }}
        </div>
        <div v-if="label" class="progress-label" :style="{ fontSize: labelSize + 'px' }">
          {{ label }}
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';

interface Props {
  value: number;
  max?: number;
  size?: number;
  strokeWidth?: number;
  color?: string;
  backgroundColor?: string;
  label?: string;
  showPercentage?: boolean;
  animate?: boolean;
  duration?: number;
}

const props = withDefaults(defineProps<Props>(), {
  max: 100,
  size: 120,
  strokeWidth: 8,
  color: '#4F46E5',
  backgroundColor: '#E5E7EB',
  showPercentage: true,
  animate: true,
  duration: 1000
});

const animatedValue = ref(0);

// Data validation
const isValidData = computed(() => {
  return typeof props.value === 'number' &&
         !isNaN(props.value) &&
         isFinite(props.value) &&
         typeof props.max === 'number' &&
         !isNaN(props.max) &&
         isFinite(props.max) &&
         props.max > 0 &&
         props.value >= 0;
});

const center = computed(() => props.size / 2);
const radius = computed(() => (props.size - props.strokeWidth) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);
const percentage = computed(() => {
  if (!isValidData.value) return 0;
  return (Math.min(props.value, props.max) / props.max) * 100;
});
const valueSize = computed(() => Math.max(16, props.size * 0.15));
const labelSize = computed(() => Math.max(12, props.size * 0.1));

const strokeDashoffset = computed(() => {
  if (!isValidData.value) return circumference.value;
  const progress = props.animate ? animatedValue.value : props.value;
  const safeProgress = Math.max(0, Math.min(progress, props.max));
  return circumference.value - (safeProgress / props.max) * circumference.value;
});

const displayValue = computed(() => {
  if (!isValidData.value) return '0%';
  const currentValue = props.animate ? animatedValue.value : props.value;
  const safeValue = Math.max(0, Math.min(currentValue, props.max));
  if (props.showPercentage) {
    return Math.round((safeValue / props.max) * 100) + '%';
  }
  return Math.round(safeValue).toString();
});

const animateProgress = () => {
  if (!props.animate || !isValidData.value) return;

  const startTime = Date.now();
  const startValue = 0;
  const endValue = Math.max(0, Math.min(props.value, props.max));

  let animationId: number;

  const animate = () => {
    const elapsed = Date.now() - startTime;
    const progress = Math.min(elapsed / props.duration, 1);

    // Easing function (ease-out)
    const easeOut = 1 - Math.pow(1 - progress, 3);
    animatedValue.value = startValue + (endValue - startValue) * easeOut;

    if (progress < 1) {
      animationId = requestAnimationFrame(animate);
    }
  };

  animationId = requestAnimationFrame(animate);

  // Cleanup function for performance
  return () => {
    if (animationId) {
      cancelAnimationFrame(animationId);
    }
  };
};

onMounted(() => {
  if (props.animate) {
    setTimeout(animateProgress, 100);
  } else {
    animatedValue.value = props.value;
  }
});
</script>

<style scoped>
.circular-progress-container {
  position: relative;
  display: inline-block;
}

.circular-progress-svg {
  transform: rotate(-90deg);
  overflow: visible;
}

.progress-bg {
  opacity: 0.3;
}

.progress-circle {
  transition: stroke-dashoffset 0.3s ease;
}

.animate-progress {
  animation: progress-animation 1s ease-out;
}

.progress-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  pointer-events: none;
}

.progress-value {
  font-weight: bold;
  line-height: 1;
  color: #1F2937;
}

.progress-label {
  color: #6B7280;
  margin-top: 2px;
  line-height: 1;
  font-weight: 500;
}

.progress-error {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 8px;
  width: 100%;
  height: 100%;
  background-color: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 50%;
  color: #DC2626;
}

.error-text {
  font-size: 12px;
  font-weight: 500;
  text-align: center;
}

@keyframes progress-animation {
  from {
    stroke-dashoffset: var(--circumference);
  }
}
</style>
