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
