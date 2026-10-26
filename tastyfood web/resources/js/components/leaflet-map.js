import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix Leaflet default marker icon path issue with Vite
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

export function initRestaurantMap(elementId, lat, lng, options = {}) {
    const defaultOptions = {
        zoom: 16,
        scrollWheelZoom: false,
        ...options
    };

    const map = L.map(elementId).setView([lat, lng], defaultOptions.zoom);
    
    // OpenStreetMap tiles with proper attribution
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    if (defaultOptions.scrollWheelZoom === false) {
        map.scrollWheelZoom.disable();
    }

    return map;
}

export function addRestaurantMarker(map, lat, lng, popupContent) {
    const marker = L.marker([lat, lng]).addTo(map);
    
    if (popupContent) {
        marker.bindPopup(popupContent);
    }
    
    return marker;
}

export function addDeliveryMarker(map, lat, lng, popupContent) {
    const icon = L.icon({
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        className: 'delivery-marker'
    });
    
    const marker = L.marker([lat, lng], { icon, draggable: false }).addTo(map);
    
    if (popupContent) {
        marker.bindPopup(popupContent);
    }
    
    return marker;
}

export function fitBounds(map, markers) {
    if (markers.length > 0) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

export function drawRoute(map, start, end, color = '#3b82f6') {
    // Simple straight line for now
    // In production, use a routing service like OSRM or Mapbox
    const polyline = L.polyline([start, end], {
        color: color,
        weight: 4,
        opacity: 0.7
    }).addTo(map);
    
    return polyline;
}

export function calculateDistance(lat1, lng1, lat2, lng2) {
    // Haversine formula
    const R = 6371; // Earth radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng / 2) * Math.sin(dLng / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;
    
    return distance; // in km
}
