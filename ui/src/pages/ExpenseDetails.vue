<template>
    <div padding>
      <q-card class="expense-card">
        <q-card-section>
          <div class="header">
            <h5>📄 Ausgabendetails</h5>
            <q-btn icon="arrow_back" color="grey" flat @click="router.push('/expenses')" />
          </div>
        </q-card-section>
  
        <q-separator />
  
        <q-card-section>
          <p><strong>💰 Betrag:</strong> {{ expense.amount }} €</p>
          <p><strong>📂 Kategorie:</strong> {{ expense.category }}</p>
          <p><strong>📅 Datum:</strong> {{ formatDate(expense.date) }}</p>
          <p><strong>📝 Beschreibung:</strong> {{ expense.description || 'Keine Beschreibung' }}</p>
  
          <div v-if="expense.recurring">
            <q-badge color="blue">Monatlich wiederkehrend</q-badge>
          </div>
  
          <!-- 📎 Anhang-Vorschau -->
          <div v-if="expense.attachment" class="attachment-section">
            <p><strong>📎 Anhang:</strong></p>
            
            <div v-if="isImage(expense.attachment)">
              <q-img :src="attachmentUrl" class="attachment-preview" />
            </div>
            
            <div v-else-if="isPdf(expense.attachment)">
              <iframe :src="attachmentUrl" class="pdf-preview"></iframe>
            </div>
  
            <q-btn color="secondary" icon="download" label="Herunterladen" @click="downloadAttachment" class="q-mt-md" />
          </div>
        </q-card-section>
  
        <q-separator />
  
        <q-card-actions align="right">
          <q-btn color="primary" icon="edit" label="Bearbeiten" @click="editExpense" />
          <q-btn color="red" icon="delete" label="Löschen" @click="deleteExpense" />
        </q-card-actions>
      </q-card>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from "vue";
  import { useRoute, useRouter } from "vue-router";
  import { api } from "boot/axios";
  import { useQuasar } from "quasar";
  
  const route = useRoute();
  const router = useRouter();
  const $q = useQuasar();
  
  const expense = ref({});
  
 
  async function fetchExpense() {
    try {
      const response = await api.get(`/expenses/${route.params.id}`);
      expense.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden der Ausgabe:", error);
      $q.notify({ type: "negative", message: "Fehler beim Laden der Details!" });
    }
  }
  

  function formatDate(date) {
    return new Date(date).toLocaleDateString("de-DE");
  }
  
  
  function isImage(file) {
    return file?.match(/\.(jpg|jpeg|png)$/i);
  }
  
  
  function isPdf(file) {
    return file?.match(/\.pdf$/i);
  }
  
 
//   const attachmentUrl = computed(() => {
//     return expense.value.attachment ? `${import.meta.env.VITE_APP_URL}/storage/${expense.value.attachment}` : "";
//   });
const attachmentUrl = computed(() => {
  return expense.value.attachment ? `${import.meta.env.VITE_APP_URL}/${expense.value.attachment}` : "";
});
  

  function downloadAttachment() {
    const link = document.createElement("a");
    link.href = attachmentUrl.value;
    link.setAttribute("download", "Anhang");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
  
  
  function editExpense() {
    router.push(`/expenses/edit/${expense.value.id}`);
  }
  

  async function deleteExpense() {
    $q.dialog({
      title: "Ausgabe löschen?",
      message: "Bist du sicher, dass du diese Ausgabe entfernen möchtest?",
      cancel: true,
      persistent: true,
    }).onOk(async () => {
      try {
        await api.delete(`/expenses/${expense.value.id}`);
        $q.notify({ type: "positive", message: "Ausgabe gelöscht!" });
        router.push("/expenses");
      } catch (error) {
        console.error("Fehler beim Löschen der Ausgabe:", error);
        $q.notify({ type: "negative", message: "Löschen fehlgeschlagen!" });
      }
    });
  }
  
  onMounted(fetchExpense);
  </script>
  
  <style scoped>
  .expense-card {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  

  .attachment-preview {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
  }
  

  .pdf-preview {
    width: 100%;
    height: 400px;
    border: none;
  }
  
  .attachment-section {
    margin-top: 20px;
  }
  </style>
  