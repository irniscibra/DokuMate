<template>
  <div padding>
    <q-btn icon="arrow_back" flat label="Zurück" @click="router.go(-1)" />

    <q-card class="client-card">
      <q-card-section>
        <h5>{{ client.name }}</h5>
        <p><strong>E-Mail:</strong> {{ client.email }}</p>
        <p><strong>Telefon:</strong> {{ client.phone }}</p>
        <p><strong>Adresse:</strong> {{ client.address }}</p>
      </q-card-section>
    </q-card>

    <q-separator />

    <q-card class="q-mt-md total-revenue-card">
      <q-card-section>
        <h6>📊 Gesamtumsatz</h6>
        <p class="text-h6 text-primary"><strong>{{ Number(client.total_revenue).toFixed(2) }} €</strong></p>
      </q-card-section>
    </q-card>

    <h6 class="q-mt-md">📜 Rechnungen</h6>
    <q-list bordered>
      <q-item v-for="invoice in client.invoices" :key="invoice.id" class="invoice-item">
        <q-item-section>
          <q-item-label>Rechnung {{ invoice.invoice_number }}</q-item-label>
          <q-item-label caption>📅 {{ formatDate(invoice.invoice_date) }} - <q-badge
              :color="invoice.status === 'bezahlt' ? 'green' : 'red'">
              {{ invoice.status }}
            </q-badge>
          </q-item-label>
        </q-item-section>

        <q-item-section side>
          <q-item-label class="text-bold">💰 {{ Number(invoice.total_amount).toFixed(2) }} €</q-item-label>
        </q-item-section>

        <q-item-section side>
          <q-btn flat icon="visibility" color="primary" @click="viewInvoice(invoice.id)" />
        </q-item-section>
      </q-item>
    </q-list>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api } from "boot/axios";

const route = useRoute();
const router = useRouter();
const client = ref({ invoices: [] });

// Kunde + Rechnungen abrufen
async function fetchClient() {
  try {
    const response = await api.get(`/clients/${route.params.id}`);
    client.value = response.data;
  } catch (error) {
    console.error("Fehler beim Laden des Kunden:", error);
  }
}

// Rechnung ansehen
function viewInvoice(id) {
  router.push(`/invoice/${id}`);
}

function formatDate(date) {
  return new Date(date).toLocaleDateString("de-DE");
}

onMounted(fetchClient);
</script>