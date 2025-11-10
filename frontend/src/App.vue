<script setup lang="ts">
fetch('http://localhost:8000/api/status', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json'
  }
})
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));

</script>

<template>
  <h1>Simple Test</h1>
</template>

<style scoped></style>
