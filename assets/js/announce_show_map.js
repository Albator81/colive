document.addEventListener('DOMContentLoaded', () => {
    const mapContainer = document.getElementById('map');
    const dataEl = document.getElementById('announce-data');
    if (!mapContainer || !dataEl) return;
    const lat = parseFloat(dataEl.dataset.lat);
    const lng = parseFloat(dataEl.dataset.lng);
    const fullAddress = dataEl.dataset.address;
    if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) return;
    const map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        minZoom: 10,
        maxZoom: 18,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    const marker = L.marker([lat, lng]).addTo(map)
        .bindPopup(fullAddress);
    marker.openPopup();
    marker.on('click', function(e) {
        map.setView(e.latlng, map.getZoom() < 15 ? 15 : map.getZoom(), { animate: true });
    });
    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});