function sendMessage() {
    let input = document.getElementById('userInput');
    let text = input.value.trim();
    let chatBox = document.getElementById('chatBox');

    if(text === "") return;
    chatBox.innerHTML += `
    <div class="d-flex justify-content-end align-items-center">
        <div class="d-flex flex-column align-items-end w-75">
            <div class="p-3 rounded-4 shadow-sm bg-dark text-white rounded-bottom-end-0">
                <p class="mb-0">${text}</p>
            </div>
        </div>
    </div>
    `;

    chatBox.scrollTop = chatBox.scrollHeight;
    input.value = "";

    let loadingId = "loading-" + Date.now();
    chatBox.innerHTML += `
    <div class="d-flex justify-content-start gap-2 align-items-center" id="${loadingId}">
        <div class="bg-light text-dark rounded-circle p-2 lh-1 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width:40px;height:40px;">
            <i class="bi bi-robot fs-5"></i>
        </div>
        <div class="d-flex flex-column align-items-start w-75">
            <div class="p-3 rounded-4 shadow-sm bg-white border rounded-bottom-start-0">
                <p class="mb-0 text-muted">Écrit...</p>
            </div>
        </div>
    </div>
    `;
    chatBox.scrollTop = chatBox.scrollHeight;

    fetch(document.body.dataset.botAskUrl, {
        method: "POST",
        body: JSON.stringify({ message: text }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById(loadingId).remove();

        chatBox.innerHTML += `
        <div class="d-flex justify-content-start gap-2 align-items-center">
            <div class="bg-light text-dark rounded-circle p-2 lh-1 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width:40px;height:40px;">
                <i class="bi bi-robot fs-5"></i>
            </div>
            <div class="d-flex flex-column align-items-start w-75">
                <div class="p-3 rounded-4 shadow-sm bg-white border rounded-bottom-start-0">
                    <p class="mb-0">${data.response}</p>
                </div>
            </div>
        </div>
        `;
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(error => {
        document.getElementById(loadingId).remove();
        alert("Erreur de communication avec le bot.");
    });
}

document.getElementById('userInput').addEventListener("keypress", function(event) {
    if (event.key === "Enter") sendMessage();
});
