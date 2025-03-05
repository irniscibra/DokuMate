<template>
    <div class="profile-page">
      <div class="profile-container">
        <q-card-actions align="left">
            <q-btn icon="arrow_back_ios" color="primary" flat class="back-btn" @click="goBack" />
        </q-card-actions>
        <q-img src="assets/person.avif"/>
        <h2>Mein Profil</h2>
    
        <q-form @submit.prevent="updateProfile">
          <q-input v-model="user.name" label="Name" outlined class="profile-input" />
          <q-input v-model="user.email" label="E-Mail" type="email" outlined class="profile-input" />
          
          <q-expansion-item label="Passwort ändern" expand-separator>
            <q-input v-model="password" label="Neues Passwort" type="password" outlined class="profile-input" />
            <q-input v-model="confirmPassword" label="Passwort bestätigen" type="password" outlined class="profile-input" />
          </q-expansion-item>
         
          <q-btn type="submit" label="Speichern" color="primary" class="save-btn" />
        </q-form>
        
        
    <q-btn label="Logout" color="negative" flat @click="logout" class="logout-btn" />

    <q-banner v-if="message" dense class="message-banner">
        {{ message }}
      </q-banner>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
import { useUserStore } from 'stores/userStore';
import { useRouter } from 'vue-router';
import { api } from 'boot/axios';

const userStore = useUserStore();
const router = useRouter();

const user = ref({ ...userStore.user });
const password = ref('');
const confirmPassword = ref('');
const currentPassword = ref('');
const message = ref(null);


async function updateProfile() {
  try {
    const response = await api.put('/profile/update', {
      name: user.value.name,
      email: user.value.email,
    });

    userStore.setUser(response.data.user); 
    message.value = 'Profil aktualisiert!';
  } catch (error) {
    console.error(error);
    message.value = 'Fehler beim Speichern!';
  }
}

// Passwort ändern
async function changePassword() {
  try {
    const response = await api.put('/profile/change-password', {
      current_password: currentPassword.value,
      new_password: password.value,
      new_password_confirmation: confirmPassword.value,
    });

    message.value = 'Passwort erfolgreich geändert!';
    password.value = '';
    confirmPassword.value = '';
    currentPassword.value = '';
  } catch (error) {
    console.error(error);
    message.value = 'Fehler beim Ändern des Passworts!';
  }
}

const goBack = () => router.back();

// Logout
function logout() {
  userStore.clearUser();
  localStorage.removeItem('auth_token');
  router.push('/');
}
  </script>
  
  <style scoped>
  .profile-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding: 20px;
  }
  
  .profile-container {
    background: white;
    padding: 20px;
    border-radius: 15px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
  }
  
  .profile-input {
    margin-bottom: 15px;
  }
  
  .save-btn {
    width: 100%;
    margin-top: 10px;
  }
  
  .logout-btn {
    width: 100%;
    margin-top: 20px;
  }
  .back-btn {
  align-self: flex-start;
  margin-bottom: 10px;
}
  </style>