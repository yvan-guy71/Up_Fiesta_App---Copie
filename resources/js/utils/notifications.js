// Utilitaires pour la gestion des notifications
import Echo from 'laravel-echo';

let echo = null;

export function initNotifications() {
    // Initialiser Laravel Echo
    if (typeof window.Laravel !== 'undefined' && window.Laravel.echo) {
        echo = new Echo({
            broadcaster: 'pusher',
            key: window.Laravel.echo.key,
            cluster: window.Laravel.echo.cluster,
            wsHost: window.Laravel.echo.wsHost,
            wsPort: window.Laravel.echo.wsPort,
            wssPort: window.Laravel.echo.wssPort,
            forceTLS: window.Laravel.echo.forceTLS,
            enabledTransports: ['ws', 'wss'],
        });
        
        // Écouter les notifications privées
        if (window.Laravel.user) {
            listenToUserNotifications(window.Laravel.user.id);
        }
    }
    
    // Initialiser les notifications système
    initSystemNotifications();
    
    // Initialiser les notifications toast
    initToastNotifications();
}

function listenToUserNotifications(userId) {
    if (!echo) return;
    
    echo.private(`users.${userId}`)
        .notification((notification) => {
            handleNewNotification(notification);
        });
    
    echo.private(`users.${userId}`)
        .listen('NotificationPushed', (event) => {
            handleRealtimeNotification(event);
        });
}

function handleNewNotification(notification) {
    // Ajouter la notification à la liste
    addNotificationToList(notification);
    
    // Afficher une alerte toast
    showToast(notification);
    
    // Mettre à jour le compteur
    updateNotificationCounter();
    
    // Jouer un son si activé
    playNotificationSound();
}

function handleRealtimeNotification(event) {
    const notification = {
        id: event.id,
        data: event.data,
        created_at: event.created_at,
        read_at: event.read_at,
    };
    
    handleNewNotification(notification);
}

function addNotificationToList(notification) {
    const container = document.querySelector('[data-notifications]');
    if (container) {
        const notificationElement = createNotificationElement(notification);
        container.insertBefore(notificationElement, container.firstChild);
        
        // Limiter le nombre de notifications affichées
        const notifications = container.querySelectorAll('.notification-item');
        if (notifications.length > 10) {
            notifications[notifications.length - 1].remove();
        }
    }
}

