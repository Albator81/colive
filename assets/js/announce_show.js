document.addEventListener('DOMContentLoaded', () => {
    // 1. Récupération des données depuis le DOM
    const dataEl = document.getElementById('announce-data');
    if (!dataEl) return;

    const galleryImages = JSON.parse(dataEl.dataset.photos || '[]');
    const announceId = dataEl.dataset.announceId;
    const loginUrl = dataEl.dataset.loginUrl;
    const isLoggedIn = dataEl.dataset.isLoggedIn === 'true';

    // 2. Gestion de l'image de couverture
    let currentHeroIndex = 0;
    const mainHeroImg = document.getElementById('main-hero-img');
    const desktopCounter = document.getElementById('desktop-photo-count');

    window.moveDesktopCarousel = function(direction, event) {
        if (event) event.stopPropagation();
        currentHeroIndex = (currentHeroIndex + direction + galleryImages.length) % galleryImages.length;
        if (mainHeroImg) mainHeroImg.src = galleryImages[currentHeroIndex];
        if (desktopCounter) desktopCounter.innerText = (currentHeroIndex + 1) + " / " + galleryImages.length;
    };

    window.handleHeroClick = function() {
        openLightbox(currentHeroIndex);
    };

    // 3. Gestion de la Lightbox (Plein écran)
    let currentImageIndex = 0;
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const counter = document.getElementById('lightbox-counter');

    window.openLightbox = function(index) {
        currentImageIndex = index;
        updateLightbox();
        // Remplacement par les classes Bootstrap pures
        lightbox.classList.remove('d-none');
        lightbox.classList.add('d-flex');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function() {
        lightbox.classList.remove('d-flex');
        lightbox.classList.add('d-none');
        document.body.style.overflow = 'auto';
    };

    window.nextImage = function(event) {
        if (event) event.stopPropagation();
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        updateLightbox();
    };

    window.prevImage = function(event) {
        if (event) event.stopPropagation();
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        updateLightbox();
    };

    function updateLightbox() {
        if (lightboxImg) lightboxImg.src = galleryImages[currentImageIndex];
        if (counter) counter.innerText = (currentImageIndex + 1) + " / " + galleryImages.length;
    }

    // 4. Gestion du Like (Favoris)
    window.toggleLike = async function(event, id) {
        event.stopPropagation();
        event.preventDefault();
        
        if (!isLoggedIn) {
            window.location.href = loginUrl;
            return;
        }

        const btn = event.currentTarget;
        const icon = btn.querySelector('i');

        try {
            const response = await fetch('/announce/' + id + '/like', { method: 'POST' });
            
            if (response.ok) {
                const data = await response.json();
                
                // Animation de clic
                icon.style.transform = "scale(1.3)";

                if (data.isLiked) {
                    icon.classList.remove('bi-heart', 'text-secondary');
                    icon.classList.add('bi-heart-fill', 'text-danger');
                } else {
                    icon.classList.remove('bi-heart-fill', 'text-danger');
                    icon.classList.add('bi-heart', 'text-secondary');
                }

                // Fin d'animation
                setTimeout(() => icon.style.transform = "scale(1)", 200);
            }
        } catch (error) { 
            console.error("Erreur toggleLike:", error); 
        }
    };

    // 5. Swipe tactile pour la vue plein écran (Mobile)
    let touchStartX = 0;
    let touchEndX = 0;
    if (lightbox) {
        lightbox.addEventListener('touchstart', e => touchStartX = e.changedTouches[0].screenX);
        lightbox.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchEndX < touchStartX - 50) nextImage();
            if (touchEndX > touchStartX + 50) prevImage();
        });
    }

    // Navigation Clavier
    document.addEventListener('keydown', function(event) {
        if (!lightbox || lightbox.classList.contains('d-none')) return;
        if (event.key === "Escape") closeLightbox();
        if (event.key === "ArrowRight") nextImage();
        if (event.key === "ArrowLeft") prevImage();
    });

    const reviewForm = document.getElementById('review-form');

    if (reviewForm) {
        reviewForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Empêche le rechargement classique de la page

            const url = this.dataset.url;
            const note = document.getElementById('review-note').value;
            const commentaire = document.getElementById('review-comment').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const errorBox = document.getElementById('review-error');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Envoi...';
            errorBox.classList.add('d-none');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        note: note,
                        commentaire: commentaire
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.location.reload();
                } else {
                    errorBox.innerText = data.error || "Une erreur est survenue.";
                    errorBox.classList.remove('d-none');
                    submitBtn.disabled = false;
                    submitBtn.innerText = "Publier l'avis";
                }
            } catch (error) {
                console.error("Erreur Fetch:", error);
                errorBox.innerText = "Erreur de connexion au serveur.";
                errorBox.classList.remove('d-none');
                submitBtn.disabled = false;
                submitBtn.innerText = "Publier l'avis";
            }
        });
    }
});
