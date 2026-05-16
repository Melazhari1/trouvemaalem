<template>
  <div class="map-wrapper h-full">
    <div ref="mapEl" class="map-el h-full w-full"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

// Fix Leaflet default icon paths
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
});

const props = defineProps({
  initialWorkers: { type: Array, default: null },
  userLocation: { type: Object, default: null },
  initialCenter: { type: Array, default: () => [31.7917, -7.0926] },
  initialZoom: { type: Number, default: 6 },
  translations: { type: Object, default: () => ({ km_from_you: 'km away', view_details: 'View Details' }) },
});

import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const workers = ref(props.initialWorkers || []);

const fetchWorkers = async () => {
  if (props.initialWorkers) return;
  try {
    const locale = page.url.split('/')[1] || 'en';
    const response = await axios.get(`/${locale}/api/map-data`);
    workers.value = response.data;
  } catch {
    // map data unavailable — markers simply won't render
  }
};

const mapEl = ref(null);
let map = null;
let markersLayer = null;
let userMarker = null;

const userIcon = L.divIcon({
  html: `<div class="user-pulse"></div>`,
  className: 'user-marker-container',
  iconSize: [24, 24],
  iconAnchor: [12, 12],
});

const createWorkerIcon = (worker) => {
  const ratingRaw = Number(worker.average_rating || worker.rating || 0);
  const ratingHtml = ratingRaw > 0
    ? `<span class="marker-rating">★ ${ratingRaw.toFixed(1)}</span>`
    : `<span class="marker-rating marker-no-rating">—</span>`;
  return L.divIcon({
    html: `
      <div class="premium-marker">
        ${ratingHtml}
        <div class="marker-tip"></div>
      </div>
    `,
    className: 'worker-marker-container',
    iconSize: [60, 32],
    iconAnchor: [30, 32],
    popupAnchor: [0, -32]
  });
};

function haversineDistance(lat1, lng1, lat2, lng2) {
  const R = 6371;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLng = ((lng2 - lng1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLng / 2) *
      Math.sin(dLng / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return Math.round(R * c * 10) / 10;
}

function buildPopup(worker) {
  const rating = Number(worker.average_rating || worker.rating || 0);

  const ratingHtml = rating > 0 ? `
    <div class="popup-rating">
      ${Array.from({ length: 5 }, (_, i) =>
        `<span style="color:${i < Math.round(rating) ? '#d78126' : '#e2e8f0'}; font-size:14px;">★</span>`
      ).join('')}
      <span class="popup-rating-num">${rating.toFixed(1)}</span>
    </div>` : '';

  let distanceHtml = '';
  if (props.userLocation && worker.lat && worker.lng) {
    const dist = haversineDistance(props.userLocation.lat, props.userLocation.lng, worker.lat, worker.lng);
    distanceHtml = `<div class="popup-distance">🚗 ${dist} ${props.translations.km_from_you}</div>`;
  }

  return `
    <div class="premium-popup">
      <div class="popup-header">
        <img src="${worker.image}" alt="${worker.name}" class="popup-img" />
        <div class="popup-info">
          <div class="popup-name">${worker.name}</div>
          <div class="popup-category">${worker.category?.name || ''}</div>
        </div>
      </div>
      ${ratingHtml}
      <div class="popup-location">📍 ${worker.location}</div>
      ${distanceHtml}
      <a href="/artisan/${worker.slug}" class="popup-btn">${props.translations.view_details} →</a>
    </div>
  `;
}

function renderMarkers() {
  if (!map) return;

  if (markersLayer) {
    map.removeLayer(markersLayer);
  }
  
  markersLayer = L.markerClusterGroup({
    showCoverageOnHover: false,
    spiderfyOnMaxZoom: true,
    maxClusterRadius: 40,
    iconCreateFunction: function(cluster) {
      return L.divIcon({
        html: `<div class="premium-cluster">${cluster.getChildCount()}</div>`,
        className: 'cluster-container',
        iconSize: [40, 40]
      });
    }
  }).addTo(map);

  const bounds = [];
  workers.value.forEach(w => {
    if (w.lat && w.lng) {
      const marker = L.marker([w.lat, w.lng], { icon: createWorkerIcon(w) })
        .bindPopup(buildPopup(w), { maxWidth: 300, className: 'leaflet-premium-popup' });
      markersLayer.addLayer(marker);
      bounds.push([w.lat, w.lng]);
    }
  });

  if (userMarker) {
    map.removeLayer(userMarker);
    userMarker = null;
  }
  if (props.userLocation) {
    userMarker = L.marker([props.userLocation.lat, props.userLocation.lng], { icon: userIcon })
      .bindPopup('<div class="user-popup">📍 You are here</div>')
      .addTo(map);
    bounds.push([props.userLocation.lat, props.userLocation.lng]);
  }

  if (bounds.length > 1) {
    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
  } else if (bounds.length === 1) {
    map.setView(bounds[0], 14);
  }
}

onMounted(async () => {
  // Expose L globally so the UMD plugin patches the same instance we use
  window.L = L;
  await import('leaflet.markercluster');

  fetchWorkers();
  nextTick(() => {
    if (!mapEl.value) return;

    map = L.map(mapEl.value, {
      center: props.initialCenter,
      zoom: props.initialZoom,
      scrollWheelZoom: true,
      zoomControl: false, // Custom zoom control position
    });

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; CartoDB',
      subdomains: 'abcd',
      maxZoom: 20
    }).addTo(map);

    renderMarkers();
  });
});

