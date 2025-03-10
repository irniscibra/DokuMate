<template>
  <q-page padding>

    <!-- TABS -->
    <q-tabs v-model="selectedTab" class="text-primary">
      <q-tab name="details" label="Firmeninformationen" />
      <q-tab name="edit" label="Bearbeiten" />
      <q-tab name="invoices" label="Rechnungen" />
      <!-- <q-tab name="reports" label="Steuerreport" /> -->
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
            <p><strong>Firmername:</strong>{{ settings.company_name }}</p>
            <p><strong>Adresse:</strong> {{ settings.address }}</p>
            <p><strong>E-Mail:</strong> {{ settings.email }}</p>
            <p><strong>Telefon:</strong> {{ settings.phone }}</p>
            <p><strong>Steuer-ID:</strong> {{ settings.tax_id }}</p>
            <p><strong>Geschäftsführer:</strong> {{ settings.ceo_name }}</p>
            <p><strong>Bankverbindung:</strong> {{ settings.bank_details }}</p>
            <p><strong>Bank:</strong> {{ settings.bank_name }}</p>
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
              <q-input v-model="settings.bic" label="BIC" />
              <q-input v-model="settings.bank_name" label="Bankname" />
              <q-input v-model="settings.invoice_footer" label="Rechnungstext (Fußzeile)" type="textarea" />

              <q-btn type="submit" label="Speichern" color="primary" />
            </q-form>
          </q-card-section>
        </q-card>
      </q-tab-panel>
      <!--Rechnungen-->
      <q-tab-panel name="invoices">
        <q-card-section>
          <h5 class="text-center q-ma-none q-pb-sm text-primary">Deine Rechungen</h5>
        </q-card-section>

        <q-card-section class="invoice-header">
          <q-btn dense flat icon="arrow_back_ios" @click="prevMonth" />
          <div class="invoice-month">
            <span>{{ formattedMonth }}</span>
          </div>
          <q-btn dense flat icon="arrow_forward_ios" @click="nextMonth" />
        </q-card-section>

        <q-card-section class="invoice-list">
          <div v-for="invoice in filteredInvoices" :key="invoice.id" class="invoice-card"
            :class="invoice.status === 'bezahlt' ? 'paid' : 'unpaid'">
            <div class="invoice-content">
              <div class="invoice-title">{{ invoice.client ? invoice.client.name : "Allgemeine Rechnung" }}</div>
              <div class="invoice-amount">{{ Number(invoice.total_amount).toFixed(2) }} €</div>
            </div>
            <div class="invoice-footer">
              <div class="invoice-date">{{ formatDate(invoice.invoice_date) }}</div>
              <q-btn flat round icon="visibility" @click="viewInvoice(invoice.id)" />
            </div>
          </div>
          <template v-if="filteredInvoices.length === 0">
            <div class="empty-state">
              <q-icon name="receipt_long" size="48px" color="grey-7" />
              <p>Für diesen Monat sind noch keine Rechnungen vorhanden.</p>
            </div>
          </template>

        </q-card-section>
        <q-card-actions align="center">
          <q-btn rounded dense icon="add" size="30px" color="primary" class="add-invoice-btn"
            @click="openInvoiceDialog" />
        </q-card-actions>

      </q-tab-panel>
      <!--Steuerreport-->
      <q-tab-panel name="reports">
        <q-card-section>
    <div class="header">
      <q-select 
        v-model="selectedPeriod" 
        :options="periodOptions" 
        label="Zeitraum auswählen" 
        dense outlined 
        class="period-select"
        emit-value
        map-options
        option-label="label"
        option-value="value"
      />
    </div>
  </q-card-section>

  <q-separator />

  <!-- <q-card-section class="report-content">
    <div class="report-grid">
      <q-card flat bordered class="report-item">
        <q-card-section>
          <p class="label">📈 Umsatz</p>
          <p class="value text-green">{{ taxReport.total_revenue }} €</p>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="report-item">
        <q-card-section>
          <p class="label">💰 Bezahlt</p>
          <p class="value text-blue">{{ taxReport.total_paid }} €</p>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="report-item">
        <q-card-section>
          <p class="label">⏳ Offen</p>
          <p class="value text-orange">{{ taxReport.total_open }} €</p>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="report-item">
        <q-card-section>
          <p class="label">🏦 Umsatzsteuer (19%)</p>
          <p class="value text-red">{{ taxReport.tax_amount }} €</p>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="report-item highlight">
        <q-card-section>
          <p class="label">📉 Gewinn nach Steuern</p>
          <p class="value text-bold">{{ taxReport.net_revenue }} €</p>
        </q-card-section>
      </q-card>
    </div>
  </q-card-section> -->

  <q-card-section class="report-content">
  <div class="report-grid">
    <q-card flat bordered class="report-item">
      <q-card-section>
        <p class="label">📈 Einnahmen</p>
        <p class="value text-green">{{ financeData.total_revenue }} €</p>
      </q-card-section>
    </q-card>

    <q-card flat bordered class="report-item">
      <q-card-section>
        <p class="label">💰 Bezahlt</p>
        <p class="value text-blue">{{ financeData.total_paid }} €</p>
      </q-card-section>
    </q-card>

    <q-card flat bordered class="report-item">
      <q-card-section>
        <p class="label">📉 Ausgaben</p>
        <p class="value text-orange">{{ financeData.total_expenses }} €</p>
      </q-card-section>
    </q-card>

    <q-card flat bordered class="report-item highlight">
      <q-card-section>
        <p class="label">🏦 Gewinn vor Steuern</p>
        <p class="value text-bold">{{ financeData.profit_before_tax }} €</p>
      </q-card-section>
    </q-card>

    <q-card flat bordered class="report-item highlight">
      <q-card-section>
        <p class="label">📉 Gewinn nach Steuern</p>
        <p class="value text-bold">{{ financeData.profit_after_tax }} €</p>
      </q-card-section>
    </q-card>
  </div>
