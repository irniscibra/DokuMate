<template>
    <q-form @submit="updateAppointment">
      <q-card class="appointment-card">
        <q-btn icon="arrow_back_ios" flat color="primary" @click="router.go(-1)"/>
        
        <!-- Header mit Bild -->
        <q-card-section class="header">
          <q-img src="/assets/appointment.avif"></q-img>
        </q-card-section>
  
        <!-- Eingabefelder -->
        <q-card-section>
          <q-input v-model="appointment.title" label="Titel" dense outlined class="input" />
          <q-input v-model="appointment.description" label="Beschreibung" dense outlined type="textarea" class="input" />
  
          <div class="row">
            <q-select v-model="appointment.category" :options="categories" label="Kategorie" dense outlined class="input col" />
            <q-select v-model="appointment.status" :options="statuses" label="Status" dense outlined class="input col" />
          </div>
  
          <div class="row">
            <q-input v-model="appointment.date" type="date" label="Datum" dense outlined class="input col" />
            <q-input v-model="appointment.time" type="time" label="Uhrzeit" dense outlined class="input col" />
          </div>
  
          <div class="row">
            <q-input v-model="appointment.duration" type="number" label="Dauer (Minuten)" dense outlined class="input col" />
            <q-input v-model="appointment.location" label="Ort" dense outlined class="input col" />
          </div>
  
          <q-select v-model="appointment.client_id" :options="clients" label="Client auswählen" dense outlined class="input" />
        </q-card-section>
  
        <!-- Buttons -->
        <q-card-actions align="right">
          <q-btn flat label="Abbrechen" @click="router.go(-1)" />
          <q-btn color="primary" label="Speichern" type="submit" />
        </q-card-actions>
      </q-card>
    </q-form>
  </template>
  
  <script setup>
  import { ref, onMounted } from "vue";
  import { useRoute, useRouter } from "vue-router";
  import { api } from "boot/axios";
  
  const route = useRoute();
  const router = useRouter();
  
  const appointment = ref({
    title: "",
    description: "",
    category: "",
    date: "",
    time: "",
    duration: "",
    location: "",
    status: "geplant",
    client_id: null,
  });
  
  const categories = ["Meeting", "Call", "Sonstiges"];
  const statuses = ["geplant", "erledigt", "abgesagt"];
  const clients = ref([]);
  
  onMounted(async () => {
    try {
      // Bestehenden Termin abrufen
      const response = await api.get(`/appointments/${route.params.id}`);
      appointment.value = response.data;
  
      // Clients abrufen
      const clientResponse = await api.get("/clients");
      clients.value = clientResponse.data.map(client => ({ label: client.name, value: client.id }));
    } catch (error) {
      console.error("Fehler beim Laden der Daten:", error);
    }
  });
  
  async function updateAppointment() {
    try {
      await api.put(`/appointments/${route.params.id}`, appointment.value);
      router.push("/appointments");
    } catch (error) {
      console.error("Fehler beim Speichern des Termins:", error);
    }
  }
  </script>
  
  <style scoped>
  .appointment-card {
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }
  
  .row {
    display: flex;
    gap: 10px;
  }
  
  .input {
    width: 100%;
  }
  
  .header {
    text-align: center;
  }
  </style>