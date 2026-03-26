async function loadReviews() {
    const hostId = document.body.dataset.hostId;
    try {
        const response = await fetch(`/api/reviews?annonce.utilisateur.id=${hostId}`);
        const data = await response.json();
        const reviewsData = data.member || [];
        console.log(reviewsData);

        if (reviewsData.length === 0) {
            document.getElementById('reviewsGrid').innerHTML = '<p>Aucun avis n\'a été publié pour cet hôte.</p>';
            document.getElementById('averageRating').textContent = '0';
            document.getElementById('totalReviews').textContent = '0 avis reçus';
            document.getElementById('reviewCount').textContent = '0 commentaires';
            renderStars(0);
            return;
        }

        let totalNote = 0;

        reviewsData.forEach(review => {
            totalNote += review.note;
        });

        const averageRating = (totalNote / reviewsData.length).toFixed(1);
        const totalReviews = reviewsData.length;

        document.getElementById('averageRating').textContent = averageRating;
        document.getElementById('totalReviews').textContent = `${totalReviews} avis reçus`;
        document.getElementById('reviewCount').textContent = `${totalReviews} commentaires`;
        renderStars(Math.round(averageRating));

        const reviewsGrid = document.getElementById('reviewsGrid');
        reviewsGrid.innerHTML = '';

        reviewsData.forEach(review => {
            const reviewer = review.utilisateur;
            const avatarUrl = `https://ui-avatars.com/api/?name=${reviewer.prenom}&background=random&color=fff`;

            const starsHtml = generateStars(review.note);
            const date = new Date(review.dateCreation).toLocaleDateString('fr-FR');

            const reviewHtml = `
                <div class="review-item">
                    <div class="review-head">
                        <img src="${avatarUrl}" class="review-avatar">
                        <div class="head-right">
                            <div class="date-name">
                                <h4 class="review-author">${reviewer.prenom} ${reviewer.nom}</h4>
                                <div class="review-date">${date}</div>
                            </div>
                            <span class="review-stars-mini">${starsHtml}</span>
                        </div>
                    </div>
                    <div class="review-body">
                        ${review.commentaire}
                    </div>
                </div>
            `;
            reviewsGrid.innerHTML += reviewHtml;
        });

    } catch (error) {
        document.getElementById('reviewsGrid').innerHTML = '<p>Erreur lors du chargement des avis.</p>';
    }
}

function generateStars(note) {
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= note) {
            starsHtml += `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#FFB400"><path d="m6.87 14.33-1.83 6.4c-.12.4.03.84.37 1.08.34.25.8.26 1.14.02L12 18.2l5.45 3.63a.988.988 0 0 0 1.14-.02c.34-.25.49-.68.37-1.08l-1.83-6.4 4.54-4.08c.3-.27.41-.69.28-1.06-.13-.38-.47-.64-.87-.68l-5.7-.45-2.47-5.46a.998.998 0 0 0-1.82 0L8.62 8.06l-5.7.45c-.4.03-.74.3-.87.68s-.02.8.28 1.06z"></path></svg>`;
        } else {
            starsHtml += `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#D1D5DB" viewBox="0 0 24 24"><path d="m4.83 12.49 2.04 1.83-.83 2.9-1 3.5c-.12.4.03.84.37 1.08.34.25.8.26 1.14.02l3-2L12 18.19l2.45 1.63 3 2a.988.988 0 0 0 1.14-.02c.34-.25.49-.68.37-1.08l-1-3.5-.83-2.9 2.04-1.83 2.5-2.25c.3-.27.41-.69.28-1.06-.13-.38-.47-.64-.87-.68l-3.15-.25-2.56-.2-2.47-5.46a.998.998 0 0 0-1.82 0L8.61 8.05l-2.56.2-3.15.25c-.4.03-.74.3-.87.68s-.02.8.28 1.06l2.5 2.25Zm1.39-2.25 2.52-.2.62-.05.59-.05.84-1.86 1.2-2.66 1.2 2.66.84 1.86.59.05.62.05 2.52.2.83.07-.77.69-2.5 2.25-.46.42.17.6 1.25 4.38-3.74-2.49-.55-.37-.55.37-3.74 2.49 1.25.17-.60-.46-.42-4.38L6.16 11l-.77-.69z"></path></svg>`;
        }
    }
    return starsHtml;
}

function renderStars(rating) {
    const container = document.getElementById('ratingStars');

    for (let i = 1; i <= 5; i++) {
        const color = i <= rating ? '#FFB400' : '#D1D5DB';

        const svgHtml = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="${color}"><path d="m6.87 14.33-1.83 6.4c-.12.4.03.84.37 1.08.34.25.8.26 1.14.02L12 18.2l5.45 3.63a.988.988 0 0 0 1.14-.02c.34-.25.49-.68.37-1.08l-1.83-6.4 4.54-4.08c.3-.27.41-.69.28-1.06-.13-.38-.47-.64-.87-.68l-5.7-.45-2.47-5.46a.998.998 0 0 0-1.82 0L8.62 8.06l-5.7.45c-.4.03-.74.3-.87.68s-.02.8.28 1.06z"></path></svg>`;

        container.insertAdjacentHTML('beforeend', svgHtml);
    }
}

loadReviews();
