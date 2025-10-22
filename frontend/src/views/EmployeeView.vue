<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getEmployees, updateEmployeeEmail, type Employee } from '@/services/employees'

const loading = ref(false)
const error = ref<string | null>(null)
const employees = ref<Employee[]>([])
const editingId = ref<number | null>(null)
const editingEmail = ref('')
const emailError = ref<string | null>(null)

function isValidEmail(value: string): boolean {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
}

async function load() {
  loading.value = true
  error.value = null
  try {
    employees.value = await getEmployees()
  } catch (e: any) {
    error.value = e?.message || 'Failed to load employees'
  } finally {
    loading.value = false
  }
}

async function saveEmail(emp: Employee, email: string) {
  const prev = emp.email
  emp.email = email
  try {
    const updated = await updateEmployeeEmail(emp.id, email)
    emp.email = updated.email
  } catch (e) {
    emp.email = prev
  }
}

async function onEdit(emp: Employee) {
  editingId.value = emp.id
  editingEmail.value = emp.email
  emailError.value = null
}

async function onSave(emp: Employee) {
  emailError.value = null
  if (!isValidEmail(editingEmail.value)) {
    emailError.value = 'Please enter a valid email.'
    return
  }
  const prev = emp.email
  try {
    const updated = await updateEmployeeEmail(emp.id, editingEmail.value)
    emp.email = updated.email
    editingId.value = null
  } catch (e) {
    emp.email = prev
  }
}

onMounted(load)
</script>

<template>
  <section>
    <h2>Employee</h2>
    <div v-if="loading">Loading…</div>
    <div v-else>
      <div v-if="error" role="alert">{{ error }}</div>
      <table v-else data-testid="employees-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Salary</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="e in employees" :key="e.id">
            <td>{{ e.full_name }}</td>
            <td>
              <template v-if="editingId === e.id">
                <input v-model="editingEmail" type="email" aria-label="email-edit-input" />
                <p v-if="editingId === e.id && !isValidEmail(editingEmail)" data-testid="email-error" role="alert">
                  Please enter a valid email.
                </p>
              </template>
              <template v-else>
                <span aria-label="email-text">{{ e.email }}</span>
              </template>
            </td>
            <td>{{ Number(e.salary).toLocaleString() }}</td>
            <td>
              <template v-if="editingId === e.id">
                <button aria-label="save-email" :disabled="!isValidEmail(editingEmail)" @click="onSave(e)">Save</button>
                <button aria-label="cancel-email" @click="editingId = null; emailError = null">Cancel</button>
              </template>
              <template v-else>
                <button aria-label="edit-email" @click="onEdit(e)">Edit</button>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<style scoped>
h2 {
  margin-bottom: 0.5rem;
}

ul {
  margin-top: 1rem;
}

li {
  margin: 0.5rem 0;
}
</style>
