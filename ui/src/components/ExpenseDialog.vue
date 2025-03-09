<template>
  <q-dialog v-model="dialogVisible" persistent>
    <q-card style="min-width: 400px">
      <q-card-section>
        <div class="row justify-between">
          <h5>{{ isEditing ? "Ausgabe bearbeiten" : "Neue Ausgabe hinzufügen" }}</h5>
          <q-btn icon="close" flat round dense @click="closeDialog" />
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <q-form @submit.prevent="saveExpense">
          <q-input v-model="expenseData.amount" label="Betrag (€)" type="number" required />
          <q-select v-model="expenseData.category" :options="categoryOptions" label="Kategorie" required />
          <q-input v-model="expenseData.description" label="Beschreibung" type="textarea" />
          <q-input v-model="expenseData.date" label="Datum" type="date" required />
          <q-toggle v-model="expenseData.recurring" label="Monatliche Ausgabe" />

          <!-- Beleg Upload -->
          <q-file v-model="expenseData.attachment" label="Beleg hochladen (PDF, Bild)" accept=".jpg,.png,.pdf" />

          <q-card-actions align="right">
            <q-btn flat label="Abbrechen" @click="closeDialog" />
            <q-btn type="submit" label="Speichern" color="primary" />
          </q-card-actions>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";
import { api } from "boot/axios";
import { useQuasar } from "quasar";

const $q = useQuasar();
const emit = defineEmits(["expenseSaved", "close"]);
const props = defineProps({ modelValue: Boolean, expense: Object });

const dialogVisible = ref(props.modelValue);
const isEditing = ref(false);
const expenseData = ref({
  amount: "",
  category: "",
  description: "",
  date: "",
  recurring: false,
  attachment: null,
});

// 📌 Kategorien für Dropdown
const categoryOptions = [
  "Miete", "Löhne", "Leasing", "Software", "Büromaterial", "Reisekosten", "Sonstiges"
];

// 📌 Wenn eine Ausgabe zum Bearbeiten übergeben wird
watch(() => props.expense, (newVal) => {
  if (newVal) {
    isEditing.value = true;
    expenseData.value = { ...newVal };
  } else {
    isEditing.value = false;
    expenseData.value = { amount: "", category: "", description: "", date: "", recurring: false, attachment: null };
  }
}, { deep: true, immediate: true });

// 📌 Ausgabe speichern
async function saveExpense() {
  try {
    const formData = new FormData();
    Object.keys(expenseData.value).forEach(key => {
      formData.append(key, expenseData.value[key]);
    });

    const response = isEditing.value
      ? await api.put(`/expenses/${expenseData.value.id}`, formData)
      : await api.post("/expenses", formData);

    emit("expenseSaved", response.data);
    closeDialog();
  } catch (error) {
    console.error("Fehler beim Speichern:", error);
    $q.notify({ type: "negative", message: "Speichern fehlgeschlagen!" });
  }
}

// 📌 Dialog schließen
function closeDialog() {
  dialogVisible.value = false;
  emit("close");
}
</script>