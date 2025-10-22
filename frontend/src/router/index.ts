import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import CompanyView from '@/views/CompanyView.vue'
import EmployeeView from '@/views/EmployeeView.vue'
import CSVImportView from '@/views/CSVImportView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/company',
      name: 'company',
      component: CompanyView,
    },
    {
      path: '/employee',
      name: 'employee',
      component: EmployeeView,
    },
    {
      path: '/csv-import',
      name: 'csv-import',
      component: CSVImportView,
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: { template: '<div>Not Found</div>' },
    },
  ],
})

export default router
