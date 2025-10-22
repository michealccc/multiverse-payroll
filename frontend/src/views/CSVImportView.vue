<script setup lang="ts">
import { ref } from 'vue'
import { uploadCsv, type CsvUploadResponse } from '@/services/csv'

const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)
const csvContent = ref<string>('')
const isUploading = ref(false)
const uploadResult = ref<CsvUploadResponse | null>(null)
const error = ref<string>('')

function handleFileSelect(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (file) {
    selectedFile.value = file
    uploadResult.value = null
    error.value = ''

    const reader = new FileReader()
    reader.onload = (e) => {
      csvContent.value = e.target?.result as string
    }
    reader.onerror = () => {
      error.value = 'Failed to read file'
    }
    reader.readAsText(file)
  }
}

async function handleUpload() {
  if (!csvContent.value) {
    error.value = 'Please select a CSV file first'
    return
  }

  isUploading.value = true
  error.value = ''
  uploadResult.value = null

  try {
    const result = await uploadCsv(csvContent.value)
    uploadResult.value = result
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Upload failed'
  } finally {
    isUploading.value = false
  }
}

function resetForm() {
  selectedFile.value = null
  csvContent.value = ''
  uploadResult.value = null
  error.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}
</script>

<template>
  <div>
    <h2>CSV Import</h2>
    <p>Upload a CSV file to import employees and companies.</p>

    <div class="upload-section">
      <h3>Required CSV Format:</h3>
      <pre class="csv-format">
Company Name,Employee Name,Email Address,Salary
ACME Corporation,John Doe,johndoe@acme.com,50000
ACME Corporation,Jane Doe,janedoe@acme.com,55000</pre
      >
      <p>Please ensure the CSV file follows the above format.</p>

      <div class="file-input-wrapper">
        <input
          ref="fileInput"
          type="file"
          accept=".csv"
          @change="handleFileSelect"
          :disabled="isUploading"
        />
        <button @click="handleUpload" :disabled="!selectedFile || isUploading">
          {{ isUploading ? "Uploading..." : "Upload CSV" }}
        </button>
        <button
          v-if="selectedFile || uploadResult"
          @click="resetForm"
          :disabled="isUploading"
        >
          Reset
        </button>
      </div>

      <p v-if="selectedFile" class="file-info">Selected: {{ selectedFile.name }}</p>
    </div>

    <div v-if="error" class="error-message"><strong>Error:</strong> {{ error }}</div>

    <div v-if="uploadResult" class="result-section">
      <h3>Upload Results</h3>
      <div class="stats">
        <div class="stat-item">
          <strong>Total Rows:</strong> {{ uploadResult.total_rows }}
        </div>
        <div class="stat-item success">
          <strong>Employees Imported:</strong> {{ uploadResult.employees_imported }}
        </div>
        <div class="stat-item">
          <strong>Companies Created:</strong> {{ uploadResult.companies_created }}
        </div>
        <div v-if="uploadResult.employees_failed > 0" class="stat-item failed">
          <strong>Employees Failed:</strong> {{ uploadResult.employees_failed }}
        </div>
      </div>

      <div
        v-if="uploadResult.errors && uploadResult.errors.length > 0"
        class="errors-list"
      >
        <h4>Validation Errors:</h4>
        <ul>
          <li v-for="(err, index) in uploadResult.errors" :key="index" class="error-item">
            {{ err }}
          </li>
        </ul>
      </div>

      <div v-else class="success-message">All employees imported successfully!</div>
    </div>
  </div>
</template>

<style scoped>
h2 {
  margin-bottom: 0.5rem;
}

button {
  margin-right: 0.5rem;
}
</style>
