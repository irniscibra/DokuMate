import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null, 
  }),
  actions: {
    setUser(userData) {
      if (userData.role) {
        userData.role = userData.role.name; // Nur den Rollennamen speichern
      }
      this.user = userData;
      localStorage.setItem('user', JSON.stringify(userData)); 
    },
    clearUser() {
      this.user = null;
      localStorage.removeItem('user'); 
    }
  }
});