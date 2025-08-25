document.addEventListener('DOMContentLoaded', function() {
  // Vérifier qu'on est sur une page avec upload
  const uploadButton = document.getElementById('uploadButton');
  const uploadButtonEmpty = document.getElementById('uploadButtonEmpty');
  const fileInput = document.getElementById('fileInput');

  // Variable pour éviter les doubles clics
  let isFileDialogOpen = false;

  // Si les éléments ne sont pas présents, sortir silencieusement
  if (!fileInput) {
    return;
  }

  // Récupère le token CSRF depuis la balise meta
  const metaTag = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = metaTag ? metaTag.getAttribute('content') : '';

  // Fonction pour ouvrir le sélecteur de fichiers
  function openFileDialog(event) {
    event.preventDefault();
    event.stopPropagation();
    
    if (isFileDialogOpen) return;
    
    isFileDialogOpen = true;
    setTimeout(() => {
      isFileDialogOpen = false;
    }, 1000); // Reset après 1 seconde
    
    fileInput.click();
  }

  // Au clic sur le bouton principal, déclenche le sélecteur de fichiers
  if (uploadButton) {
    uploadButton.addEventListener('click', openFileDialog);
  }

  // Au clic sur le bouton secondaire, déclenche le sélecteur de fichiers
  if (uploadButtonEmpty) {
    uploadButtonEmpty.addEventListener('click', openFileDialog);
  }

  // Lorsque l'utilisateur sélectionne un ou plusieurs fichiers
  fileInput.addEventListener('change', function() {
    const files = fileInput.files;
    if (files.length === 0) return;

    const maxFileSize = 10 * 1024 * 1024 * 1024; // 10GB en octets
    
    // Vérification de la taille des fichiers
    for (let file of files) {
      if (file.size > maxFileSize) {
        alert(`Le fichier "${file.name}" (${(file.size / 1024 / 1024 / 1024).toFixed(2)} GB) dépasse la taille maximale autorisée de 10GB.`);
        fileInput.value = ''; // Reset le champ
        return;
      }
    }
    
    // Afficher le nombre de fichiers sélectionnés
    const fileCount = files.length;
    const plural = fileCount > 1 ? 's' : '';
    
    if (fileCount > 1) {
      const confirmUpload = confirm(`Vous avez sélectionné ${fileCount} vidéo${plural}. Voulez-vous les uploader toutes ?`);
      if (!confirmUpload) {
        fileInput.value = '';
        return;
      }
    }

    // Récupère l'ID de la galerie depuis l'attribut data
    const galleryId = fileInput.getAttribute('data-gallery-id');

    // Créer le pop-up de progression
    createUploadProgressModal(files, galleryId);

    // Uploader les fichiers un par un
    uploadFilesSequentially(Array.from(files), galleryId);
    
    // Reset le champ file
    fileInput.value = '';
  });

  // Fonction pour créer le modal de progression
  function createUploadProgressModal(files, galleryId) {
    // Supprimer un modal existant s'il y en a un
    const existingModal = document.getElementById('uploadProgressModal');
    if (existingModal) {
      existingModal.remove();
    }

    const modal = document.createElement('div');
    modal.id = 'uploadProgressModal';
    modal.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      z-index: 10000;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    `;

    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
      background: white;
      padding: 25px;
      border-radius: 12px;
      width: 500px;
      max-width: 90vw;
      max-height: 70vh;
      overflow-y: auto;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    `;

    const header = document.createElement('div');
    header.style.cssText = `
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    `;

    const title = document.createElement('h3');
    title.textContent = `Upload de ${files.length} vidéo${files.length > 1 ? 's' : ''}`;
    title.style.cssText = `
      margin: 0;
      color: #333;
      font-size: 18px;
    `;

    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '✕';
    closeBtn.style.cssText = `
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #666;
      padding: 5px;
      border-radius: 4px;
    `;
    closeBtn.onclick = () => modal.remove();

    header.appendChild(title);
    header.appendChild(closeBtn);

    const progressList = document.createElement('div');
    progressList.id = 'uploadProgressList';

    // Créer une ligne de progression pour chaque fichier
    Array.from(files).forEach((file, index) => {
      const progressItem = document.createElement('div');
      progressItem.id = `progress-item-${index}`;
      progressItem.style.cssText = `
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
      `;

      progressItem.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
          <span style="font-weight: 500; color: #333; font-size: 14px;">${file.name}</span>
          <span style="font-size: 12px; color: #666;">${formatFileSize(file.size)}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="flex: 1; background: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
            <div id="progress-bar-${index}" style="width: 0%; height: 100%; background: #4CAF50; border-radius: 4px; transition: width 0.3s ease;"></div>
          </div>
          <span id="progress-text-${index}" style="font-size: 12px; color: #666; min-width: 40px;">0%</span>
        </div>
        <div id="status-${index}" style="font-size: 12px; color: #999; margin-top: 5px;">En attente...</div>
      `;

      progressList.appendChild(progressItem);
    });

    modalContent.appendChild(header);
    modalContent.appendChild(progressList);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
  }

  // Fonction pour formater la taille des fichiers
  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  // Fonction pour uploader les fichiers un par un
  async function uploadFilesSequentially(files, galleryId) {
    const uploadButton = document.getElementById('uploadButton');
    if (uploadButton) {
      uploadButton.disabled = true;
      uploadButton.textContent = 'Upload en cours...';
    }

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      const statusElement = document.getElementById(`status-${i}`);
      const progressBar = document.getElementById(`progress-bar-${i}`);
      const progressText = document.getElementById(`progress-text-${i}`);

      try {
        // Mettre à jour le statut
        statusElement.textContent = 'Upload en cours...';
        statusElement.style.color = '#2196F3';

        // Créer FormData pour ce fichier
        const formData = new FormData();
        formData.append('files[]', file);
        formData.append('galleryId', galleryId);
        formData.append('csrf_token', csrfToken);

        // Créer XMLHttpRequest pour avoir la progression
        const xhr = new XMLHttpRequest();

        // Promesse pour gérer l'upload
        const uploadPromise = new Promise((resolve, reject) => {
          xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
              const percentComplete = Math.round((e.loaded / e.total) * 100);
              progressBar.style.width = percentComplete + '%';
              progressText.textContent = percentComplete + '%';
            }
          });

          xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
              try {
                const result = JSON.parse(xhr.responseText);
                if (result.success) {
                  progressBar.style.width = '100%';
                  progressText.textContent = '100%';
                  statusElement.textContent = 'Terminé ✓';
                  statusElement.style.color = '#4CAF50';
                  resolve(result);
                } else {
                  reject(new Error(result.message || 'Erreur inconnue'));
                }
              } catch (e) {
                reject(new Error('Erreur de parsing JSON'));
              }
            } else {
              reject(new Error(`Erreur HTTP: ${xhr.status}`));
            }
          });

          xhr.addEventListener('error', () => {
            reject(new Error('Erreur réseau'));
          });
        });

        xhr.open('POST', `/gallery/upload/${galleryId}`);
        xhr.send(formData);

        await uploadPromise;

      } catch (error) {
        console.error('Erreur upload:', error);
        statusElement.textContent = `Erreur: ${error.message}`;
        statusElement.style.color = '#f44336';
        progressBar.style.background = '#f44336';
      }
    }

    // Tous les uploads sont terminés
    if (uploadButton) {
      uploadButton.disabled = false;
      uploadButton.textContent = 'Ajouter Vidéo';
    }

    // Attendre 2 secondes puis fermer le modal et recharger
    setTimeout(() => {
      const modal = document.getElementById('uploadProgressModal');
      if (modal) modal.remove();
      window.location.reload();
    }, 2000);
  }
});
