<template>
    <div>
      <div class="header">
        <h5>📊 Ausgaben</h5>
        <q-card-actions align="right">
          <q-btn color="primary" icon="add" round @click="openExpenseDialog()" />
        </q-card-actions>
      </div>

    <div class="filters">
      <q-select
        v-model="selectedPeriod"
        :options="periodOptions"
        label="📆 Zeitraum wählen"
        dense outlined
        class="filter-item"
      />
      <q-select
        v-model="selectedCategory"
        :options="categoryOptions"
        label="📂 Kategorie wählen"
        dense outlined
        class="filter-item"
      />
    </div>

    <!-- SUCHFELD -->
    <q-input v-model="search"  placeholder=" Suche nach Kategorie oder Beschreibung" class="search-field">
      <template v-slot:append>
        <q-icon name="search" />
      </template>
    </q-input>
  
      <q-list bordered separator>
        <q-item v-for="expense in filteredExpenses" :key="expense.id">
          <q-item-section>
            <q-item-label>
              <strong>{{ expense.category }}</strong>
              <q-badge v-if="expense.recurring" color="blue" class="q-ml-sm">Monatlich</q-badge>
            </q-item-label>
            <q-item-label caption>
              📅 {{ formatDate(expense.date) }} | {{ expense.description }}
            </q-item-label>
          </q-item-section>
  
          <q-item-section side>
            <q-item-label class="text-bold">💰 {{ Number(expense.amount).toFixed(2) }} €</q-item-label>
          </q-item-section>
  
          <q-item-section side class="btn-group">
          <q-btn icon="visibility" color="primary" flat round @click="viewExpense(expense.id)" />
          <q-btn icon="edit" color="primary" flat round @click="editExpense(expense)" />
          <q-btn icon="delete" color="red" flat round @click="deleteExpense(expense.id)" />
        </q-item-section>
        </q-item>
      </q-list>
  
      <!-- Dialog zum Hinzufügen/Bearbeiten von Ausgaben -->
      <ExpenseDialog
        v-model="expenseDialogVisible"
        :expense="selectedExpense"
        @expenseSaved="onExpenseSaved"
      />
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from "vue";
  import { api } from "boot/axios";
  import { useQuasar } from "quasar";
  import ExpenseDialog from "components/ExpenseDialog.vue";  
  import { useRouter } from "vue-router";

  const router = useRouter();
  const $q = useQuasar();
  const expenses = ref([]);
  const search = ref("");
  const expenseDialogVisible = ref(false);
  const selectedExpense = ref(null);
  const periodOptions = [
  { label: "📆 Dieser Monat", value: "this_month" },
  { label: "📅 Letzter Monat", value: "last_month" },
  { label: "📊 Dieses Jahr", value: "this_year" },
  { label: "📉 Letztes Jahr", value: "last_year" },
  { label: "📍 Benutzerdefiniert", value: "custom" }
];

const categoryOptions = [
  "Alle", "Miete", "Löhne", "Leasing", "Software", "Büromaterial", "Reisekosten", "Sonstiges"
];

const selectedPeriod = ref("Dieser Monat");
const selectedCategory = ref(null);

const viewExpense = (id) => {
  router.push(`/expense/${id}`);
}
  
  async function fetchExpenses() {  
    try {
      const response = await api.get("/expenses");
      expenses.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden der Ausgaben:", error);
    }
  }
  
  // const filteredExpenses = computed(() => {
  //   return expenses.value.filter((expense) =>
  //     expense.category.toLowerCase().includes(search.value.toLowerCase()) ||
  //     expense.description.toLowerCase().includes(search.value.toLowerCase())
  //   );
  // });
  
  function openExpenseDialog() {
    selectedExpense.value = null;
    expenseDialogVisible.value = true;
  }
  
  function editExpense(expense) {
    selectedExpense.value = expense;
    expenseDialogVisible.value = true;
  }
  
  function onExpenseSaved(savedExpense) {
    const index = expenses.value.findIndex((e) => e.id === savedExpense.id);
    if (index !== -1) {
      expenses.value[index] = savedExpense;
    } else {
      expenses.value.push(savedExpense);
    }
  }
  
  async function deleteExpense(id) {
  $q.dialog({
    title: "Ausgabe löschen?",
    message: "Bist du sicher, dass du diese Ausgabe entfernen möchtest?",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await api.delete(`/expenses/${id}`);
      expenses.value = expenses.value.filter((e) => e.id !== id);
    } catch (error) {
      console.error("Fehler beim Löschen der Ausgabe:", error);
      $q.notify({ type: "negative", message: "Löschen fehlgeschlagen!" });
    }
  });
}

const filteredExpenses = computed(() => {
  return expenses.value
    .filter(expense => {
      if (selectedCategory.value && selectedCategory.value !== "Alle") {
        return expense.category === selectedCategory.value;
      }
      return true;
    })
    .filter(expense => {
      const expenseDate = new Date(expense.date);
      const now = new Date();
      if (selectedPeriod.value === "this_month") {
        return expenseDate.getMonth() === now.getMonth() && expenseDate.getFullYear() === now.getFullYear();
      }
      if (selectedPeriod.value === "last_month") {
        const lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        return expenseDate.getMonth() === lastMonth.getMonth() && expenseDate.getFullYear() === lastMonth.getFullYear();
      }
      if (selectedPeriod.value === "this_year") {
        return expenseDate.getFullYear() === now.getFullYear();
      }
      if (selectedPeriod.value === "last_year") {
        return expenseDate.getFullYear() === now.getFullYear() - 1;
      }
      return true;
    })
    .filter(expense =>
      expense.category.toLowerCase().includes(search.value.toLowerCase()) ||
      expense.description.toLowerCase().includes(search.value.toLowerCase())
    );
});
  
  function formatDate(date) {
    return new Date(date).toLocaleDateString("de-DE");
  }
  
  onMounted(fetchExpenses);
  </script>

<style scoped>
.expenses-container {
  max-width: 800px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.filter-item {
  flex: 1;
  min-width: 140px;
}

.search-field {
  margin-bottom: 16px;
}

.btn-group {
  display: flex;
  flex-direction: row;
  gap: 2px;
}

@media (max-width: 600px) {
  .header {
    flex-direction: column;
    gap: 10px;
  }

  .filters {
    flex-direction: column;
  }

  .btn-group {
    flex-direction: row;
    justify-content: flex-end;
  }
}
</style>

  