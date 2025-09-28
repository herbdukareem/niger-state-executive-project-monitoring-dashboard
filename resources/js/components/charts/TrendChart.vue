<template>
  <div class="trend-chart-container">
    <div class="trend-chart" :style="{ height: height + 'px' }">
      <div 
        v-for="(item, index) in chartData" 
        :key="index"
        class="trend-item"
        :style="{ 
          '--delay': (index * 100) + 'ms',
          '--bar-height': getBarHeight(item.value) + '%'
        }"
      >
        <div class="trend-bar-container">
          <div 
            class="trend-bar"
            :style="{ 
              backgroundColor: getBarColor(item.value),
              height: animate ? 'var(--bar-height)' : getBarHeight(item.value) + '%'
            }"
          >
            <div class="trend-bar-gradient"></div>
          </div>
        </div>
        
        <div class="trend-label">
          <div class="trend-month">{{ item.label }}</div>
          <div class="trend-value">{{ item.value }} {{ valueUnit }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';

interface TrendItem {
  label: string;
  value: number;
  color?: string;
}

interface Props {
  data: TrendItem[];
  height?: number;
  valueUnit?: string;
  colorScheme?: 'blue' | 'green' | 'mixed';
  animate?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  height: 200,
  valueUnit: 'projects',
  colorScheme: 'mixed',
  animate: true
});

const isAnimated = ref(false);

const chartData = computed(() => props.data);

const maxValue = computed(() => {
  return Math.max(...props.data.map(item => item.value));
});

const getBarHeight = (value: number) => {
  if (maxValue.value === 0) return 0;
  return Math.max((value / maxValue.value) * 80, 5); // Min 5% height for visibility
};

const getBarColor = (value: number) => {
  if (props.colorScheme === 'blue') {
    return '#3B82F6';
  } else if (props.colorScheme === 'green') {
    return '#10B981';
  } else {
    // Mixed color scheme based on value
    const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];
    const index = Math.floor((value / maxValue.value) * (colors.length - 1));
    return colors[index] || '#3B82F6';
  }
};

onMounted(() => {
  if (props.animate) {
    setTimeout(() => {
      isAnimated.value = true;
    }, 100);
  }
});
</script>

<style scoped>
.trend-chart-container {
  width: 100%;
}

.trend-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 8px;
  padding: 16px 0;
}

.trend-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  animation: fadeInUp 0.6s ease-out var(--delay) both;
}

.trend-bar-container {
  width: 100%;
  height: 120px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  margin-bottom: 12px;
}

.trend-bar {
  width: 80%;
  max-width: 40px;
  min-height: 8px;
  border-radius: 4px 4px 0 0;
  position: relative;
  transition: height 0.8s ease-out;
  overflow: hidden;
}

.trend-bar-gradient {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(180deg, 
    rgba(255, 255, 255, 0.3) 0%, 
    rgba(255, 255, 255, 0) 100%
  );
}

.trend-label {
  text-align: center;
  width: 100%;
}

.trend-month {
  font-size: 12px;
  color: #6B7280;
  margin-bottom: 2px;
  font-weight: 500;
}

.trend-value {
  font-size: 11px;
  color: #374151;
  font-weight: 600;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .trend-chart {
    gap: 4px;
  }
  
  .trend-bar {
    width: 90%;
  }
  
  .trend-month {
    font-size: 10px;
  }
  
  .trend-value {
    font-size: 9px;
  }
}
</style>
