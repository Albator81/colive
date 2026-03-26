const gridEl = document.getElementById('grid-announces');
const userId = gridEl.dataset.userId ? parseInt(gridEl.dataset.userId) : null;
const loginUrl = gridEl.dataset.loginUrl;

async function createCards() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const apiParams = new URLSearchParams();

        const locationParam = urlParams.get('location');
        const typeParam = urlParams.get('type');
        const dateStart = urlParams.get('date_start');
        const dateEnd = urlParams.get('date_end');
        const prixMin = urlParams.get('prix_min');
        const prixMax = urlParams.get('prix_max');
        const surface = urlParams.get('surface');
        const equipement = urlParams.get('equipement');

        if (locationParam) apiParams.append('ville', locationParam);
        if (typeParam && typeParam !== 'all') apiParams.append('type', typeParam);
        if (dateStart) apiParams.append('disponibilite_debut[after]', dateStart);
        if (dateEnd) apiParams.append('disponibilite_fin[before]', dateEnd);
        if (prixMin) apiParams.append('prix[gte]', prixMin);
        if (prixMax) apiParams.append('prix[lte]', prixMax);
        if (surface) apiParams.append('surface[gte]', surface);
        if (equipement) apiParams.append('equipment', equipement);

        const apiURL = `/api/announces?${apiParams.toString()}`;
        const response = await fetch(apiURL);
        const data = await response.json();
        const announceData = data.member || [];

        gridEl.className = 'row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4';
        gridEl.innerHTML = '';

        if (announceData.length === 0) {
            gridEl.innerHTML = '<div class="col-12"><p class="text-center text-muted">Aucune annonce n\'a été trouvée.</p></div>';
            return;
        }

        announceData.forEach(announce => {
            let isLiked = false;
            announce.likes.forEach(like => {
                if (like.utilisateur.id == userId) {
                    isLiked = true;
                }
            });

            const photoUrl = (announce.photos && announce.photos.length > 0) ? announce.photos[0].contenu : `https://picsum.photos/seed/${announce.id}/400/600`;

            const start = new Date(announce.disponibilite_debut).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
            const end = new Date(announce.disponibilite_fin).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });

            const heartClass = isLiked ? 'bi-heart-fill text-danger' : 'bi-heart text-white';

            const announceHtml = `
                <div class="col">
                    <article class="card h-100 shadow-sm border-0 position-relative text-decoration-none" 
                             style="cursor: pointer; transition: transform 0.2s;" 
                             onmouseover="this.classList.add('shadow'); this.style.transform='translateY(-5px)'"
                             onmouseout="this.classList.remove('shadow'); this.style.transform='translateY(0)'"
                             onclick="window.location.href='/announce/${announce.id}'">
                        
                        <!-- Suppression de la classe .btn et ajout de p-0 d-inline-flex pour réduire la hitbox au strict minimum -->
                        <button class="position-absolute top-0 end-0 m-3 p-0 border-0 bg-transparent d-inline-flex" 
                                style="z-index: 2;"
                                onclick="toggleLike(event, ${announce.id})">
                            <i class="bi ${heartClass} fs-4" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5)); transition: transform 0.2s ease-in-out;"></i>
                        </button>

                        <img src="${photoUrl}" class="card-img-top object-fit-cover" alt="${announce.titre}" style="height: 200px;">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate fw-bold mb-2">${announce.titre}</h5>
                            
                            <p class="card-text text-muted mb-1 small">
                                <i class="bi bi-geo-alt-fill text-primary"></i> ${announce.ville}
                            </p>
                            
                            <p class="card-text text-muted mb-3 small">
                                <i class="bi bi-calendar-event"></i> ${start} - ${end}
                            </p>
                            
                            <div class="mt-auto">
                                <span class="fs-5 fw-bold text-dark">${announce.prix}€</span> 
                                <small class="text-muted">/mois</small>
                            </div>
                        </div>
                    </article>
                </div>
            `;

            gridEl.innerHTML += announceHtml;
        });
    } catch (error) {
        console.log(error);
        document.getElementById('grid-announces').innerHTML = '<div class="col-12"><p class="text-danger text-center">Erreur lors du chargement des annonces.</p></div>';
    }
}

createCards();

function toggleMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
}

async function toggleLike(event, announceId) {
    event.stopPropagation();
    event.preventDefault();

    if (!userId) {
        window.location.href = loginUrl;
        return;
    }

    const btn = event.currentTarget;
    const icon = btn.querySelector('i');

    try {
        const response = await fetch('/announce/' + announceId + '/like', {
            method: 'POST'
        });

        if (response.ok) {
            const data = await response.json();

            // Animation d'agrandissement
            icon.style.transform = "scale(1.3)";

            if (data.isLiked) {
                // Rempli et rouge
                icon.classList.remove('bi-heart', 'text-white');
                icon.classList.add('bi-heart-fill', 'text-danger');
            } else {
                // Vide et blanc
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart', 'text-white');
            }

            // Fin de l'animation
            setTimeout(() => icon.style.transform = "scale(1)", 200);
        }
    } catch (error) {
        console.error('Erreur', error);
    }
}