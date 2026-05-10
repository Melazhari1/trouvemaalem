<template>
  <div class="map-container" :style="{ height: height, width: '100%' }">
    <l-map ref="map" v-model:zoom="zoom" :center="[lat, lng]">
      <l-tile-layer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        layer-type="base"
        name="OpenStreetMap"
        attribution="&copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors"
      ></l-tile-layer>
      <l-marker :lat-lng="[lat, lng]">
        <l-tooltip>{{ popupText }}</l-tooltip>
      </l-marker>
    </l-map>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import 'leaflet/dist/leaflet.css';
import { LMap, LTileLayer, LMarker, LTooltip } from '@vue-leaflet/vue-leaflet';

const props = defineProps({
  lat: {
    type: Number,
    required: true,
  },
  lng: {
    type: Number,
    required: true,
  },
  popupText: {
    type: String,
    default: 'Artisan Location',
  },
  height: {
    type: String,
    default: '400px',
  },
});

const zoom = ref(13);
</script>

<style scoped>
.map-container {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
