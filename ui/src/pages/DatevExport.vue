<template>
    <q-page padding>
      <q-card class="datev-card">
        <q-card-section>
          <h5 class="text-primary">📑 DATEV-Export</h5>
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
            @update:model-value="fetchDatevReport"
          />
        </q-card-section>
  
        <q-separator />
        <q-card-section class="report-content">
          <q-table
            dense
            flat
            bordered
            :rows="datevData"
            :columns="columns"
            row-key="id"
            :pagination="{ rowsPerPage: 10 }"
            no-data-label="Keine Daten für diesen Zeitraum."
          />
        </q-card-section>
  
        <q-separator />
  
        <q-card-actions align="right">
          <q-btn color="primary" icon="download" label="DATEV CSV-Export" @click="downloadDatevCSV" />
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
  const datevData = ref([]);
  
  const periodOptions = [
    { label: "Dieser Monat", value: "this_month" },
    { label: "Letzter Monat", value: "last_month" },
    { label: "Letztes Quartal", value: "last_quarter" },
    { label: "Dieses Jahr", value: "this_year" }
  ];
  
  const columns = [
  { name: "document_number", label: "Belegnummer", field: "document_number", align: "left", sortable: true },
  { name: "date", label: "Datum", field: "date", align: "left", sortable: true },
  { name: "description", label: "Beschreibung", field: "description", align: "left" },
  { name: "tax_rate", label: "Steuer (%)", field: "tax_rate", align: "center", sortable: true },
  { name: "amount", label: "Betrag (€)", field: "amount", align: "right", sortable: true }
];
  
  async function fetchDatevReport() {
    try {
      const response = await api.get("/reports/datev", { params: { period: selectedPeriod.value } });
      datevData.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden des DATEV-Exports:", error);
      $q.notify({ type: "negative", message: "Fehler beim Laden des DATEV-Reports!" });
    }
  }
  
  async function downloadDatevCSV() {
    try {
      const response = await api.get("/reports/datev/csv", { responseType: "blob" });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "DATEV-Export.csv");
      document.body.appendChild(link);
      link.click();
    } catch (error) {
      console.error("Fehler beim CSV-Export:", error);
    }
  }
  
  onMounted(fetchDatevReport);
  watch(selectedPeriod, fetchDatevReport);
  </script>
  
  <style scoped>
  .datev-card {
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
  
  .text-bold { font-weight: bold; }
  </style>
  