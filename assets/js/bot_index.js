function sendMessage() {
    let input = document.getElementById('userInput');
    let text = input.value.trim();
    let chatBox = document.getElementById('chatBox');

    if(text === "") return;
    chatBox.innerHTML += `
    <div class="message user">
        <div class="bubble">${text}</div>
    </div>
`;

    chatBox.scrollTop = chatBox.scrollHeight;
    input.value = "";

    let loadingId = "loading-" + Date.now();
    chatBox.innerHTML += `
    <div class="message bot" id="${loadingId}">
        <div class="bubble bubble-loading">Écrit...</div>
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
        <div class="message bot">
            <div class="bubble">${data.response}</div>
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
