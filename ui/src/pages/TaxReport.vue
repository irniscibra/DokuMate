<template>
    <q-page padding>
      <q-card class="tax-report-card">
        <q-card-section>
          <h5 class="text-primary">📊 Steuerreport</h5>
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
            @update:model-value="fetchFinanceReport"
          />
        </q-card-section>
  
        <q-separator />
  
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
      </q-card>
    </q-page>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from "vue";
  import { api } from "boot/axios";
  import { useQuasar } from "quasar";
  
  const $q = useQuasar();
  const selectedPeriod = ref("last_month");
  const financeData = ref({});
  
  const periodOptions = [
    { label: "Dieser Monat", value: "this_month" },
    { label: "Letzter Monat", value: "last_month" },
    { label: "Letztes Quartal", value: "last_quarter" },
    { label: "Dieses Jahr", value: "this_year" }
  ];
  
  // 📌 API-Call für Finanzreport mit Zeitraum
  async function fetchFinanceReport() {
    try {
      const response = await api.get("/reports/finance", { params: { period: selectedPeriod.value } });
      financeData.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden des Finanzberichts:", error);
      $q.notify({ type: "negative", message: "Fehler beim Laden des Finanzberichts!" });
    }
  }
  
  async function downloadPDF() {
    try {
      const response = await api.get("/reports/finance/pdf", { responseType: "blob" });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "Steuerreport.pdf");
      document.body.appendChild(link);
      link.click();
    } catch (error) {
      console.error("Fehler beim PDF-Export:", error);
    }
  }
  
  async function downloadCSV() {
    try {
      const response = await api.get("/reports/finance/csv", { responseType: "blob" });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "Steuerreport.csv");
      document.body.appendChild(link);
      link.click();
    } catch (error) {
      console.error("Fehler beim CSV-Export:", error);
    }
  }
  
  onMounted(fetchFinanceReport);
  // watch(selectedPeriod, fetchFinanceReport);
  </script>
  
  <style scoped>
  .tax-report-card {
    border-radius: 12px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    padding: 20px;
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
  
  .text-green { color: #4caf50; }
  .text-blue { color: #1e88e5; }
  .text-orange { color: #fb8c00; }
  .text-red { color: #e53935; }
  .text-bold { font-weight: bold; }
  </style>
  