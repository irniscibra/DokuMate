<template>
    <q-form @submit="saveAppointment">
      <q-card class="appointment-card">
        <q-btn icon="arrow_back_ios" flat color="primary" @click="router.go(-1)"/>
        <q-card-section class="header">
          <q-img src="/assets/appointment.avif"></q-img>
        </q-card-section>
  
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
  
        <q-card-actions align="right">
          <q-btn flat label="Abbrechen" @click="$emit('cancel')" />
          <q-btn color="primary" label="Speichern" type="submit" />
        </q-card-actions>
      </q-card>
    </q-form>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { api } from 'boot/axios';
  import{useRouter} from 'vue-router';
  
    const emit = defineEmits(["save", "cancel"]);
    const router = useRouter();
  
  const appointment = ref({
    title: '',
    description: '',
    category: '',
    date: '',
    time: '',
    duration: '',
    location: '',
    status: 'geplant',
    client_id: null,
  });
  
  const categories = ['Meeting', 'Call', 'Sonstiges'];
  const statuses = ['geplant', 'erledigt', 'abgesagt'];
  const clients = ref([]);
  
  // onMounted(async () => {
  //   try {
  //     const response = await api.get('/clients'); 
  //     clients.value = response.data.map(client => ({ label: client.name, value: client.id }));
  //   } catch (error) {
  //     console.error("Fehler beim Laden der Clients:", error);
  //   }
  // });
  
  function saveAppointment() {
    console.log("Termin-Daten:", appointment.value);
    api.post('/appointments', appointment.value)
      .then(() => {
        console.log("Termin erfolgreich gespeichert!");
        emit('save', appointment.value);
      })
      .catch(error => {
        console.error("Fehler beim Speichern des Termins:", error);
      });
  }
  </script>
  
  <style scoped>
  .appointment-card {
    background: #ffffff;
    /* padding: 20px; */
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    margin: auto;
  }
  
  .header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
    color: #3A4F92;
  }
  
  .header-icon {
    color: #3A4F92;
  }
  
  .input {
    width: 100%;
    margin-bottom: 10px;
  }
  
  .row {
    display: flex;
    gap: 10px;
  }
  
  .col {
    flex: 1;
  }
  </style>
  