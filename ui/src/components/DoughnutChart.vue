<template>
    <div class="chart-container">
      <Doughnut v-if="chartData" :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { defineProps, computed } from "vue";
  import { Doughnut } from "vue-chartjs";
  import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";
  
  // Chart.js registrieren
  ChartJS.register(Title, Tooltip, Legend, ArcElement);
  
  const props = defineProps({
    revenue: Number,
    expenses: Number
  });
  
  const chartData = computed(() => ({
    labels: ["Einnahmen", "Ausgaben"],
    datasets: [
      {
        backgroundColor: ["#4caf50", "#fb8c00"],
        data: [props.revenue || 0, props.expenses || 0]
      }
    ]
  }));
  
  const chartOptions = {
    responsive: true,
    plugins: {
      legend: {
        position: "bottom"
      }
    }
  };
  </script>

<style scoped>
.chart-container {
  max-width: 300px;  /* 🔹 Maximal 300px Breite */
  margin: auto;       /* 🔹 Zentrieren */
}
</style>
  