</q-card-section>


  <q-separator />

  <q-card-actions align="right">
    <q-btn color="primary" icon="download" label="PDF-Export" @click="downloadPDF" />
    <q-btn color="secondary" icon="table_chart" label="CSV-Export" @click="downloadCSV" />
  </q-card-actions>


      </q-tab-panel>
    </q-tab-panels>

    <!-- Rechnungserstellung als Dialog -->
    <InvoiceDialog :visible="invoiceDialogVisible" @invoiceCreated="onInvoiceCreated"
      @close="invoiceDialogVisible = false" />
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed,watch } from "vue";
import { api } from "boot/axios";
import { useQuasar } from "quasar";
import InvoiceDialog from "components/InvoiceDialog.vue";
import { useRouter } from "vue-router";

const router = useRouter();
const $q = useQuasar();
const selectedTab = ref("details");
const invoices = ref([]);
const invoiceDialogVisible = ref(false);
const selectedMonth = ref(new Date().getMonth() + 1);
const selectedYear = ref(new Date().getFullYear());
const financeData = ref({});

const settings = ref({
  company_name: "",
  address: "",
  email: "",
  phone: "",
  tax_id: "",
  ceo_name: "",
  bank_details: "",
  bic: "",
  bank_name: "",
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

// Rechnungen nach Monat filtern
const filteredInvoices = computed(() => {
  return invoices.value.filter(inv => {
    const invDate = new Date(inv.invoice_date);
    return invDate.getMonth() + 1 === selectedMonth.value && invDate.getFullYear() === selectedYear.value;
  });
});

// Monat wechseln
function prevMonth() {
  if (selectedMonth.value === 1) {
    selectedMonth.value = 12;
    selectedYear.value--;
  } else {
    selectedMonth.value--;
  }
}

function nextMonth() {
  if (selectedMonth.value === 12) {
    selectedMonth.value = 1;
    selectedYear.value++;
  } else {
    selectedMonth.value++;
  }
}

// Monat formatieren
const formattedMonth = computed(() => {
  return new Date(selectedYear.value, selectedMonth.value - 1).toLocaleString('de-DE', { month: 'long', year: 'numeric' });
});


function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}


function viewInvoice(id) {
  router.push(`/invoice/${id}`);
  console.log("Rechnung ansehen:", id);
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
//Steuereport
const selectedPeriod = ref("Letzter Monat");
const taxReport = ref({});

const periodOptions = [
{ label: "Dieser Monat", value: "this_month" },
  { label: "Letzter Monat", value: "last_month" },
  { label: "Letztes Quartal", value: "last_quarter" },
  { label: "Dieses Jahr", value: "this_year" }
];

// watchEffect(async () => {
//   const response = await api.get("/reports/tax", { params: { period: selectedPeriod.value } });
//   taxReport.value = response.data;
// });
async function fetchTaxReport() {
  try {
    const response = await api.get(`/reports/tax?period=${selectedPeriod.value}`);
    taxReport.value = response.data;
  } catch (error) {
    console.error("Fehler beim Abrufen des Steuerreports:", error);
    $q.notify({ type: "negative", message: "Fehler beim Laden des Steuerreports" });
  }
}

const selectedFrom = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split("T")[0]);
const selectedTo = ref(new Date().toISOString().split("T")[0]);

async function fetchFinanceReport() {
  try {
    const response = await api.get("/reports/finance", {
      params: { from: selectedFrom.value, to: selectedTo.value }
    });
    financeData.value = response.data;
  } catch (error) {
    console.error("Fehler beim Laden des Finanzberichts:", error);
    $q.notify({ type: "negative", message: "Fehler beim Laden des Finanzberichts!" });
  }
}

onMounted(() => {
  fetchFinanceReport();
});

watch(selectedPeriod, async () => {
  console.log("Neuer Zeitraum:", selectedPeriod.value); 
  await fetchTaxReport();
});

async function downloadPDF() {
  const response = await api.get("/reports/tax/pdf", { responseType: "blob" });
  const url = window.URL.createObjectURL(new Blob([response.data]));
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", "Steuerreport.pdf");
  document.body.appendChild(link);
  link.click();
}
// onMounted(fetchTaxReport)
onMounted(fetchSettings);
onMounted(fetchInvoices);
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


.invoice-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.invoice-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: 0.2s ease;
}