watch(
  () => [workers.value, props.userLocation, props.initialWorkers],
  () => { 
    if (props.initialWorkers) {
      workers.value = props.initialWorkers;
    }
    renderMarkers(); 
  },
  { deep: true }
);
</script>

<style>
@reference "../../css/app.css";

/* Map & Containers */
.map-wrapper {
  position: relative;
  width: 100%;
}

/* Premium Marker Styles */
.premium-marker {
  @apply bg-white border-2 border-brand-blue rounded-full px-3 py-1 shadow-lg flex items-center justify-center transition-all duration-300;
  width: auto;
  min-width: 60px;
}
.worker-marker-container:hover .premium-marker {
  @apply scale-110 border-brand-orange bg-brand-orange text-white;
}
.marker-rating {
  @apply text-[11px] font-black whitespace-nowrap;
}
.marker-no-rating {
  @apply text-slate-400;
}
.marker-tip {
  @apply absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-r-2 border-b-2 border-brand-blue rotate-45;
}
.worker-marker-container:hover .marker-tip {
  @apply bg-brand-orange border-brand-orange;
}

/* User Marker */
.user-pulse {
  @apply w-4 h-4 bg-blue-500 border-2 border-white rounded-full shadow-lg relative;
}
.user-pulse::after {
  content: '';
  @apply absolute -inset-2 bg-blue-500 rounded-full animate-ping opacity-30;
}

/* Cluster */
.premium-cluster {
  @apply w-10 h-10 bg-brand-blue text-white rounded-full flex items-center justify-center font-black text-sm border-4 border-white shadow-lg;
}

/* Premium Popup */
.leaflet-premium-popup .leaflet-popup-content-wrapper {
  @apply rounded-2xl p-0 overflow-hidden shadow-premium border-none;
}
.leaflet-premium-popup .leaflet-popup-content {
  @apply m-0 p-4;
}
.premium-popup {
  @apply font-display;
}
.popup-header {
  @apply flex items-center gap-3 mb-3;
}
.popup-img {
  @apply w-12 h-12 rounded-xl object-cover border border-slate-100;
}
.popup-name {
  @apply text-brand-blue font-black text-sm;
}
.popup-category {
  @apply text-brand-orange text-[10px] font-black uppercase tracking-widest;
}
.popup-rating {
  @apply flex items-center gap-1 mb-2;
}
.popup-rating-num {
  @apply text-xs font-black text-slate-400 ml-1;
}
.popup-location {
  @apply text-xs text-slate-500 mb-1;
}
.popup-distance {
  @apply text-xs font-black text-green-600 mb-3;
}
.popup-btn {
  @apply block w-full text-center py-2 bg-brand-blue text-white text-xs font-black rounded-lg transition-colors hover:bg-brand-blue-light;
}
</style>
