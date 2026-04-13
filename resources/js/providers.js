// Fichier JavaScript spécifique pour les prestataires
import '@css/providers.css';

// Import des utilitaires
import { initNotifications } from '@utils/notifications';
import { initRealtime } from '@utils/realtime';
import { initForms } from '@utils/forms';

// Initialisation des modules pour prestataires
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser les notifications temps réel
    initNotifications();
    
    // Initialiser la connexion WebSocket
    initRealtime();
    
    // Initialiser les formulaires avancés
    initForms();
    
    // Initialiser les fonctionnalités spécifiques aux prestataires
    initProviderFeatures();
});

function initProviderFeatures() {
    // Gestion du statut du compte
    initAccountStatus();
    
    // Gestion des assignations
    initAssignments();
    
    // Gestion des réservations
    initBookings();
    
    // Gestion de la galerie média
    initMediaGallery();
}

function initAccountStatus() {
    const statusElement = document.querySelector('[data-provider-status]');
    if (statusElement) {
        const status = statusElement.dataset.providerStatus;
        updateStatusDisplay(status);
    }
}

function updateStatusDisplay(status) {
    const indicators = document.querySelectorAll('.status-indicator');
    indicators.forEach(indicator => {
        indicator.classList.remove('active', 'pending', 'rejected');
        indicator.classList.add(status);
    });
}

function initAssignments() {
    // Gestion des actions sur les assignations
    document.querySelectorAll('[data-assignment-action]').forEach(button => {
        button.addEventListener('click', handleAssignmentAction);
    });
}

function handleAssignmentAction(event) {
    const button = event.currentTarget;
    const action = button.dataset.assignmentAction;
    const assignmentId = button.dataset.assignmentId;
    
    if (confirm(`Êtes-vous sûr de vouloir ${action} cette assignation ?`)) {
        submitAssignmentAction(assignmentId, action);
    }
}

async function submitAssignmentAction(assignmentId, action) {
    try {
        const response = await fetch(`/api/assignments/${assignmentId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Action failed');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Une erreur est survenue. Veuillez réessayer.');
    }
}

function initBookings() {
    // Gestion des actions sur les réservations
    document.querySelectorAll('[data-booking-action]').forEach(button => {
        button.addEventListener('click', handleBookingAction);
    });
}

function handleBookingAction(event) {
    const button = event.currentTarget;
    const action = button.dataset.bookingAction;
    const bookingId = button.dataset.bookingId;
    
    if (action === 'mark_done') {
        if (confirm('Marquer cette réservation comme terminée ?')) {
            submitBookingAction(bookingId, action);
        }
    } else if (action === 'cancel') {
        if (confirm('Annuler cette réservation ?')) {
            submitBookingAction(bookingId, action);
        }
    }
}

async function submitBookingAction(bookingId, action) {
    try {
        const response = await fetch(`/api/bookings/${bookingId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        
        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Action failed');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Une erreur est survenue. Veuillez réessayer.');
    }
}

function initMediaGallery() {
    // Gestion de la galerie média
    const galleryContainer = document.querySelector('[data-media-gallery]');
    if (galleryContainer) {
        initGalleryUpload();
        initGallerySorting();
    }
}

function initGalleryUpload() {
    const uploadButton = document.querySelector('[data-media-upload]');
    if (uploadButton) {
        uploadButton.addEventListener('click', () => {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*,video/*';
            input.multiple = true;
            input.addEventListener('change', handleMediaUpload);
            input.click();
        });
    }
}

function handleMediaUpload(event) {
    const files = Array.from(event.target.files);
    files.forEach(file => {
        uploadMediaFile(file);
    });
}

async function uploadMediaFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', file.type.startsWith('image/') ? 'image' : 'video');
    
    try {
        const response = await fetch('/api/media/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        });
        
        if (response.ok) {
            const result = await response.json();
            addMediaToGallery(result);
        } else {
            throw new Error('Upload failed');
        }
    } catch (error) {
        console.error('Upload error:', error);
        alert('Erreur lors du téléchargement du fichier.');
    }
}

function addMediaToGallery(mediaData) {
    const gallery = document.querySelector('[data-media-gallery]');
    if (gallery) {
        const mediaElement = createMediaElement(mediaData);
        gallery.appendChild(mediaElement);
    }
}

function createMediaElement(mediaData) {
    const div = document.createElement('div');
    div.className = 'media-item';
    div.dataset.mediaId = mediaData.id;
    
    if (mediaData.type === 'image') {
        div.innerHTML = `
            <img src="${mediaData.url}" alt="${mediaData.title || ''}" class="w-full h-48 object-cover rounded">
            <div class="media-overlay">
                <button class="edit-btn" data-action="edit">Éditer</button>
                <button class="delete-btn" data-action="delete">Supprimer</button>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div class="video-placeholder w-full h-48 bg-gray-200 rounded flex items-center justify-center">
                <span class="text-gray-500">Vidéo</span>
            </div>
            <div class="media-overlay">
                <button class="edit-btn" data-action="edit">Éditer</button>
                <button class="delete-btn" data-action="delete">Supprimer</button>
            </div>
        `;
    }
    
    return div;
}

function initGallerySorting() {
    const gallery = document.querySelector('[data-media-gallery]');
    if (gallery) {
        new Sortable(gallery, {
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: updateMediaOrder,
        });
    }
}

async function updateMediaOrder(event) {
    const mediaIds = Array.from(event.target.children).map(item => item.dataset.mediaId);
    
    try {
        await fetch('/api/media/reorder', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ media_ids: mediaIds }),
        });
    } catch (error) {
        console.error('Reorder error:', error);
    }
}

// Export des fonctions pour utilisation externe
export {
    initProviderFeatures,
    handleAssignmentAction,
    handleBookingAction,
    uploadMediaFile,
};
