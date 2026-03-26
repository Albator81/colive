function deleteExistingPhoto(photoId) {
    if (confirm('Voulez-vous vraiment supprimer cette photo ?')) {
        fetch('/announce/picture/' + photoId + '/delete', {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('photo-' + photoId).remove();
                } else {
                    alert('Erreur lors de la suppression');
                }
            })
            .catch(error => console.error('Erreur:', error));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('announce_images');
    const previewContainer = document.getElementById('preview-container');

    dropZone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function() {
        previewContainer.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
});
