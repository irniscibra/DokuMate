<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="drawer = !drawer" />
        <q-toolbar-title>Admin-Dashboard</q-toolbar-title>
        <q-btn icon="logout" label="Adminpanel verlassen" @click="router.push('/landing')"/>
      </q-toolbar>
    </q-header>

    <!-- Seitenmenü -->
    <q-drawer v-model="drawer" show-if-above side="left" elevated>
      <q-list>
        <q-item clickable v-ripple @click="selectedTab = 'users'">
          <q-item-section avatar>
            <q-icon name="group" />
          </q-item-section>
          <q-item-section>Mitarbeiterverwaltung</q-item-section>
        </q-item>

        <q-item clickable v-ripple @click="selectedTab = 'company'">
          <q-item-section avatar>
            <q-icon name="business" />
          </q-item-section>
          <q-item-section>Firmeneinstellungen</q-item-section>
        </q-item>œ

        <q-item clickable v-ripple @click="selectedTab = 'invoices'">
          <q-item-section avatar>
            <q-icon name="receipt_long" />
          </q-item-section>
          <q-item-section>Rechnungen</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <q-page padding>
        <component :is="selectedComponent"></component>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, computed } from "vue";
import MitarbeiterVerwaltung from "components/MitarbeiterVerwaltung.vue";
import CompanySettings from "components/CompanySettings.vue";
import RechnungenVerwaltung from "components/RechnungenVerwaltung.vue";
import {useRouter} from "vue-router";

const router = useRouter();

const drawer = ref(false);
const selectedTab = ref("users");

const selectedComponent = computed(() => {
  switch (selectedTab.value) {
    case "company":
      return CompanySettings;
    case "invoices":
      return RechnungenVerwaltung;
    default:
      return MitarbeiterVerwaltung;
  }
});
</script>
