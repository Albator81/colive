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

        if (locationParam) {
            apiParams.append('ville', locationParam);
        }

        if (typeParam && typeParam !== 'all') {
            apiParams.append('type', typeParam);
        }

        if (dateStart) {
            apiParams.append('disponibilite_debut[after]', dateStart);
        }

        if (dateEnd) {
            apiParams.append('disponibilite_fin[before]', dateEnd);
        }

        if (prixMin) {
            apiParams.append('prix[gte]', prixMin);
        }

        if (prixMax) {
            apiParams.append('prix[lte]', prixMax);
        }

        if (surface) {
            apiParams.append('surface[gte]', surface);
        }

        if (equipement) {
            apiParams.append('equipment', equipement);
        }

        const apiURL = `/api/announces?${apiParams.toString()}`;
        const response = await fetch(apiURL);
        const data = await response.json();
        const announceData = data.member || [];
        const userId = document.body.dataset.userId || null;

        const announceGrid = document.getElementById('grid-announces');
        announceGrid.innerHTML = '';

        if (announceData.length === 0) {
            announceGrid.innerHTML = '<p>Aucune annonce n\'a été trouvée.</p>';
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

            const announceHtml = `
                <article class="announce-card" onclick="window.location.href='/announce/${announce.id}'">
                    <button class="card-like-btn ${isLiked ? 'liked' : ''}" 
                            onclick="toggleLike(event, ${announce.id})">
                        <i class="bi ${isLiked ? 'bi-heart-fill' : 'bi-heart'}"></i>
                    </button>

                    <img src="${photoUrl}" class="card-img" alt="${announce.titre}">

                    <div class="card-content">
                        <h2 class="card-title">${announce.titre}</h2>
                        <p class="card-location"><i class="bi bi-geo-alt-fill"></i> ${announce.ville}</p>
                        <p class="card-date">${start} - ${end}</p>
                        <div class="card-price">${announce.prix}€ <small>/mois</small></div>
                    </div>
                </article>
            `;

            announceGrid.innerHTML += announceHtml;
        });
    } catch (error) {
        console.log(error);
        document.getElementById('grid-announces').innerHTML = '<p>Erreur lors du chargement des annonces.</p>';
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

    const loginUrl = document.body.dataset.loginUrl;
    if (!loginUrl) {
        window.location.href = "/login";
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

            if (data.isLiked) {
                btn.classList.add('liked');
                btn.classList.add('animate');

                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');

                setTimeout(() => btn.classList.remove('animate'), 400);
            } else {
                btn.classList.remove('liked');
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
            }
        }
    } catch (error) {
        console.error('Erreur', error);
    }
}
