<template>
    <q-dialog v-model="dialogVisible" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="row justify-between">
            <h5>{{ isEditing ? "Kunden bearbeiten" : "Neuen Kunden hinzufügen" }}</h5>
            <q-btn icon="close" flat round dense @click="closeDialog" />
          </div>
        </q-card-section>
  
        <q-separator />
  
        <q-card-section>
          <q-form @submit.prevent="saveClient">
            <q-input v-model="clientData.name" label="Name" required />
            <q-input v-model="clientData.email" label="E-Mail" type="email" required />
            <q-input v-model="clientData.phone" label="Telefonnummer" />
            <q-input v-model="clientData.address" label="Adresse" type="textarea" />
  
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
  const emit = defineEmits(["clientAdded", "clientUpdated", "close"]);
  const props = defineProps({
    visible: Boolean,
    client: Object,
  });
  
  const dialogVisible = ref(false);
  const isEditing = ref(false);
  const clientData = ref({
    name: "",
    email: "",
    phone: "",
    address: "",
  });
  
  // **🔹 Watch für Sichtbarkeit des Dialogs**
  watch(
    () => props.visible,
    (newVal) => {
      dialogVisible.value = newVal;
    }
  );
  
  // **🔹 Watch für Kunden-Daten**
  watch(
    () => props.client,
    (newVal) => {
      if (newVal) {
        isEditing.value = true;
        clientData.value = { ...newVal };
      } else {
        isEditing.value = false;
        clientData.value = { name: "", email: "", phone: "", address: "" };
      }
    },
    { deep: true, immediate: true }
  );
  
  // **🔹 Speichern oder Bearbeiten des Kunden**
  async function saveClient() {
    try {
      if (isEditing.value) {
        await api.put(`/clients/${clientData.value.id}`, clientData.value);
        emit("clientUpdated", clientData.value);
      } else {
        const response = await api.post("/clients", clientData.value);
        emit("clientAdded", response.data);
      }
      closeDialog();
    } catch (error) {
      console.error("Fehler beim Speichern des Kunden:", error);
      $q.notify({ type: "negative", message: "Speichern fehlgeschlagen!" });
    }
  }
  
  // **🔹 Dialog schließen**
  function closeDialog() {
    dialogVisible.value = false;
    emit("close");
  }
  </script>
  