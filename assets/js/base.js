var audio = new Audio(document.body.dataset.notifSound);
const eventSource = new EventSource(document.body.dataset.mercureUrl);

const listContainer = document.getElementById("notif-list-container");
const badge = document.getElementById("notif-badge");
const notifButton = document.getElementById("notifDropdown");
const clearNotifsButton = document.getElementById("notifClear");

let unreadCount = parseInt(badge.innerText) || 0;

eventSource.onmessage = event => {
    audio.play();
    
    const data = JSON.parse(event.data);
    console.log(data);

    unreadCount++;
    badge.innerText = unreadCount;
    badge.classList.remove('d-none');

    const notifHtml = `
        <li>
            <a class="dropdown-item notif-item notif-new px-3 py-2 d-flex flex-column" href="${data.target}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-primary notif-title"><strong>*</strong> ${data.title}</strong>
                    <span class="text-muted small notif-timestamp">${data.timestamp}</span>
                </div>
                <span class="text-muted small">${data.content}</span>
            </a>
        </li>`;
    
    listContainer.insertAdjacentHTML('afterbegin', notifHtml);
};

notifButton.addEventListener('click', () => {
    if (unreadCount > 0) {
        unreadCount = 0;
        badge.innerText = '0';
        badge.classList.add('d-none');
        
        fetch(document.body.dataset.notifSeeUrl);
    }
});

clearNotifsButton.addEventListener('click', (e) => {
    e.stopPropagation();

    if (listContainer.querySelector('.notif-item') !== null) {
        listContainer.innerHTML = `
            <li id="empty-notif-msg" class="text-center text-muted py-3">
                <small>Aucune nouvelle notification</small>
            </li>
        `;
        unreadCount = 0;
        badge.innerText = '0';
        badge.classList.add('d-none');

        fetch(document.body.dataset.notifClearUrl);
    }
});
