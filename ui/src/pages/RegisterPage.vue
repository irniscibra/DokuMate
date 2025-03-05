<template>
    <div class="login-container">
      <div class="login-card">
        <img src="/assets/register.avif" alt="Logo" class="logo">
        <!-- <h2>Welcome!</h2> -->
  
        <q-input v-model="name" label="Name" outlined class="q-mb-md" />
        <q-input v-model="email" label="Email" outlined class="q-mb-md" />
        <q-input v-model="password" label="Password" type="password" outlined class="q-mb-md" />
        <q-input v-model="password_confirmation" label="Confirm Password" type="password" outlined class="q-mb-md" />
  
        <q-btn @click="register" label="Create Account" class="full-width btn-login" />
        <p class="q-mt-md">Already have an account? <router-link to="/">Login</router-link></p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import { api } from 'boot/axios';
  
  const name = ref('');
  const email = ref('');
  const password = ref('');
  const password_confirmation = ref('');
  const router = useRouter();
  
  async function register() {
    try {
      const response = await api.post('/register', {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value
      });
  
      localStorage.setItem('auth_token', response.data.access_token);
      router.push('/landing');
    } catch (error) {
      console.error('Registrierung fehlgeschlagen', error);
    }
  }
  </script>
  
  <style scoped>
  .login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(135deg, #007AFF, #9C27B0);
  }
  .login-card {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    width: 350px;
    text-align: center;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
  }
  .logo {
    width: 300px;
    margin-bottom: 1rem;
  }
  .btn-login {
    background: linear-gradient(135deg, #007AFF, #9C27B0);
    color: white;
  }
  .social-icons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 10px;
  }
  </style>
  