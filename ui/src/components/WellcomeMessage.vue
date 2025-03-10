<template>
    <div class="welcome-container">
      <q-card class="welcome-card">
        <q-card-section>
          <h2 class="text-primary">👋 Willkommen, {{ props.admin }}!</h2>
          <p>Hier kannst du deine Rechnungen, Ausgaben und Kunden verwalten.</p>
        </q-card-section>
        <q-img src="/assets/admin-wellcome.avif" class="illustration" />


        <q-card-section>
          <q-btn color="primary" label="Jetzt starten" @click="$emit('start')" />
        </q-card-section>
      </q-card>
    </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  admin: {
    type: String,
    default: "Admin"
  }
});
</script>

<style scoped>
.welcome-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.welcome-card {
  max-width: 800px;
  text-align: center;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.illustration {
  max-width: 100%;
  border-radius: 8px;
  margin-top: 10px;
}
</style>
