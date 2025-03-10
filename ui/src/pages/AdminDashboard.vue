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
          <q-item-section>Rechnugsverwaltung</q-item-section>
        </q-item>

        <q-item clickable v-ripple @click="selectedTab = 'expenses'">
          <q-item-section avatar>
            <q-icon name="payments" />
          </q-item-section>
          <q-item-section>Ausgaben</q-item-section>
        </q-item>


        <q-item clickable v-ripple @click="selectedTab = 'clients'">
          <q-item-section avatar>
            <q-icon name="person" />
          </q-item-section>
          <q-item-section>Kunden</q-item-section>
        </q-item>

        <q-item clickable v-ripple @click="selectedTab = 'tax-report'">
          <q-item-section avatar>
            <q-icon name="assessment" />
          </q-item-section>
          <q-item-section>Steuer-Report</q-item-section>
        </q-item>

        <q-item clickable v-ripple @click="selectedTab = 'datev-export'">
          <q-item-section avatar>
            <q-icon name="assessment" />
          </q-item-section>
          <q-item-section>Datev Export</q-item-section>
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
import CompanySettings from "src/pages/CompanySettings.vue";
import ClientsManagment from "src/pages/ClientsManagment.vue";
import ExpensesList from "src/pages/ExpensesList.vue";
import TaxReport from "pages/TaxReport.vue";
import DatevExport from "./DatevExport.vue";
import { useRouter } from "vue-router";

const router = useRouter();

const drawer = ref(false);
const selectedTab = ref("users");

const selectedComponent = computed(() => {
  switch (selectedTab.value) {
    case "company":
      return CompanySettings;
    case "clients":
      return ClientsManagment;
    case "expenses":
      return ExpensesList;
    case "tax-report":
      return TaxReport;
      case "datev-export":
      return DatevExport;
    default:
      return MitarbeiterVerwaltung;
  }
});
</script>
