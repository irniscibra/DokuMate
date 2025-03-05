<template>
     <div class="admin-dashboard">
    
    <h5 class="text-primary">👥 Mitarbeiterverwaltung</h5>
    <div class="user-grid" v-if="users.length > 0">
      <q-card v-for="user in users" :key="user.id" class="user-card">
        <q-card-section>
          <div class="user-info">
            <q-avatar icon="person" size="xl" />
            <div>
              <h6>{{ user.name }}</h6>
              <p>{{ user.email }}</p>
            </div>
          </div>
        </q-card-section>
        <q-card-section>
          <q-chip :color="user.active ? 'green' : 'red'" text-color="white">
            {{ user.active ? 'Aktiv' : 'Inaktiv' }}
          </q-chip>
          <p>Stunden: {{ user.total_hours }}</p>
          <p>Verdienst: {{ user.total_earnings }} €</p>
        </q-card-section>
        <q-card-actions>
          <q-btn color="primary" label="Berichte ansehen" @click="openReportsDialog(user)"/>
        </q-card-actions>
      </q-card>
    </div>

    <div v-else class="empty-state">Keine Mitarbeiter gefunden.</div>

    <!-- Bericht-Dialog -->
    <q-dialog v-model="reportsDialog">
      <q-card>
        <q-card-section>
          <h4>Berichte von {{ selectedUser.name }}</h4>
        </q-card-section>
        <q-card-section v-if="selectedUser.appointments.length > 0">
          <q-list>
            <q-item v-for="appointment in selectedUser.appointments" :key="appointment.id">
              <q-item-section>
                <q-item-label><b>Termin:</b> {{ appointment.title }}</q-item-label>
                <q-item-label caption><b>Datum:</b> {{ appointment.date }} | <b>Verdienst:</b> {{ appointment.cost }} €</q-item-label>
                <q-item-label>{{ appointment.report }}</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-btn v-if="!appointment.billed" color="primary" icon="receipt_long" @click="generateInvoice(appointment)" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
        <q-card-section v-else>
          <p>Keine Berichte vorhanden.</p>
        </q-card-section>
        <q-card-actions>
          <q-btn flat label="Schließen" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { api } from "boot/axios";
import {useRouter} from "vue-router";

const users = ref([]);
const reportsDialog = ref(false);
const selectedUser = ref({});
const router = useRouter();

async function fetchUsers() {
  try {
    const response = await api.get("/users-with-reports");
    users.value = response.data;
  } catch (error) {
    console.error("Fehler beim Abrufen der Mitarbeiter:", error);
  }
}

function openReportsDialog(user) {
  selectedUser.value = user;
  reportsDialog.value = true;
}

async function generateInvoice(appointment) {
  try {
    await api.post(`/appointments/${appointment.id}/invoice`);
    appointment.billed = 1;
  } catch (error) {
    console.error("Fehler beim Erstellen der Rechnung:", error);
  }
}

onMounted(fetchUsers);
</script>
<style scoped>
.admin-dashboard {
  display: flex;
  flex-direction: column;
  padding: 20px;
}

.user-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.user-card {
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.empty-state {
  text-align: center;
  font-size: 1.2em;
  color: gray;
}
</style>