function createNotificationElement(notification) {
    const div = document.createElement('div');
    div.className = 'notification-item';
    if (!notification.read_at) {
        div.classList.add('unread');
    }
    div.dataset.notificationId = notification.id;
    
    const data = typeof notification.data === 'string' 
        ? JSON.parse(notification.data) 
        : notification.data;
    
    div.innerHTML = `
        <div class="notification-content">
            <div class="notification-avatar ${data.type || 'info'}">
                <i class="fas fa-${getNotificationIcon(data.type || 'info')}"></i>
            </div>
            <div class="notification-body">
                <div class="notification-header-row">
                    <div class="notification-title">${data.title || 'Nouvelle notification'}</div>
                    <div class="notification-time">${formatNotificationTime(notification.created_at)}</div>
                </div>
                <div class="notification-message">${data.message || ''}</div>
                <div class="notification-actions">
                    <button class="notification-action" data-action="mark-read">Marquer comme lu</button>
                    <button class="notification-action" data-action="delete">Supprimer</button>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter les écouteurs d'événements
    const markReadBtn = div.querySelector('[data-action="mark-read"]');
    const deleteBtn = div.querySelector('[data-action="delete"]');
    
    if (markReadBtn) {
        markReadBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            markNotificationAsRead(notification.id);
        });
    }
    
    if (deleteBtn) {
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            deleteNotification(notification.id);
        });
    }
    
    // Marquer comme lu au clic
    div.addEventListener('click', () => {
        if (!notification.read_at) {
            markNotificationAsRead(notification.id);
        }
    });
    
    return div;
}

function getNotificationIcon(type) {
    const icons = {
        'info': 'info-circle',
        'success': 'check-circle',
        'warning': 'exclamation-triangle',
        'error': 'exclamation-circle',
        'booking': 'calendar',
        'payment': 'credit-card',
        'message': 'envelope',
        'assignment': 'briefcase',
        'review': 'star',
        'provider': 'user-tie',
        'admin': 'user-shield',
    };
    return icons[type] || 'bell';
}

function formatNotificationTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'À l\'instant';
    if (diff < 3600000) return `Il y a ${Math.floor(diff / 60000)} min`;
    if (diff < 86400000) return `Il y a ${Math.floor(diff / 3600000)}h`;
    if (diff < 604800000) return `Il y a ${Math.floor(diff / 86400000)}j`;
    return date.toLocaleDateString('fr-FR');
}

function showToast(notification) {
    const data = typeof notification.data === 'string' 
        ? JSON.parse(notification.data) 
        : notification.data;
    
    const toast = document.createElement('div');
    toast.className = `toast ${data.type || 'info'}`;
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-icon">
                <i class="fas fa-${getNotificationIcon(data.type || 'info')}"></i>
            </div>
            <div class="toast-text">
                <strong>${data.title || 'Notification'}</strong>
                <p>${data.message || ''}</p>
            </div>
            <button class="toast-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Ajouter au conteneur de toasts
    const container = getOrCreateToastContainer();
    container.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Fermeture automatique
    const timeout = setTimeout(() => {
        removeToast(toast);
    }, 5000);
    
    // Fermeture manuelle
    toast.querySelector('.toast-close').addEventListener('click', () => {
        clearTimeout(timeout);
        removeToast(toast);
    });
}

function getOrCreateToastContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function removeToast(toast) {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
}

function updateNotificationCounter() {
    const counter = document.querySelector('[data-notification-counter]');
    if (counter) {
        const currentCount = parseInt(counter.textContent) || 0;
        counter.textContent = currentCount + 1;
        counter.classList.add('pulse');
        setTimeout(() => counter.classList.remove('pulse'), 1000);
    }
}

function playNotificationSound() {
    if (window.Laravel && window.Laravel.notifications && window.Laravel.notifications.sound) {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(() => {
            // Ignorer les erreurs de lecture audio (navigateur bloque autoplay)
        });
    }
}

function initSystemNotifications() {
    // Demander la permission pour les notifications navigateur
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

function initToastNotifications() {
    // Initialiser les toasts existants
    document.querySelectorAll('.toast').forEach(toast => {
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => removeToast(toast));
        }
    });
}

// Actions sur les notifications
async function markNotificationAsRead(notificationId) {
    try {
        await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        
        // Mettre à jour l'interface
        const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notificationElement) {
            notificationElement.classList.add('read');
            notificationElement.classList.remove('unread');
            
            // Supprimer le bouton "Marquer comme lu"
            const markReadBtn = notificationElement.querySelector('[data-action="mark-read"]');
            if (markReadBtn) {
                markReadBtn.remove();
            }
        }
        
        // Mettre à jour le compteur
        updateNotificationCounter(-1);
        
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

async function deleteNotification(notificationId) {
    try {
        await fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        
        // Supprimer l'élément du DOM
        const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notificationElement) {
            notificationElement.remove();
        }
        
        // Mettre à jour le compteur
        updateNotificationCounter(-1);
        
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
}

// Marquer toutes les notifications comme lues
async function markAllNotificationsAsRead() {
    try {
        await fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        
        // Mettre à jour l'interface
        document.querySelectorAll('.notification-item').forEach(item => {
            item.classList.add('read');
            const markReadBtn = item.querySelector('.mark-read-btn');
            if (markReadBtn) markReadBtn.remove();
        });
        
        // Réinitialiser le compteur
        const counter = document.querySelector('[data-notification-counter]');
        if (counter) {
            counter.textContent = '0';
        }
        
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
}

// Export des fonctions
export {
    initNotifications,
    markNotificationAsRead,
    deleteNotification,
    markAllNotificationsAsRead,
    showToast,
};
