<template>
    <div >
      <q-btn icon="arrow_back" flat label="Zurück" @click="router.go(-1)" />
  
      <q-card class="invoice-card">
        <q-card-section>
          <h5>Rechnung {{ invoice.invoice_number }}</h5>
          <p><strong>Datum:</strong> {{ formatDate(invoice.invoice_date) }}</p>
          <p><strong>Kunde:</strong> {{ invoice.client ? invoice.client.name : 'Kein Kunde' }}</p>
          <p><strong>Betrag:</strong> {{ invoice.total_amount }} €</p>
          <p><strong>Status:</strong> {{ invoice.status }}</p>
        </q-card-section>
  
        <q-separator />
  
        <q-card-section>
          <h6>Leistungen</h6>
          <q-list bordered>
            <q-item v-for="item in invoice.items" :key="item.id">
              <q-item-section>
                <q-item-label>{{ item.description }}</q-item-label>
                <q-item-label caption>Menge: {{ item.quantity }} | Einzelpreis: {{ item.unit_price }} €</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label>{{ item.total_price }} €</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
  
        <q-card-actions align="right">
          <q-btn color="primary" icon="picture_as_pdf" label="PDF herunterladen" @click="downloadPDF(invoice.id)" />
          <!-- <q-btn color="secondary" icon="cloud_upload" label="eRechnung erstellen" @click="generateEInvoice" /> -->
        </q-card-actions>
   
      </q-card>
      
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from "vue";
  import { useRoute, useRouter } from "vue-router";
  import { api } from "boot/axios";
  
  const route = useRoute();
  const router = useRouter();
  const invoice = ref({});
  
  async function fetchInvoice() {
    try {
      const response = await api.get(`/invoices/${route.params.id}`);
      invoice.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden der Rechnung:", error);
    }
  }
  
  function formatDate(date) {
    return new Date(date).toLocaleDateString("de-DE");
  }

async function downloadPDF(id) {
  try {
    const response = await api.get(`/invoices/${id}/pdf`, { responseType: "blob" });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `Rechnung_${id}.pdf`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    console.error("Fehler beim Generieren des PDFs:", error);
  }
}
  
  async function generateEInvoice() {
    try {
      await api.post(`/invoices/${invoice.value.id}/e-invoice`);
      alert("eRechnung erfolgreich erstellt!");
    } catch (error) {
      console.error("Fehler beim Erstellen der eRechnung:", error);
    }
  }
  
  onMounted(fetchInvoice);
  </script>
  
  <style scoped>
  .invoice-card {
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  }
  </style>
  