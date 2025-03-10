import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from 'pages/LoginPage.vue';
import RegisterPage from 'pages/RegisterPage.vue';
import LandingPage from 'pages/LandingPage.vue';
import ProfilePage from 'pages/ProfilePage.vue';
import AppointmentsPage from 'src/pages/AppointmentsPage.vue';
import ClientsManagment from 'pages/ClientsManagment.vue';
import ClientDetail from 'src/pages/ClientDetail.vue';
import TaxReport from 'src/pages/TaxReport.vue';
import DatevExport from 'src/pages/DatevExport.vue';
import CompanySettings from 'src/pages/CompanySettings.vue';
import ExpensesList from 'src/pages/ExpensesList.vue';

const routes = [
  { path: '/', component: LoginPage, meta: { guestOnly: true } },
  { path: '/register', component: RegisterPage, meta: { guestOnly: true } },
  { path: '/landing', component: LandingPage, meta: { requiresAuth: true } },
  { path: '/profile', component: ProfilePage, meta: { requiresAuth: true } },
  { path: '/appointments', component: AppointmentsPage, meta: { requiresAuth: true } },
  {
    path: '/admin-dashboard', component: () => import('src/pages/AdminDashboard2.vue'), meta: { requiresAuth: true },
    children: [
      { path: "clients", component: ClientsManagment },
      { path: "clients/:id", component: ClientDetail, props: true },
      { path: "tax-report", component: TaxReport },
      { path: "datev-export", component: DatevExport },
      { path: "company-settings", component: CompanySettings },
      { path: "invoice/:id", component: () => import("components/InvoiceDetail.vue"), props: true }, 
      {path:"expenses",component:ExpensesList},
      {
        path: "/expense/:id",
        component: () => import("pages/ExpenseDetails.vue"),
        name: "ExpenseDetails"
      },

    ]
  },
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
    path:"/tasks",
    name:"Tasks",
    component:()=>import('src/components/TaskCard.vue')
  },


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
