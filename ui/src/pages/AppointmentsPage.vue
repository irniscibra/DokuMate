<template>
    <div class="appointments-page">
        <!-- Zurück-Button -->
        <div class="header">
            <q-btn icon="arrow_back_ios" flat color="primary" @click="router.go(-1)" />
            <h5 class="title">📅 Deine Termine</h5>
        </div>

        <!-- Ladeanzeige / Leerer Zustand -->
        <div v-if="loading" class="loading">Lade Termine...</div>
        <div v-else-if="appointments.length === 0" class="empty-state">
            <q-icon name="event_busy" size="40px" color="grey" />
            <p>Keine anstehenden Termine.</p>
        </div>

        <!-- Terminliste -->
        <div v-else class="appointments-list">
            <q-card v-for="appointment in appointments" :key="appointment.id" class="appointment-card">
                <q-card-section class="card-header">
                    <q-icon :name="getIcon(appointment.status)" :color="getColor(appointment.status)" size="24px" />
                    <div class="info">
                        <h3>{{ appointment.title }}</h3>
                        <p class="meta">
                            📅 {{ formatDate(appointment.date) }} | 🕒 {{ appointment.time }}
                        </p>
                    </div>
                    <q-chip :color="getColor(appointment.status)" text-color="white" dense>
                        {{ appointment.status }}
                    </q-chip>
                </q-card-section>
                <q-card-section>
                    <div v-if="appointment.location" class="location">
                        <q-btn flat dense icon="navigation" color="primary"
                            @click="openNavigation(appointment.location)" />
                        <span>{{ appointment.location }}</span>
                    </div>
                </q-card-section>

                <q-card-section v-if="appointment.description" class="description">
                    {{ appointment.description }}
                </q-card-section>
                <q-card-actions align="right">
                    <!-- <q-btn flat dense icon="edit" color="primary" @click="editAppointment(appointment.id)" />
                    <q-btn flat dense icon="delete" color="negative" /> -->
                    <q-fab color="warning" text-color="black" icon="keyboard_arrow_left" direction="left">
                        <q-fab-action color="primary" text-color="white" @click="editAppointment(appointment.id)"
                            icon="edit" />
                        <q-fab-action color="negative" text-color="white" icon="delete" />
                        <q-fab-action color="secondary" icon="description" title="Bericht schreiben"@click="openReportDialog(appointment)" />
                        <q-fab-action color="secondary" icon="receipt_long" title="Rechnung erstellen"@click="openInvoiceDialog(appointment)" />
                    </q-fab>
                </q-card-actions>
           

                <!-- Bericht-Dialog -->
                <q-dialog v-model="reportDialog">
                    <q-card>
                        <q-card-section>
                            <h4>Bericht für {{ appointment.title }}</h4>
                        </q-card-section>
                        <q-card-section>
                            <q-input v-model="report.workedHours" type="number" label="Geleistete Stunden" />
                            <q-input v-model="report.notes" type="textarea" label="Zusätzliche Notizen" />
                        </q-card-section>
                        <q-card-actions>
                            <q-btn flat label="Abbrechen" v-close-popup />
                            <q-btn color="primary" label="Speichern" @click="saveReport(appointment)" />
                        </q-card-actions>
                    </q-card>
                </q-dialog>

                <!-- Rechnung-Dialog -->
                <q-dialog v-model="invoiceDialog">
                    <q-card>
                        <q-card-section>
                            <h4>Rechnung für {{ appointment.title }}</h4>
                        </q-card-section>
                        <q-card-section>
                            <p>Geleistete Stunden: {{ appointment.worked_hours }}</p>
                            <p>Kosten: {{ appointment.cost }}€</p>
                        </q-card-section>
                        <q-card-actions>
                            <q-btn flat label="Abbrechen" v-close-popup />
                            <q-btn color="primary" label="Rechnung senden" @click="createInvoice" />
                        </q-card-actions>
                    </q-card>
                </q-dialog>
            </q-card>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { api } from 'boot/axios';
import { useRouter } from "vue-router";

const report = ref({
    workedHours: 0,
    notes: "",
});
const invoiceDialog = ref(false)
const reportDialog = ref(false)
const router = useRouter()
const appointments = ref([]);
const loading = ref(true);

async function fetchAppointments() {
    try {
        const response = await api.get("/appointments");
        appointments.value = response.data;
    } catch (error) {
        console.error("Fehler beim Abrufen der Termine:", error);
    } finally {
        loading.value = false;
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("de-DE", {
        weekday: "long",
        day: "numeric",
        month: "long",
    });
}

function getColor(status) {
    return {
        "geplant": "blue",
        "erledigt": "green",
        "abgesagt": "red",
    }[status] || "grey";
}

function getIcon(status) {
    return {
        "geplant": "event",
        "erledigt": "check_circle",
        "abgesagt": "cancel",
    }[status] || "help";
}

function openNavigation(address) {
    const encodedAddress = encodeURIComponent(address);
    if (navigator.userAgent.match(/(iPhone|iPad|iPod)/i)) {
        window.open(`http://maps.apple.com/?q=${encodedAddress}`, '_blank');
    } else {
        window.open(`https://www.google.com/maps/search/?api=1&query=${encodedAddress}`, '_blank');
    }
}

function editAppointment(id) {
    router.push(`/appointments/${id}/edit`);
}
const openReportDialog = (appointment) =>{
    reportDialog.value = true;
    console.log(appointment.id);
    
}

const openInvoiceDialog = (appointment) =>{
    console.log(appointment.id);
}

async function saveReport(appointment) {
  try {
    await api.post(`/appointments/${appointment.id}/report`, {
      worked_hours: report.value.workedHours,
      report: report.value.notes
    });
    reportDialog.value = false;
  } catch (error) {
    console.error("Fehler beim Speichern des Berichts:", error);
  }
}

onMounted(fetchAppointments);
</script>

<style scoped>
.appointments-page {
    padding: 20px;
}

.header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.title {
    color: #3A4F92;
    font-weight: bold;
}

.loading,
.empty-state {
    text-align: center;
    font-size: 1.2em;
    color: gray;
    margin-top: 30px;
}

.empty-state q-icon {
    display: block;
    margin: 0 auto 10px;
}

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.appointment-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.info {
    flex-grow: 1;
}

.info h3 {
    margin: 0;
    font-size: 1.2em;
    font-weight: bold;
    color: #2A5173;
}

.meta {
    font-size: 0.9em;
    color: #555;
}

.description {
    padding: 15px;
    font-size: 0.9em;
    color: #444;
}

.location {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #3A4F92;
}
</style>