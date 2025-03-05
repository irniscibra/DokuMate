<template>
    <div class="appointment-page">
      <AppointmentForm @save="handleSave" @cancel="goBack" />
    </div>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  import AppointmentForm from 'src/components/AppointmentForm.vue';
  
  const router = useRouter();
  
  function handleSave(appointmentData) {
    console.log("Termin gespeichert:", appointmentData);
    router.push('/appointments'); // Nach dem Speichern zur Terminliste zurück
  }
  
  function goBack() {
    router.push('/appointments'); // Zurück zur Terminübersicht
  }
  </script>
  
  <style scoped>
  .appointment-page {
    max-width: 600px;
    margin: auto;
    padding: 20px;
  }
  h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #3A4F92;
  }
  </style>