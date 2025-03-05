<template>
    <div class="login-container">
      <div class="login-card">
        <img src="/assets/login.avif" alt="Logo" class="logo">
        <!-- <h2>Welcome back!</h2> -->
  
        <q-input v-model="email" label="Email" outlined class="q-mb-md" />
        <q-input v-model="password" label="Password" type="password" outlined class="q-mb-md">
          <template v-slot:append>
            <q-icon name="visibility" />
          </template>
        </q-input>
  
        <div class="row q-mb-md">
          <q-checkbox v-model="rememberMe" label="Remember me" />
          <q-space />
          <a href="#">Forgot password?</a>
        </div>
  
        <q-btn @click="login" label="Login" class="full-width btn-login" />
        <p class="q-mt-md">New user? <router-link to="/register">Sign Up</router-link></p>
  
        <div class="social-icons">
          <q-icon name="fab fa-twitter" />
          <q-icon name="fab fa-linkedin" />
          <q-icon name="fab fa-facebook" />
          <q-icon name="fab fa-google" />
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import { api } from 'boot/axios';
  
  const email = ref('');
  const password = ref('');
  const rememberMe = ref(false);
  const router = useRouter();
  import { useUserStore } from 'stores/userStore';

const userStore = useUserStore();
  
  async function login() {
    try {
      const response = await api.post('/login', { email: email.value, password: password.value });
  
      localStorage.setItem('auth_token', response.data.access_token);
      if (rememberMe.value) {
        localStorage.setItem('remember_me', 'true');
        localStorage.setItem('user', JSON.stringify(response.data.user));
      }
      userStore.setUser(response.data.user); // Nutzer im Store speichern
      router.push('/landing');
    } catch (error) {
      console.error('Login fehlgeschlagen', error);
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
  