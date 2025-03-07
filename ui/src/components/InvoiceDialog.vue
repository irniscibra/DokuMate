<template>
    <q-dialog v-model="dialogVisible" persistent>
      <q-card class="invoice-dialog" style="width: 600px;">
        <q-card-section>
          <div class="row justify-between">
            <h5>📝 Rechnung erstellen</h5>
            <q-btn icon="close" flat round dense @click="closeDialog" />
          </div>
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="saveInvoice">
            <q-select
              v-model="newInvoice.client_id"
              :options="clientOptions"
              option-label="name"
              option-value="id"
              emit-value
              map-options
              label="Kunde (optional)"
            />
  
            <q-input v-model="newInvoice.invoice_date" label="Rechnungsdatum" type="date" required />
  
            <h6>Leistungen</h6>
            <q-list bordered>
              <q-item v-for="(item, index) in newInvoice.items" :key="index">
                <q-item-section>
                  <q-input v-model="item.description" label="Beschreibung" dense required />
                </q-item-section>
                <q-item-section>
                  <q-input v-model="item.quantity" label="Menge" type="number" dense required />
                </q-item-section>
                <q-item-section>
                  <q-input v-model="item.unit_price" label="Preis (€)" type="number" dense required />
                </q-item-section>
                <q-item-section side>
                  <q-btn icon="delete" color="red" flat @click="removeItem(index)" />
                </q-item-section>
              </q-item>
            </q-list>
            <q-btn icon="add" label="Leistung hinzufügen" flat @click="addItem" />
  
            <q-separator />
  
            <q-input v-model="totalAmount" label="Gesamtbetrag (€)" readonly />
  
            <q-card-actions align="right">
              <q-btn flat label="Abbrechen" @click="closeDialog" />
              <q-btn type="submit" label="Rechnung speichern" color="primary" />
            </q-card-actions>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </template>
  
  <script setup>
  import { ref, computed, defineProps, defineEmits, onMounted, watch } from "vue";
  import { api } from "boot/axios";
  import { useQuasar } from "quasar";
  import { useUserStore } from "src/stores/userStore";

  const authStore = useUserStore()
  
  const $q = useQuasar();
  const emit = defineEmits(["invoiceCreated", "close"]);
  const props = defineProps({
    visible: Boolean,
  });
  
  const dialogVisible = ref(false);
  const clientOptions = ref([]);
  const newInvoice = ref({
    client_id: null,
    invoice_date: "",
    items: [],
  });
  
  // Aktualisiert `dialogVisible`, wenn `visible` sich ändert
  watch(() => props.visible, (newVal) => {
    dialogVisible.value = newVal;
  });
  
  // Berechnung des Gesamtbetrags
  const totalAmount = computed(() => {
  return parseFloat(
    newInvoice.value.items.reduce((sum, item) => {
      return sum + item.quantity * item.unit_price;
    }, 0)
  ).toFixed(2);
});
  // Kunden abrufen
  async function fetchClients() {
    try {
      const response = await api.get("/clients");
      clientOptions.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden der Kunden:", error);
    }
  }
  
  // Neue Rechnung speichern
  async function saveInvoice() {
    try {
      const response = await api.post("/invoices", {
        company_id: authStore.user.company_id,
        user_id: authStore.user.id,
        client_id: newInvoice.value.client_id,
        invoice_date: newInvoice.value.invoice_date,
        total_amount: parseFloat(totalAmount.value),
        status:"offen",
        items: newInvoice.value.items
      });
  
      emit("invoiceCreated", response.data);
      $q.notify({ type: "positive", message: "Rechnung erfolgreich erstellt!" });
      closeDialog();
    } catch (error) {
      console.error("Fehler beim Speichern:", error);
      $q.notify({ type: "negative", message: "Fehler beim Speichern!" });
    }
  }
  
  // Leistungen hinzufügen/entfernen
  function addItem() {
    newInvoice.value.items.push({ description: "", quantity: 1, unit_price: 0 });
  }
  function removeItem(index) {
    newInvoice.value.items.splice(index, 1);
  }
  
  // Dialog schließen
  function closeDialog() {
    dialogVisible.value = false;
    emit("close");
  }
  
  onMounted(fetchClients);
  </script>
  