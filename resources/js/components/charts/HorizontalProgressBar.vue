<template>
  <div class="horizontal-progress-container">
    <!-- Error state -->
    <div v-if="!isValidData" class="progress-error">
      <v-icon color="error" size="small">mdi-alert-circle</v-icon>
      <span class="error-text">Invalid data</span>
    </div>

    <!-- Normal state -->
    <template v-else>
      <div v-if="showLabel" class="progress-header">
        <span class="progress-title">{{ title }}</span>
        <span class="progress-percentage">{{ displayValue }}</span>
      </div>

      <div class="progress-bar-wrapper" :style="{ height: height + 'px' }">
        <div
          class="progress-bar-bg"
          :style="{
            backgroundColor: backgroundColor,
            borderRadius: rounded ? (height / 2) + 'px' : '4px'
          }"
        >
          <div
            class="progress-bar-fill"
            :style="{
              width: progressWidth + '%',
              backgroundColor: color,
              borderRadius: rounded ? (height / 2) + 'px' : '4px',
              transition: animate ? `width ${duration}ms ease-out` : 'none'
            }"
          >
            <div v-if="showGradient" class="progress-gradient"></div>
          </div>
        </div>

        <!-- Value label inside bar -->
        <div
          v-if="showValueInside && progressWidth > 20"
          class="progress-value-inside"
          :style="{
            fontSize: (height * 0.6) + 'px',
            lineHeight: height + 'px'
          }"
        >
          {{ displayValue }}
        </div>
      </div>

      <!-- Budget values for budget utilization -->
      <div v-if="showBudgetValues" class="budget-values">
        <span class="budget-used">{{ formatCurrency(budgetUsed) }}</span>
        <span class="budget-separator">/</span>
        <span class="budget-total">{{ formatCurrency(budgetTotal) }}</span>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, watch } from 'vue';

interface Props {
  value: number;
  max?: number;
  title?: string;
  height?: number;
  color?: string;
  backgroundColor?: string;
  showLabel?: boolean;
  showValueInside?: boolean;
  showPercentage?: boolean;
  showGradient?: boolean;
  rounded?: boolean;
  animate?: boolean;
  duration?: number;
  budgetUsed?: number;
  budgetTotal?: number;
  showBudgetValues?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  max: 100,
  height: 24,
  color: '#4F46E5',
  backgroundColor: '#E5E7EB',
  showLabel: true,
  showValueInside: false,
  showPercentage: true,
  showGradient: false,
  rounded: true,
  animate: true,
  duration: 1000,
  showBudgetValues: false
});

const animatedProgress = ref(0);

// Data validation
const isValidData = computed(() => {
  return typeof props.value === 'number' &&
         !isNaN(props.value) &&
         isFinite(props.value) &&
         typeof props.max === 'number' &&
         !isNaN(props.max) &&
         isFinite(props.max) &&
         props.max > 0;
});

const progressWidth = computed(() => {
  if (!isValidData.value) return 0;
  const progress = props.animate ? animatedProgress.value : props.value;
  const safeProgress = Math.max(0, Math.min(progress, props.max));
  return Math.min((safeProgress / props.max) * 100, 100);
});

const displayValue = computed(() => {
  if (!isValidData.value) return '0%';
  const currentValue = props.animate ? animatedProgress.value : props.value;
  const safeValue = Math.max(0, Math.min(currentValue, props.max));
  if (props.showPercentage) {
    return Math.round((safeValue / props.max) * 100) + '%';
  }
  return Math.round(safeValue).toString();
});

const formatCurrency = (amount: number) => {
  if (amount >= 1000000000) {
    return '₦' + (amount / 1000000000).toFixed(1) + 'B';
  } else if (amount >= 1000000) {
    return '₦' + (amount / 1000000).toFixed(0) + 'M';
  } else if (amount >= 1000) {
    return '₦' + (amount / 1000).toFixed(0) + 'K';
  }
  return '₦' + amount.toLocaleString();
};

const animateProgress = () => {
  if (!props.animate) {
    animatedProgress.value = props.value;
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
    animatedProgress.value = startValue + (endValue - startValue) * easeOut;
    
    if (progress < 1) {
      requestAnimationFrame(animate);
    }
  };
  
  requestAnimationFrame(animate);
};

onMounted(() => {
  setTimeout(animateProgress, 100);
});

watch(() => props.value, () => {
  animateProgress();
});
</script>

<style scoped>
.horizontal-progress-container {
  width: 100%;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.progress-title {
  font-weight: 500;
  color: #374151;
  font-size: 14px;
}

.progress-percentage {
  font-weight: 600;
  color: #1F2937;
  font-size: 14px;
}

.progress-bar-wrapper {
  position: relative;
  width: 100%;
}

.progress-bar-bg {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  position: relative;
  overflow: hidden;
}

.progress-gradient {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, 
    rgba(255, 255, 255, 0) 0%, 
    rgba(255, 255, 255, 0.2) 50%, 
    rgba(255, 255, 255, 0) 100%
  );
}

.progress-value-inside {
  position: absolute;
  top: 0;
  left: 8px;
  color: white;
  font-weight: 600;
  font-size: 12px;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.budget-values {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-top: 4px;
  font-size: 12px;
  gap: 4px;
}

.budget-used {
  font-weight: 600;
  color: #1F2937;
}

.budget-separator {
  color: #6B7280;
}

.budget-total {
  color: #6B7280;
}

.progress-error {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background-color: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 6px;
  color: #DC2626;
}

.error-text {
  font-size: 12px;
  font-weight: 500;
}
</style>
