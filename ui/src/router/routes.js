// const routes = [
//   {
//     path: '/',
//     component: () => import('layouts/MainLayout.vue'),
//     children: [
//       { path: '', component: () => import('pages/IndexPage.vue') }
//     ]
//   },

//   // Always leave this as last one,
//   // but you can also remove it
//   {
//     path: '/:catchAll(.*)*',
//     component: () => import('pages/ErrorNotFound.vue')
//   }
// ]

// export default routes


import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from 'pages/LoginPage.vue';
import RegisterPage from 'pages/RegisterPage.vue';
import LandingPage from 'pages/LandingPage.vue';
import ProfilePage from 'pages/ProfilePage.vue';
import AppointmentsPage from 'src/pages/AppointmentsPage.vue';

const routes = [
  { path: '/', component: LoginPage, meta: { guestOnly: true } },
  { path: '/register', component: RegisterPage, meta: { guestOnly: true } },
  { path: '/landing', component: LandingPage, meta: { requiresAuth: true } },
  { path: '/profile', component: ProfilePage, meta: { requiresAuth: true } },
  {path : '/appointments',component: AppointmentsPage, meta: { requiresAuth: true } },
  {path:'/admin-dashboard', component: () => import('src/pages/AdminDashboard.vue'), meta: { requiresAuth: true } },
  {
    path: '/appointments/new',
    component: () => import('pages/NewAppointmentPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointments/:id/edit',
    name: 'EditAppointment',
    component: () => import('src/pages/EditAppointment.vue'),
    props: true
  },
  {
    path: "/invoice/:id",
    component: () => import("components/InvoiceDetail.vue"),
    props: true
  }

];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation Guard für Auth-Handling
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token');

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/');
  } else if (to.meta.guestOnly && isAuthenticated) {
    next('/landing');
  } else {
    next();
  }
});

export default routes;
