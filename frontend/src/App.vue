<script setup lang="ts">
import { RouterView } from 'vue-router'
</script>

<template>
  <div id="app">
    <header>
      <h1>Multiverse Payroll</h1>
      <nav>
        <RouterLink to="/">Home</RouterLink>
      </nav>
    </header>
    <main>
      <RouterView />
    </main>
  </div>
</template>

<style>
body {
  font-family: system-ui, sans-serif;
  margin: 0;
  padding: 0;
}

header {
  background: #333;
  color: white;
  padding: 1rem;
}

header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
}

nav a {
  color: white;
  margin-right: 1rem;
}

main {
  padding: 2rem;
}
</style>
