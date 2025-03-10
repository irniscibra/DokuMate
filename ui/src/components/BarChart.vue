<template>
    <div class="chart-container">
      <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { defineProps, computed, watch } from "vue";
  import { Bar } from "vue-chartjs";
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
  } from "chart.js";
  
  // Chart.js registrieren
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);
  
  const props = defineProps({
    financeData: Object // Erwartet dynamische Finanzdaten
  });
  
  // 🟢 Computed Property: Aktualisiert sich, wenn financeData sich ändert
  const chartData = computed(() => {
    if (!props.financeData) return null;
  
    return {
      labels: ["Einnahmen", "Gewinn vor Steuern", "Gewinn nach Steuern"],
      datasets: [
        {
          label: "Finanzen (€)",
          backgroundColor: ["#4caf50", "#1E88E5", "#E53935"], // Grüne Einnahmen, Blaue Gewinne, Rote Verluste
          data: [
            props.financeData.total_revenue || 0,       // Einnahmen
            props.financeData.profit_before_tax || 0,   // Gewinn vor Steuern
            props.financeData.profit_after_tax || 0     // Gewinn nach Steuern
          ]
        }
      ]
    };
  });
  
  // 🛠 Optionen für den Chart
  const chartOptions = {
    responsive: true,
    plugins: {
      legend: {
        position: "bottom"
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  };
  </script>
  
  <style scoped>
  .chart-container {
    max-width: 600px; 
    height: 300px;   
    margin: auto;     
  }
  </style>
  