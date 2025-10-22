<script setup lang="ts">
import CompaniesSummary from '@/components/CompaniesSummary.vue'

</script>

<template>
  <section>
    <div>
      <h2>Companies</h2>

      <CompaniesSummary />

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
