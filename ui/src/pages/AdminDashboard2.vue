<template>
    <q-layout view="hHh Lpr lFf">
      <q-header elevated class="bg-primary text-white">
        <q-toolbar>
          <q-btn flat dense round icon="menu" @click="drawer = !drawer" />
          <q-toolbar-title>Admin-Dashboard</q-toolbar-title>
          <q-btn flat icon="logout" label="Adminpanel verlassen" @click="router.push('/landing')" />
        </q-toolbar>
      </q-header>
  
      <!-- Seitenmenü -->
      <q-drawer v-model="drawer" show-if-above side="left" elevated>
        <q-list>
          <q-item clickable v-ripple @click="navigateTo('clients')">
            <q-item-section avatar>
              <q-icon name="person" />
            </q-item-section>
            <q-item-section>Kunden</q-item-section>
          </q-item>
  
          <q-item clickable v-ripple @click="navigateTo('tax-report')">
            <q-item-section avatar>
              <q-icon name="assessment" />
            </q-item-section>
            <q-item-section>Steuer-Report</q-item-section>
          </q-item>
  
          <q-item clickable v-ripple @click="navigateTo('datev-export')">
            <q-item-section avatar>
              <q-icon name="file_download" />
            </q-item-section>
            <q-item-section>Datev Export</q-item-section>
          </q-item>
        </q-list>
      </q-drawer>
  
      <!-- WICHTIG: Hier werden die verschachtelten Routen angezeigt -->
      <q-page-container>
        <router-view></router-view> <!-- 🔥 Hier wird ClientsManagment oder ClientDetail geladen -->
      </q-page-container>
    </q-layout>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  
  const router = useRouter();
  const drawer = ref(false);
  
  function navigateTo(page) {
    router.push(`/admin-dashboard/${page}`);
  }
  </script>
  