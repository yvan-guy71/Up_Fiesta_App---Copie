// Utilitaires pour la gestion des connexions temps réel
import Echo from 'laravel-echo';

let echo = null;
let isConnected = false;

export function initRealtime() {
    // Initialiser Laravel Echo si disponible
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
            disableStats: true,
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback) => {
                        fetch('/broadcasting/auth', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                socket_id: socketId,
                                channel_name: channel.name
                            })
                        })
                        .then(response => response.json())
                        .then(data => callback(false, data))
                        .catch(error => callback(true, error));
                    },
                };
            },
        });
        
        // Écouter les événements de connexion
        echo.connector.pusher.connection.bind('connected', () => {
            isConnected = true;
            updateConnectionStatus(true);
            console.log('Connected to real-time server');
        });
        
        echo.connector.pusher.connection.bind('disconnected', () => {
            isConnected = false;
            updateConnectionStatus(false);
            console.log('Disconnected from real-time server');
        });
        
        echo.connector.pusher.connection.bind('error', (error) => {
            isConnected = false;
            updateConnectionStatus(false);
            console.error('Real-time connection error:', error);
        });
        
        // Initialiser les canaux selon le rôle de l'utilisateur
        initializeUserChannels();
    }
}

function initializeUserChannels() {
    if (!echo || !window.Laravel.user) return;
    
    const user = window.Laravel.user;
    
    // Canal privé pour l'utilisateur
    echo.private(`users.${user.id}`)
        .listen('.UserStatusChanged', (event) => {
            handleUserStatusChange(event);
        })
        .listen('.MessageReceived', (event) => {
            handleNewMessage(event);
        })
        .listen('.BookingUpdated', (event) => {
            handleBookingUpdate(event);
        })
        .listen('.AssignmentUpdated', (event) => {
            handleAssignmentUpdate(event);
        });
    
    // Canaux spécifiques selon le rôle
    if (user.role === 'admin') {
        initializeAdminChannels();
    } else if (user.role === 'provider') {
        initializeProviderChannels(user.id);
    } else if (user.role === 'client') {
        initializeClientChannels(user.id);
    }
}

function initializeAdminChannels() {
    // Canal administrateur pour les notifications système
    echo.channel('admin')
        .listen('.NewUserRegistered', (event) => {
            handleNewUserRegistration(event);
        })
        .listen('.ProviderVerificationRequest', (event) => {
            handleProviderVerification(event);
        })
        .listen('.PaymentReceived', (event) => {
            handlePaymentReceived(event);
        })
        .listen('.SystemAlert', (event) => {
            handleSystemAlert(event);
        });
}

function initializeProviderChannels(providerId) {
    // Canal prestataire pour les assignations
    echo.channel(`providers.${providerId}`)
        .listen('.ServiceAssigned', (event) => {
            handleServiceAssignment(event);
        })
        .listen('.BookingConfirmed', (event) => {
            handleBookingConfirmation(event);
        })
        .listen('.ReviewReceived', (event) => {
            handleReviewReceived(event);
        });
}

function initializeClientChannels(clientId) {
    // Canal client pour les mises à jour de réservations
    echo.channel(`clients.${clientId}`)
        .listen('.BookingStatusChanged', (event) => {
            handleBookingStatusChange(event);
        })
        .listen('.ProviderAssigned', (event) => {
            handleProviderAssignment(event);
        });
}

// Gestionnaires d'événements
function handleUserStatusChange(event) {
    console.log('User status changed:', event);
    updateOnlineStatus(event.userId, event.status);
}

function handleNewMessage(event) {
    console.log('New message received:', event);
    if (window.ChatApp) {
        window.ChatApp.addMessage(event.message);
    }
    
    // Afficher une notification si l'utilisateur n'est pas sur la page de chat
    if (!isChatPageActive()) {
        showNewMessageNotification(event);
    }
}

function handleBookingUpdate(event) {
    console.log('Booking updated:', event);
    updateBookingDisplay(event.booking);
    
    // Afficher une notification
    if (window.notifications) {
        window.notifications.showToast({
            type: 'info',
            title: 'Mise à jour de réservation',
            message: `La réservation #${event.booking.id} a été mise à jour`,
        });
    }
}

function handleAssignmentUpdate(event) {
    console.log('Assignment updated:', event);
    updateAssignmentDisplay(event.assignment);
    
    // Afficher une notification
    if (window.notifications) {
        window.notifications.showToast({
            type: 'info',
            title: 'Mise à jour d\'assignation',
            message: `L'assignation #${event.assignment.id} a été mise à jour`,
        });
    }
}

function handleNewUserRegistration(event) {
    console.log('New user registered:', event);
    updateAdminDashboard();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'info',
            title: 'Nouvel utilisateur',
            message: `${event.user.name} s'est inscrit sur la plateforme`,
        });
    }
}

function handleProviderVerification(event) {
    console.log('Provider verification request:', event);
    updateAdminDashboard();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'warning',
            title: 'Vérification requise',
            message: `Le prestataire ${event.provider.name} attend la vérification`,
        });
    }
}

function handlePaymentReceived(event) {
    console.log('Payment received:', event);
    updateAdminDashboard();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'success',
            title: 'Paiement reçu',
            message: `Paiement de ${event.amount} XOF reçu`,
        });
    }
}

