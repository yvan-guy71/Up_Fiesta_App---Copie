// Fichier JavaScript spécifique pour l'administration
import '@css/admin.css';

// Import des utilitaires
import { initNotifications } from '@utils/notifications';
import { initRealtime } from '@utils/realtime';
import { initForms } from '@utils/forms';

// Initialisation des modules pour l'administration
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser les notifications temps réel
    initNotifications();
    
    // Initialiser la connexion WebSocket
    initRealtime();
    
    // Initialiser les formulaires avancés
    initForms();
    
    // Initialiser les fonctionnalités spécifiques à l'administration
    initAdminFeatures();
});

function initAdminFeatures() {
    // Gestion du tableau de bord
    initDashboard();
    
    // Gestion des utilisateurs
    initUserManagement();
    
    // Gestion des prestataires
    initProviderManagement();
    
    // Gestion des services
    initServiceManagement();
    
    // Gestion des statistiques
    initStatistics();
}

function initDashboard() {
    // Initialiser les graphiques
    initCharts();
    
    // Initialiser les compteurs
    initCounters();
    
    // Initialiser les activités récentes
    initRecentActivities();
}

function initCharts() {
    // Graphique des revenus
    const revenueChart = document.getElementById('revenue-chart');
    if (revenueChart) {
        createRevenueChart(revenueChart);
    }
    
    // Graphique des services
    const servicesChart = document.getElementById('services-chart');
    if (servicesChart) {
        createServicesChart(servicesChart);
    }
    
    // Graphique des utilisateurs
    const usersChart = document.getElementById('users-chart');
    if (usersChart) {
        createUsersChart(usersChart);
    }
}

function createRevenueChart(canvas) {
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Revenus (XOF)',
                data: [1200000, 1900000, 3000000, 2500000, 2700000, 3200000],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' XOF';
                        }
                    }
                }
            }
        }
    });
}

function createServicesChart(canvas) {
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Événements', 'Services', 'Prestations', 'Autres'],
            datasets: [{
                data: [30, 25, 35, 10],
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(34, 197, 94)',
                    'rgb(251, 146, 60)',
                    'rgb(163, 230, 53)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
}

function createUsersChart(canvas) {
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Clients', 'Prestataires', 'Admins'],
            datasets: [{
                label: 'Utilisateurs',
                data: [450, 120, 5],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(251, 146, 60, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initCounters() {
    // Animation des compteurs
    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach(counter => {
        animateCounter(counter);
    });
}

function animateCounter(element) {
    const target = parseInt(element.dataset.counter);
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 16);
}

function initRecentActivities() {
    // Rafraîchissement automatique des activités récentes
    const activitiesContainer = document.querySelector('[data-activities]');
    if (activitiesContainer) {
        setInterval(() => {
            loadRecentActivities();
        }, 30000); // Toutes les 30 secondes
    }
}

async function loadRecentActivities() {
    try {
        const response = await fetch('/api/admin/activities', {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            const activities = await response.json();
            updateActivitiesDisplay(activities);
        }
    } catch (error) {
        console.error('Error loading activities:', error);
    }
}

function updateActivitiesDisplay(activities) {
    const container = document.querySelector('[data-activities]');
    if (container) {
        container.innerHTML = activities.map(activity => `
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-${getActivityIcon(activity.type)}"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-text">${activity.description}</p>
                    <span class="activity-time">${formatTime(activity.created_at)}</span>
                </div>
            </div>
        `).join('');
    }
}

function getActivityIcon(type) {
    const icons = {
        'user_registered': 'user-plus',
        'provider_approved': 'check-circle',
        'booking_created': 'calendar-plus',
        'payment_completed': 'credit-card',
        'service_completed': 'check-double',
        'message_sent': 'envelope',
    };
    return icons[type] || 'circle';
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'Il y a quelques secondes';
    if (diff < 3600000) return `Il y a ${Math.floor(diff / 60000)} minutes`;
    if (diff < 86400000) return `Il y a ${Math.floor(diff / 3600000)} heures`;
    return `Il y a ${Math.floor(diff / 86400000)} jours`;
}

function initUserManagement() {
    // Gestion des actions sur les utilisateurs
    document.querySelectorAll('[data-user-action]').forEach(button => {
        button.addEventListener('click', handleUserAction);
    });
}

function handleUserAction(event) {
    const button = event.currentTarget;
    const action = button.dataset.userAction;
    const userId = button.dataset.userId;
    
    if (action === 'delete') {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            submitUserAction(userId, action);
        }
    } else if (action === 'toggle_status') {
        submitUserAction(userId, action);
    }
}

async function submitUserAction(userId, action) {
    try {
        const response = await fetch(`/api/admin/users/${userId}/${action}`, {
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

function initProviderManagement() {
    // Gestion des actions sur les prestataires
    document.querySelectorAll('[data-provider-action]').forEach(button => {
        button.addEventListener('click', handleProviderAction);
    });
}

function handleProviderAction(event) {
    const button = event.currentTarget;
    const action = button.dataset.providerAction;
    const providerId = button.dataset.providerId;
    
    if (action === 'approve') {
        if (confirm('Approuver ce prestataire ?')) {
            submitProviderAction(providerId, action);
        }
    } else if (action === 'reject') {
        const reason = prompt('Raison du rejet :');
        if (reason) {
            submitProviderAction(providerId, action, { reason });
        }
    } else if (action === 'delete') {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce prestataire ?')) {
            submitProviderAction(providerId, action);
        }
    }
}

async function submitProviderAction(providerId, action, data = {}) {
    try {
        const response = await fetch(`/api/admin/providers/${providerId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
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

function initServiceManagement() {
    // Gestion des actions sur les services
    document.querySelectorAll('[data-service-action]').forEach(button => {
        button.addEventListener('click', handleServiceAction);
    });
}

function handleServiceAction(event) {
    const button = event.currentTarget;
    const action = button.dataset.serviceAction;
    const serviceId = button.dataset.serviceId;
    
    if (action === 'assign') {
        showAssignmentModal(serviceId);
    } else if (action === 'delete') {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
            submitServiceAction(serviceId, action);
        }
    }
}

function showAssignmentModal(serviceId) {
    const modal = document.getElementById('assignment-modal');
    if (modal) {
        modal.dataset.serviceId = serviceId;
        modal.classList.remove('hidden');
    }
}

async function submitServiceAction(serviceId, action) {
    try {
        const response = await fetch(`/api/admin/services/${serviceId}/${action}`, {
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

function initStatistics() {
    // Export des statistiques
    const exportButton = document.querySelector('[data-export-stats]');
    if (exportButton) {
        exportButton.addEventListener('click', exportStatistics);
    }
}

async function exportStatistics() {
    try {
        const response = await fetch('/api/admin/statistics/export', {
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            }
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `statistiques-${new Date().toISOString().split('T')[0]}.xlsx`;
            a.click();
            window.URL.revokeObjectURL(url);
        }
    } catch (error) {
        console.error('Export error:', error);
        alert('Erreur lors de l\'export des statistiques.');
    }
}

// Export des fonctions pour utilisation externe
export {
    initAdminFeatures,
    createRevenueChart,
    createServicesChart,
    createUsersChart,
    handleUserAction,
    handleProviderAction,
    handleServiceAction,
};
