<template>
    <div>
      <div class="header">
        <h5>📊 Ausgaben</h5>
        <q-btn color="primary" icon="add" label="Neue Ausgabe" @click="openExpenseDialog()" />
      </div>
  
      <!-- Filter -->
      <q-input v-model="search" filled placeholder="🔍 Suche nach Kategorie oder Beschreibung" class="q-mb-md">
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
  
          <q-item-section side>
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


  const $q = useQuasar();
  const expenses = ref([]);
  const search = ref("");
  const expenseDialogVisible = ref(false);
  const selectedExpense = ref(null);
  
  async function fetchExpenses() {  
    try {
      const response = await api.get("/expenses");
      expenses.value = response.data;
    } catch (error) {
      console.error("Fehler beim Laden der Ausgaben:", error);
    }
  }
  
  const filteredExpenses = computed(() => {
    return expenses.value.filter((expense) =>
      expense.category.toLowerCase().includes(search.value.toLowerCase()) ||
      expense.description.toLowerCase().includes(search.value.toLowerCase())
    );
  });
  
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
  
  function formatDate(date) {
    return new Date(date).toLocaleDateString("de-DE");
  }
  
  onMounted(fetchExpenses);
  </script>
  