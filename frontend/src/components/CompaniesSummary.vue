<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getCompanies, type Company } from '@/services/companies'

const loading = ref(false)
const error = ref<string | null>(null)
const companies = ref<Company[]>([])

async function load() {
  loading.value = true
  error.value = null
  try {
    companies.value = await getCompanies()
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Failed to load companies'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div v-if="loading">Loading…</div>
  <div v-else>
    <div v-if="error" role="alert">{{ error }}</div>
    <ul v-else data-testid="companies-summary-list">
      <li v-for="c in companies" :key="c.id">
        {{ c.name
        }}<span v-if="c.employee_count !== undefined">
          - {{ c.employee_count }} employees</span
        >
      </li>
    </ul>
  </div>
</template>
