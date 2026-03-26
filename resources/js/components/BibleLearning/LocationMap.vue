<template>
  <div class="map-view p-6 h-[600px] w-full relative">
    <h2 class="text-xl font-bold mb-4 absolute top-4 left-4 z-10 bg-white p-2 rounded shadow">Bản Đồ Sự Kiện Kinh Thánh</h2>
    <!-- Google Maps Element -->
    <div id="google-map" class="w-full h-full bg-gray-200 rounded">
      Map will be rendered here by Google Maps JS API snippet.
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

// Assume markers data comes from API based on bl_entities (Location)
const markers = [
  { lat: 32.551, lng: 35.353, title: 'Suối Ha-rốt (Ghi-đê-ôn)' },
  { lat: 31.768, lng: 35.213, title: 'Giê-ru-sa-lem' }
];

onMounted(() => {
  // Initialize map when API is loaded
  // const map = new google.maps.Map(document.getElementById('google-map'), { ... });
});
</script>
