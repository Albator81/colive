document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('announce_images');
    const previewContainer = document.getElementById('preview-container');

    let filesArray = [];

    dropZone.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-image')) return;
        fileInput.click();
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const newFiles = Array.from(dt.files);
        addFiles(newFiles);
    });

    fileInput.addEventListener('change', function() {
        const newFiles = Array.from(this.files);
        addFiles(newFiles);
    });

    function addFiles(newFiles) {
        filesArray = filesArray.concat(newFiles);
        updateInputFiles();
        renderPreview();
    }

    function removeFile(index) {
        filesArray.splice(index, 1);
        updateInputFiles();
        renderPreview();
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }

    function renderPreview() {
        previewContainer.innerHTML = '';

        const btn = dropZone.querySelector('button');
        const txt = dropZone.querySelector('p');

        if (filesArray.length > 0) {
            btn.style.display = 'none';
            txt.style.display = 'none';
        } else {
            btn.style.display = 'block';
            txt.style.display = 'block';
        }

        filesArray.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.readAsDataURL(file);

                const wrapper = document.createElement('div');
                wrapper.className = 'preview-wrapper';

                const img = document.createElement('img');
                img.className = 'preview-image';

                const removeBtn = document.createElement('button');
                removeBtn.className = 'btn-remove-image';
                removeBtn.innerHTML = '&times;';
                removeBtn.type = 'button';
                removeBtn.onclick = (e) => {
                    e.stopPropagation();
                    removeFile(index);
                };

                reader.onloadend = function() {
                    img.src = reader.result;
                }

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);
            }
        });
    }
});
