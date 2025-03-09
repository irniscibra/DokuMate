<template>
    <div padding>
  
      <!-- Suchfeld -->
      <q-input v-model="search" filled placeholder="🔍 Kunden suchen..." class="q-mb-md">
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
      <q-card-actions align="right">
        <q-btn icon="add" color="primary" title="Kunden hinzufügen" @click="openClientDialog()"/>

      </q-card-actions>
  
      <!-- Kundenliste -->
      <q-list bordered separator>
        <q-item v-for="client in filteredClients" :key="client.id" class="client-item">
          <q-item-section>
            <q-item-label>
              {{ client.name }}
              <q-badge v-if="client.open_invoices > 0" color="red" class="q-ml-sm">
                {{ client.open_invoices }} offene Rechnungen
              </q-badge>
            </q-item-label>
            <q-item-label caption>
              📧 {{ client.email }} | 📞 {{ client.phone }}
            </q-item-label>
          </q-item-section>
  
          <q-item-section side>
            <q-item-label class="text-bold">💰 {{ Number(client.total_amount_due).toFixed(2) }} €</q-item-label>
            <q-item-label caption>Gesamtbetrag offen</q-item-label>
          </q-item-section>
  
          <q-item-section side>
            <q-btn flat icon="visibility" color="primary" @click="viewClient(client.id)" />
            <q-btn icon="edit" color="primary" flat round @click="editClient(client)" />
            <q-btn icon="delete" color="red" flat round @click="deleteClient(client.id)" />
          </q-item-section>
        </q-item>
      </q-list>
       <!-- Client Dialog -->
    <ClientDialog :visible="clientDialogVisible" :client="selectedClient" @clientAdded="onClientAdded" 
      @clientUpdated="onClientUpdated" @close="clientDialogVisible = false" />
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from "vue";
  import { api } from "boot/axios";
  import { useRouter } from "vue-router";
  import ClientDialog from "components/ClientDialog.vue";
  import { useQuasar } from "quasar";

  const $q = useQuasar();
  const router = useRouter();
  const clients = ref([]);
  const search = ref("");
  const clientDialogVisible = ref(false);
  const selectedClient = ref(null);
  
  // Kunden aus API abrufen
  async function fetchClients() {
    try {
      const response = await api.get("/clients");
      clients.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden der Kunden:", error);
    }
  }
  
  // Gefilterte Kunden für die Anzeige
  const filteredClients = computed(() => {
    return clients.value.filter(client => client.name.toLowerCase().includes(search.value.toLowerCase()));
  });
  
  // Kunden-Detailseite aufrufen
  function viewClient(id) {
    router.push(`/clients/${id}`);
  }

// Neuen Kunden-Dialog öffnen
function openClientDialog() {
  selectedClient.value = null;
  clientDialogVisible.value = true;
}

// Kunden bearbeiten
function editClient(client) {
  selectedClient.value = client;
  clientDialogVisible.value = true;
}

// Kunden speichern
function onClientAdded(newClient) {
  clients.value.push(newClient);
}

// Kunden aktualisieren
function onClientUpdated(updatedClient) {
  const index = clients.value.findIndex(c => c.id === updatedClient.id);
  if (index !== -1) {
    clients.value[index] = updatedClient;
  }
}

// Kunden löschen
async function deleteClient(id) {
  $q.dialog({
    title: "Kunde löschen?",
    message: "Bist du sicher, dass du diesen Kunden löschen möchtest?",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await api.delete(`/clients/${id}`);
      clients.value = clients.value.filter(c => c.id !== id);
    } catch (error) {
      console.error("Fehler beim Löschen des Kunden:", error);
      $q.notify({ type: "negative", message: "Löschen fehlgeschlagen!" });
    }
  });
}
  
  onMounted(fetchClients);
  </script>
  