<template>
  <div class="landing-page">
    <!-- Datum und Wochentag -->
    <div class="date-header">
      <h4>{{ formattedDate }}</h4>
    </div>

    <!-- Grid-System für Kacheln -->
    <div class="grid-container">
      <!-- Begrüßungskachel -->
      <div class="grid-item greeting-card">
        <div class="greeting-content">
          <img :src="greetingImage" alt="Tageszeit-Bild" class="greeting-image">
          <div class="greeting-text">
            <h4>Guten {{ greetingTime }}, {{ userName }}</h4>
            <p>Willkommen zurück! Hier ist dein Überblick.</p>
          </div>
        </div> 
          <q-btn round flat @click="toggleDropdown" icon="more_horizontal" />
        <q-menu v-model="dropdownOpen" anchor="bottom right" self="top right">
          <q-list>
            <q-item clickable v-close-popup @click="openProfile">
              <q-item-section>
                <q-icon name="person" class="q-mr-sm" /> Profil
              </q-item-section>
            </q-item>

            <q-item v-if="isAdmin" clickable v-close-popup @click="openAdminDashboard">
              <q-item-section>
                <q-icon name="admin_panel_settings" class="q-mr-sm" /> Admin-Dashboard
              </q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </div>

      <!-- Platzhalter für weitere Kacheln -->
      <div class="grid-item placeholder-card">
        <AppointmentCard />
      </div>
      <div class="grid-item placeholder-card">
        <TaskCard />
      </div>
      <div class="grid-item placeholder-card">📊 Statistik</div>
      <div class="grid-item placeholder-card">👥 Klienten</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from 'stores/userStore';
import AppointmentCard from 'src/components/AppointmentCard.vue';
import TaskCard from 'src/components/TaskCard.vue';

const userStore = useUserStore();
const userName = computed(() => userStore.user?.name || 'Gast');
const router = useRouter();
const dropdownOpen = ref(false);

const now = new Date();
const formattedDate = computed(() => {
  return now.toLocaleDateString('de-DE', { weekday: 'long', day: 'numeric', month: 'long' });
});

const greetingTime = computed(() => {
  const hour = now.getHours();
  if (hour < 18) return 'Morgen';
  return 'Abend';
});

const greetingImage = computed(() => {
  return now.getHours() < 18 ? '/assets/day.jpg' : '/assets/night.avif';
});

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function openProfile() {
  router.push('/profile');
}

const openAdminDashboard = () => {
  router.push('/admin-dashboard');
};

const isAdmin = userStore.user?.roles.some(role => role.name === "Admin");
</script>

<style scoped>
.landing-page {
  display: flex;
  flex-direction: column;
  padding: 10px;
}

/* Grid für Desktop */
.grid-container {
  display: grid;
  /* grid-template-columns: repeat(3, 1fr); */
  grid-template-columns: 1fr 2fr 2fr;
  gap: 15px;
  margin-top: 20px;
}

/* Erste Kachel größer */
.greeting-card {
  background: #3A4F92;
  color: white;
  padding: 20px;
  border-radius: 15px;
  text-align: center;
  grid-column: span 1;
}

.greeting-image {
  width: 100px;
  height: auto;
}


.placeholder-card {
  background: #F3F4F6;
  color: #333;
  border-radius: 15px;
  text-align: center;
  font-weight: bold;
}

/* Mobile-Version */
@media (max-width: 768px) {
  .grid-container {
    grid-template-columns: 1fr;
  }

  .greeting-card {
    grid-column: span 1;
  }
}
</style>