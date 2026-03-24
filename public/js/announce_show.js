const galleryImages = [];
{% if announce.photos is not empty %}
{% for photo in announce.photos %}
galleryImages.push("{{ photo.contenu }}");
{% endfor %}
{% else %}
galleryImages.push("https://picsum.photos/seed/{{ announce.id }}/800/1000");
{% endif %}

let currentHeroIndex = 0;
const mainHeroImg = document.getElementById('main-hero-img');
const desktopCounter = document.getElementById('desktop-photo-count');

function moveDesktopCarousel(direction, event) {
    event.stopPropagation();
    currentHeroIndex = (currentHeroIndex + direction + galleryImages.length) % galleryImages.length;
    mainHeroImg.src = galleryImages[currentHeroIndex];
    if(desktopCounter) desktopCounter.innerText = (currentHeroIndex + 1) + " / " + galleryImages.length;
}

let currentImageIndex = 0;
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const counter = document.getElementById('lightbox-counter');

function handleHeroClick() {
    openLightbox(currentHeroIndex);
}

function openLightbox(index) {
    currentImageIndex = index;
    updateLightbox();
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    lightbox.classList.remove('active');
    document.body.style.overflow = 'auto';
}
function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    updateLightbox();
}
function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightbox();
}
function updateLightbox() {
    lightboxImg.src = galleryImages[currentImageIndex];
    counter.innerText = (currentImageIndex + 1) + " / " + galleryImages.length;
}

async function toggleLike(event, announceId) {
    event.stopPropagation();
    event.preventDefault();
    {% if not app.user %}
    window.location.href = "{{ path('app_login') }}";
    return;
    {% endif %}
    const btn = event.currentTarget;
    const icon = btn.querySelector('i');
    try {
        const response = await fetch('/announce/' + announceId + '/like', { method: 'POST' });
        if (response.ok) {
            const data = await response.json();
            if (data.isLiked) {
                btn.classList.add('liked');
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
            } else {
                btn.classList.remove('liked');
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
            }
        }
    } catch (error) { console.error(error); }
}

let touchStartX = 0;
let touchEndX = 0;
lightbox.addEventListener('touchstart', e => touchStartX = e.changedTouches[0].screenX);
lightbox.addEventListener('touchend', e => {
    touchEndX = e.changedTouches[0].screenX;
    if (touchEndX < touchStartX - 50) nextImage();
    if (touchEndX > touchStartX + 50) prevImage();
});
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") closeLightbox();
    if (event.key === "ArrowRight") nextImage();
    if (event.key === "ArrowLeft") prevImage();
});
