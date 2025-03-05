<template>
    <div class="task-card">
      <div class="header">
        <q-icon name="checklist" size="20px" class="icon" />
        <span class="title">Deine Aufgaben</span>
        <q-btn flat dense label="Alle anzeigen" class="view-all-btn" @click="viewTasks" />
      </div>
  
      <div class="content">
        <div v-if="tasks.length">
          <div v-for="task in tasks" :key="task.id" class="task-item">
            <q-checkbox v-model="task.completed" />
            <div class="task-info">
              <h3 class="task-title">{{ task.title }}</h3>
              <p class="task-details">{{ task.assignedTo }} | {{ task.date }}</p>
            </div>
            <q-icon name="event" size="20px" class="calendar-icon" />
          </div>
        </div>
        <div v-else class="no-tasks">
          <p>Keine offenen Aufgaben</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  
  const tasks = ref([
    { id: 1, title: "Protokoll Erstgespräch Jason", assignedTo: "Kleinschmidt, Jason", date: "24.06.2022", completed: false }
  ]); // Später API-Anbindung
  
  function viewTasks() {
    router.push('/tasks');
  }
  </script>
  
  <style scoped>
  .task-card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  
  .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #3A4F92;
    font-weight: bold;
  }
  
  .icon {
    color: #3A4F92;
  }
  
  .view-all-btn {
    font-size: 0.8rem;
    text-transform: none;
  }
  
  .content {
    margin-top: 10px;
  }
  
  .task-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f7f9fc;
    border-radius: 10px;
    margin-top: 10px;
  }
  .title{
    font-size: 22px;
  }
  
  .task-title {
    font-size: 1rem;
    color: #2a5173;
    margin-bottom: 5px;
  }
  
  .task-details {
    font-size: 0.85rem;
    color: #666;
  }
  
  .calendar-icon {
    color: #3A4F92;
  }
  
  .no-tasks {
    color: #888;
    font-size: 1rem;
    text-align: center;
    padding: 10px;
  }
  </style>
  