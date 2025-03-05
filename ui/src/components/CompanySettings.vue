<template>
  <q-page padding>
    <h4>🏢 Rechnungeinstellungen</h4>

    <!-- TABS -->
    <q-tabs v-model="selectedTab" class="text-primary">
      <q-tab name="details" label="Firmeninformationen" />
      <q-tab name="edit" label="Bearbeiten" />
      <q-tab name="invoices" label="Rechnungen"/>
    </q-tabs>

    <q-tab-panels v-model="selectedTab" animated>
      <!-- Firmeninfo Panel -->
      <q-tab-panel name="details">
        <q-card class="company-info-card">
          <q-card-section>
            <div class="info-header">
              <h5>{{ settings.company_name }}</h5>
              <q-btn icon="edit" color="primary" flat @click="switchToEdit" />
            </div>
            <p><strong>Adresse:</strong> {{ settings.address }}</p>
            <p><strong>E-Mail:</strong> {{ settings.email }}</p>
            <p><strong>Telefon:</strong> {{ settings.phone }}</p>
            <p><strong>Steuer-ID:</strong> {{ settings.tax_id }}</p>
            <p><strong>Geschäftsführer:</strong> {{ settings.ceo_name }}</p>
            <p><strong>Bankverbindung:</strong> {{ settings.bank_details }}</p>
            <p><strong>Rechnungstext:</strong> {{ settings.invoice_footer }}</p>
          </q-card-section>
        </q-card>
      </q-tab-panel>

      <!-- Bearbeitungsformular Panel -->
      <q-tab-panel name="edit">
        <q-card class="edit-card">
          <q-card-section>
            <q-form @submit.prevent="saveSettings">
              <q-input v-model="settings.company_name" label="Firmenname" required />
              <q-input v-model="settings.address" label="Adresse" required type="textarea" />
              <q-input v-model="settings.email" label="E-Mail" required />
              <q-input v-model="settings.phone" label="Telefon" required />
              <q-input v-model="settings.tax_id" label="Steuer-ID" required />
              <q-input v-model="settings.ceo_name" label="Geschäftsführer" required />
              <q-input v-model="settings.bank_details" label="Bankverbindung" />
              <q-input v-model="settings.invoice_footer" label="Rechnungstext (Fußzeile)" type="textarea" />

              <q-btn type="submit" label="Speichern" color="primary" />
            </q-form>
          </q-card-section>
        </q-card>
      </q-tab-panel>

      <q-tab-panel name="invoices">
        <q-card>
          <q-card-section>
            <div class="row justify-between">
              <h5>📜 Rechnungen</h5>
              <q-btn dense flat icon="add" color="primary" label="Neue Rechnung" @click="openInvoiceDialog" />
            </div>
          </q-card-section>

          <q-table :rows="invoices"  row-key="id" dense bordered>
            <template v-slot:body-cell(actions)="props">
              <q-td>
                <q-btn color="primary" icon="visibility" label="Ansehen" @click="viewInvoice(props.row)" />
              </q-td>
            </template>
          </q-table>
        </q-card>
      </q-tab-panel>
    </q-tab-panels>

    <!-- Rechnungserstellung als Dialog -->
    <InvoiceDialog :visible="invoiceDialogVisible" @invoiceCreated="onInvoiceCreated" @close="invoiceDialogVisible = false" />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { api } from "boot/axios";
import { useQuasar } from "quasar";
import InvoiceDialog from "components/InvoiceDialog.vue";

const $q = useQuasar();
const selectedTab = ref("details");
const invoices = ref([]);
const invoiceDialogVisible = ref(false);

const settings = ref({
  company_name: "",
  address: "",
  email: "",
  phone: "",
  tax_id: "",
  ceo_name: "",
  bank_details: "",
  invoice_footer: ""
});
const settingsId = ref(null);

// Firmen-Daten abrufen
async function fetchSettings() {
  try {
    const response = await api.get("/company-settings");
    if (response.data) {
      settings.value = response.data;
      settingsId.value = response.data.id; // Speichert die ID der Firmeneinstellungen
    }
  } catch (error) {
    console.error("Fehler beim Laden der Firmendaten:", error);
    $q.notify({ type: "negative", message: "Fehler beim Laden der Firmendaten!" });
  }
}

// Speichern der Daten
async function saveSettings() {
  try {
    if (settingsId.value) {
      await api.put(`/company-settings/${settingsId.value}`, settings.value);
    } else {
      const response = await api.post("/company-settings", settings.value);
      settingsId.value = response.data.id; // Setzt die ID nach dem Speichern
    }
    $q.notify({ type: "positive", message: "Firmendaten erfolgreich gespeichert!" });
    selectedTab.value = "details"; // Zurück zur Ansicht wechseln
  } catch (error) {
    console.error("Fehler beim Speichern:", error);
    $q.notify({ type: "negative", message: "Fehler beim Speichern der Firmendaten!" });
  }
}

// Bearbeiten aktivieren
function switchToEdit() {
  selectedTab.value = "edit";
}
// Rechnungen abrufen
async function fetchInvoices() {
  try {
    const response = await api.get("/invoices");
    invoices.value = response.data;
  } catch (error) {
    console.error("Fehler beim Laden der Rechnungen:", error);
  }
}

// Dialog öffnen
function openInvoiceDialog() {
  invoiceDialogVisible.value = true;
}

// Neue Rechnung hinzufügen
function onInvoiceCreated(newInvoice) {
  invoices.value.push(newInvoice);
  invoiceDialogVisible.value = false;
}


onMounted(fetchSettings,fetchInvoices);
</script>

<style scoped>
.company-info-card {
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.info-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.edit-card {
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}
</style>