function handleSystemAlert(event) {
    console.log('System alert:', event);
    
    if (window.notifications) {
        window.notifications.showToast({
            type: event.level || 'warning',
            title: 'Alerte système',
            message: event.message,
        });
    }
}

function handleServiceAssignment(event) {
    console.log('Service assigned:', event);
    updateProviderAssignments();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'info',
            title: 'Nouvelle assignation',
            message: `Vous avez été assigné à un nouveau service`,
        });
    }
}

function handleBookingConfirmation(event) {
    console.log('Booking confirmed:', event);
    updateProviderBookings();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'success',
            title: 'Réservation confirmée',
            message: `La réservation #${event.booking.id} a été confirmée`,
        });
    }
}

function handleReviewReceived(event) {
    console.log('Review received:', event);
    updateProviderReviews();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'info',
            title: 'Nouvel avis',
            message: `Vous avez reçu un nouvel avis (${event.review.rating}/5)`,
        });
    }
}

function handleBookingStatusChange(event) {
    console.log('Booking status changed:', event);
    updateClientBookings();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'info',
            title: 'Statut de réservation',
            message: `Votre réservation #${event.booking.id} est maintenant ${event.booking.status}`,
        });
    }
}

function handleProviderAssignment(event) {
    console.log('Provider assigned:', event);
    updateClientBookings();
    
    if (window.notifications) {
        window.notifications.showToast({
            type: 'success',
            title: 'Prestataire assigné',
            message: `${event.provider.name} a été assigné à votre demande`,
        });
    }
}

// Fonctions utilitaires
function updateConnectionStatus(connected) {
    const statusIndicator = document.querySelector('[data-connection-status]');
    if (statusIndicator) {
        statusIndicator.className = connected ? 'connected' : 'disconnected';
        statusIndicator.title = connected ? 'Connecté' : 'Déconnecté';
    }
}

function updateOnlineStatus(userId, status) {
    const userElements = document.querySelectorAll(`[data-user-id="${userId}"]`);
    userElements.forEach(element => {
        const indicator = element.querySelector('.online-indicator');
        if (indicator) {
            indicator.className = status ? 'online-indicator online' : 'online-indicator offline';
        }
    });
}

function isChatPageActive() {
    return window.location.pathname.includes('/messages') || 
           window.location.pathname.includes('/chat');
}

function showNewMessageNotification(event) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(`Nouveau message de ${event.message.sender_name}`, {
            body: event.message.content,
            icon: '/images/logo.png',
            tag: `message-${event.message.id}`,
        });
    }
}

function updateBookingDisplay(booking) {
    const bookingElements = document.querySelectorAll(`[data-booking-id="${booking.id}"]`);
    bookingElements.forEach(element => {
        const statusElement = element.querySelector('.booking-status');
        if (statusElement) {
            statusElement.textContent = booking.status;
            statusElement.className = `booking-status status-${booking.status}`;
        }
    });
}

function updateAssignmentDisplay(assignment) {
    const assignmentElements = document.querySelectorAll(`[data-assignment-id="${assignment.id}"]`);
    assignmentElements.forEach(element => {
        const statusElement = element.querySelector('.assignment-status');
        if (statusElement) {
            statusElement.textContent = assignment.status;
            statusElement.className = `assignment-status status-${assignment.status}`;
        }
    });
}

function updateAdminDashboard() {
    // Mettre à jour les compteurs du tableau de bord admin
    const counters = document.querySelectorAll('[data-dashboard-counter]');
    counters.forEach(counter => {
        const current = parseInt(counter.textContent) || 0;
        counter.textContent = current + 1;
    });
    
    // Déclencher un rafraîchissement des activités récentes
    if (window.loadRecentActivities) {
        window.loadRecentActivities();
    }
}

function updateProviderAssignments() {
    // Mettre à jour le compteur d'assignations
    const counter = document.querySelector('[data-assignments-counter]');
    if (counter) {
        const current = parseInt(counter.textContent) || 0;
        counter.textContent = current + 1;
    }
    
    // Rafraîchir la liste des assignations
    if (window.location.pathname.includes('/assignments')) {
        location.reload();
    }
}

function updateProviderBookings() {
    // Mettre à jour le compteur de réservations
    const counter = document.querySelector('[data-bookings-counter]');
    if (counter) {
        const current = parseInt(counter.textContent) || 0;
        counter.textContent = current + 1;
    }
    
    // Rafraîchir la liste des réservations
    if (window.location.pathname.includes('/bookings')) {
        location.reload();
    }
}

function updateProviderReviews() {
    // Mettre à jour le compteur d'avis
    const counter = document.querySelector('[data-reviews-counter]');
    if (counter) {
        const current = parseInt(counter.textContent) || 0;
        counter.textContent = current + 1;
    }
    
    // Rafraîchir la liste des avis
    if (window.location.pathname.includes('/reviews')) {
        location.reload();
    }
}

function updateClientBookings() {
    // Mettre à jour le compteur de réservations client
    const counter = document.querySelector('[data-client-bookings-counter]');
    if (counter) {
        const current = parseInt(counter.textContent) || 0;
        counter.textContent = current + 1;
    }
    
    // Rafraîchir la liste des réservations
    if (window.location.pathname.includes('/mes-reservations')) {
        location.reload();
    }
}

// Export des fonctions
export {
    initRealtime,
    isConnected,
    updateConnectionStatus,
};

