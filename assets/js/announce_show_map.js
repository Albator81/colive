const map = L.map('map').setView([{{ announce.latitude }}, {{ announce.longitude }}], 15);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    minZoom: 10,
    maxZoom: 18,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
L.marker([{{ announce.latitude }}, {{ announce.longitude }}]).addTo(map)
    .bindPopup("{{announce.adresse}}, {{announce.codepostal}} - {{announce.ville}}")
    .openPopup()
    .on('click', function(e) {
        map.setView(e.latlng, map.getZoom() < 15 ? 15 : map.getZoom() , { animate: true });
    });
