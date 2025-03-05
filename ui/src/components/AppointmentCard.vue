<template>
  <div class="appointment-card">
    <div class="header">
      <q-icon name="event" size="20px" class="icon" />
      <span class="title">Deine Termine</span>
      <q-btn flat dense label="Alle anzeigen" class="view-all-btn" @click="viewAppointments" />
    </div>
     <!-- Glassmorphism Next Appointment -->
     <transition name="fade">
      <div v-if="nextAppointment" class="glass-next-appointment">
        <q-icon name="schedule" size="22px" class="time-icon" />
        <div class="info">
          <p>{{ nextAppointment.title }}</p>
          <p>📅 {{ formatDate(nextAppointment.date) }} | 🕒 {{ nextAppointment.time }}</p>
          <p>📍 {{ nextAppointment.location }}</p>
        </div>
        <div class="actions">
          <q-btn dense flat icon="done" color="positive" @click="markAsDone(nextAppointment.id)" />
          <q-btn dense flat icon="delete" color="negative" @click="deleteAppointment(nextAppointment.id)" />
        </div>
      </div>
    </transition>

    <div class="content">
      <div class="info-card">
        <div class="text-section">
          <h3>Neuen Termin hinzufügen</h3>
          <p>Erstelle neue Einträge in deinem Kalender und halte Ereignisse fest.</p>
        </div>
        <div class="icon-section">
          <q-icon name="event_note" size="32px" class="calendar-icon" />
          <q-btn round dense icon="add" color="primary" class="add-btn" @click="addNewAppointment" />
        </div>
      </div>
    </div>
   
  </div>


</template>

<script setup>
import { useRouter } from 'vue-router';
import{ref,onMounted} from 'vue';
import { api } from 'src/boot/axios';

const router = useRouter();
const nextAppointment = ref(null)
const reportDialog = ref(false);
const invoiceDialog = ref(false);

onMounted(async () => {
  try {
    const response = await api.get("/appointments");
    const now = new Date();
    
    // Finde den nächsten Termin (innerhalb der nächsten Stunde)
    nextAppointment.value = response.data.find(app => {
      const appointmentTime = new Date(`${app.date}T${app.time}`);
      const diffInMinutes = (appointmentTime - now) / 60000;
      return diffInMinutes > 0 && diffInMinutes <= 60;
    });

  } catch (error) {
    console.error("Fehler beim Abrufen der Termine:", error);
  }
});


function viewAppointments() {
  router.push('/appointments');
}

function addNewAppointment() {
  router.push('/appointments/new');
}

function formatDate(date) {
  return new Date(date).toLocaleDateString("de-DE", {
    weekday: "long",
    day: "numeric",
    month: "long",
  });
}

function openReportDialog(appointment) {
  reportDialog.value = true;
}

function openInvoiceDialog(appointment) {
  invoiceDialog.value = true;
}
</script>

<style scoped>
.appointment-card {
  background: white;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  height: 100%;
  
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #3A4F92;
  font-weight: bold;
}

.icon {
  color: #3A4F92;
}

.view-all-btn {
  font-size: 0.8rem;
  text-transform: none;
}

.content {
  margin-top: 10px;
}

.info-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f7f9fc;
  border-radius: 10px;
  padding: 15px;
}

.text-section h3 {
  font-size: 1rem;
  color: #2a5173;
  margin-bottom: 5px;
}

.text-section p {
  font-size: 0.85rem;
  color: #666;
}

.icon-section {
  display: flex;
  align-items: center;
  gap: 10px;
}

.calendar-icon {
  color: #3A4F92;
}

.add-btn {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}




/* Glassmorphism Nächster Termin */
.glass-next-appointment {
  /* position: absolute;
  top: -30px;
  left: 50%; */
  /* transform: translateX(-50%); */
  width: 100%;
  background: rgba(26, 115, 232, 0.3); /* Blaues Glass */
  backdrop-filter: blur(10px);
  padding: 15px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease-in-out;
}

.glass-next-appointment:hover {
  backdrop-filter: blur(15px);
}

.time-icon {
  color: #1565C0;
}

.actions {
  display: flex;
  gap: 5px;
}
</style>
