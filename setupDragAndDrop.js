function setupDragAndDrop(dropAreaId, fileInputId, idChantier) {
    let dropArea = document.getElementById(dropAreaId);
    let fileInput = document.getElementById(fileInputId);

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;

        handleFiles(files);
    }

    function handleFiles(files) {
        ([...files]).forEach(file => {
            uploadFile(file, idChantier);
        });
    }

    function uploadFile(file, idChantier) {
        let url = 'upload_images.php';
        let formData = new FormData();

        formData.append('file', file);
        formData.append('idChantier', idChantier);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Upload complete:', data.filePath);
                addImageToGallery(data.filePath, idChantier);
            } else {
                console.error('Upload failed:', data.error);
            }
        })
        .catch(() => {
            console.error('Upload failed');
        });
    }

    function addImageToGallery(imageUrl, idChantier) {
        // Check if image already exists
        if (!document.querySelector(`img[src="${imageUrl}"]`)) {
            let imgContainer = document.createElement('div');
            imgContainer.classList.add('img-container');

            let img = document.createElement('img');
            img.src = imageUrl;
            img.classList.add('gallery-img');

            let deleteBtn = document.createElement('span');
            deleteBtn.classList.add('delete-btn');
            deleteBtn.innerHTML = '&times;';
            deleteBtn.onclick = function() {
                deleteImage(imageUrl, idChantier, imgContainer);
            };

            imgContainer.appendChild(img);
            imgContainer.appendChild(deleteBtn);
            document.getElementById('gallery').appendChild(imgContainer);
            makeImageClickable(img);
        }
    }

    function deleteImage(imageUrl, idChantier, imgContainer) {
        let url = 'delete_image.php';
        let formData = new FormData();
        formData.append('imageUrl', imageUrl);
        formData.append('idChantier', idChantier);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Image deleted:', imageUrl);
                imgContainer.remove();
            } else {
                console.error('Delete failed:', data.error);
            }
        })
        .catch(() => {
            console.error('Delete failed');
        });
    }

    function makeImageClickable(image) {
        image.addEventListener('click', function() {
            openImageOverlay(this.src);
        });
    }

    function setupGalleryClicks() {
        const images = document.querySelectorAll('.gallery-img');
        images.forEach(image => {
            image.addEventListener('click', function() {
                openImageOverlay(this.src);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupGalleryClicks();
    });

    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
}

function openImageOverlay(imageUrl) {
    const overlay = document.getElementById('imageOverlay');
    const overlayImage = document.getElementById('overlayImage');
    const downloadLink = document.getElementById('downloadLink');

    overlayImage.src = imageUrl;
    downloadLink.href = imageUrl; // Set the download link to the image URL

    overlay.style.display = 'flex';
}

function closeImageOverlay() {
    document.getElementById('imageOverlay').style.display = 'none';
}


function printImage() {
    const overlayImage = document.getElementById('overlayImage');
    const printWindow = window.open('');
    printWindow.document.write(`<img src="${overlayImage.src}" onload="window.print(); window.close();">`);
    printWindow.document.close();
}

function shareImage() {
    const overlayImage = document.getElementById('overlayImage').src;
    if (navigator.share) {
        navigator.share({
            title: 'Image de Chantier',
            url: overlayImage
        }).then(() => {
            console.log('Image partagée avec succès');
        }).catch((error) => {
            console.error('Erreur lors du partage', error);
        });
    } else {
        alert('Le partage n\'est pas pris en charge sur ce navigateur.');
    }
}