.paid {
  background: #00bfa5;
  color: white;
}

.unpaid {
  background: #fa8072;
  color: white;
}

.invoice-content {
  flex: 1;
}

.invoice-title {
  font-size: 16px;
  font-weight: bold;
}

.invoice-amount {
  font-size: 14px;
}

.invoice-footer {
  display: flex;
  align-items: center;
  gap: 10px;
}

.invoice-date {
  font-size: 12px;
}

.invoice-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  background: var(--q-primary);
  color: white;
  border-radius: 8px;
}

.invoice-month {
  font-size: 18px;
  font-weight: bold;
}

.empty-state {
  text-align: center;
  padding: 20px;
  color: grey;
}

.empty-state q-icon {
  margin-bottom: 8px;
}

.empty-state p {
  font-size: 16px;
  font-weight: 500;
  margin-bottom: 12px;
}

/**Steuerreport style */

.tax-report-card {
  border-radius: 12px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.period-select {
  width: 100%;
}

.report-content {
  padding: 20px 0;
}

.report-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 12px;
}

.report-item {
  border-radius: 8px;
  padding: 10px;
  text-align: center;
  background: #f9f9f9;
}

.report-item.highlight {
  background: #e3f2fd;
  border: 1px solid #2196f3;
}

.label {
  font-size: 14px;
  color: #666;
}

.value {
  font-size: 18px;
  font-weight: bold;
  margin-top: 4px;
}

.text-green { color: #4caf50; }
.text-blue { color: #1e88e5; }
.text-orange { color: #fb8c00; }
.text-red { color: #e53935; }
.text-bold { font-weight: bold; }
</style>
