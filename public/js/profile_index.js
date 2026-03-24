function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) { tabcontent[i].classList.remove("active"); }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", " inactive"); }
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.className = evt.currentTarget.className.replace(" inactive", " active");
}

async function toggleLike(event, announceId) {
    event.stopPropagation();
    event.preventDefault();
    const btn = event.currentTarget;
    const icon = btn.querySelector('i');
    try {
        const response = await fetch('/announce/' + announceId + '/like', { method: 'POST' });
        if (response.ok) {
            const data = await response.json();
            if (data.isLiked) {
                btn.classList.add('liked', 'animate');
                icon.classList.remove('bi-heart'); icon.classList.add('bi-heart-fill');
                setTimeout(() => btn.classList.remove('animate'), 400);
            } else {
                btn.classList.remove('liked');
                icon.classList.remove('bi-heart-fill'); icon.classList.add('bi-heart');
            }
        }
    } catch (error) { console.error('Erreur', error); }
}
