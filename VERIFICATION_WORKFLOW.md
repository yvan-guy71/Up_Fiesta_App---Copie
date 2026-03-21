# Workflow de Vérification et Notation des Prestataires

## Vue d'ensemble

Ce système implémente un workflow complet pour assurer la qualité des services rendus par les prestataires:

1. **Prestataire marque comme terminé** → Client est notifié de noter
2. **Client note le prestataire** → Admin reçoit la notification
3. **Admin vérifie le travail** → Réduction de 15% appliquée
4. **Prestataire reçoit le paiement** → Avec la réduction de commission

## Étapes détaillées

### 1. Prestataire marque le service comme terminé

Quand le prestataire met `provider_done = true` sur une réservation:
- Un observateur Booking détecte le changement
- Envoie `ReviewRequestedNotification` au client
- Définit `require_client_review = true` et `client_review_requested_at = now()`

**Modèle affecté:** `App\Models\Booking` (méthode static booted)

### 2. Client note le prestataire

Le client peut maintenant noter via la route POST `/reservations/{booking}/avis`:
- Accepte les notes de 1 à 5 et un commentaire optionnel
- Crée une entrée `Review` dans la base de données
- Le ReviewController accepte la notation tant que:
  - Le client est propriétaire de la réservation
  - `provider_done = true` AND `require_client_review = true`

**Modèle affecté:** `App\Models\Review`
**Contrôleur:** `App\Http\Controllers\ReviewController@store`

### 3. Admin vérifie le travail

L'admin peut vérifier le service via:

#### Option A: Tableau des tâches en attente de vérification
Widget `PendingVerifications` (nouvel widget):
- Affiche les réservations avec `admin_verification_status = 'pending'`
- Montre si le client a noté ou non
- Bouton "Vérifier" pour confirmer la tâche

#### Option B: Page des réservations
Ressource `BookingResource`:
- Ajoute la colonne `admin_verification_status`
- Ajoute la colonne `require_client_review` et `review.id`
- Ajoute l'action "Vérifier le travail"

**Action:** `verify_by_admin`
- Appelle `BookingReviewService::verifyBookingByAdmin()`
- Applique une réduction de 15% sur `provider_amount`
- Définit `admin_verification_status = 'verified'`
- Définit `payout_status = 'ready'`
- Envoie `BookingVerifiedByAdminNotification` au prestataire

### 4. Payout au prestataire

Les prestataires avec `admin_verification_status = 'verified'` et `payout_status = 'ready'` peuvent être payés:
- L'action "Terminer et payer le prestataire" devient disponible
- Exécute le transfer via `PayoutService::transfer()`
- Montant versé = `provider_amount - provider_commission_reduction`

## Modifications de base de données

### Migration: `2026_03_20_000000_add_admin_verification_to_bookings.php`

Ajoute les colonnes suivantes à la table `bookings`:
- `require_client_review` (boolean, default: false) - La notation a-t-elle été demandée?
- `client_review_requested_at` (timestamp nullable) - Quand le client a-t-il été notifié?
- `admin_verification_status` (string, default: 'pending') - Statut: 'pending' ou 'verified'
- `admin_verified_at` (timestamp nullable) - Quand l'admin a-t-il vérifié?
- `admin_verified_by` (foreign key nullable) - Qui a vérifié?
- `provider_commission_reduction` (decimal, default: 0) - Montant de la réduction

## Services

### BookingReviewService

**Méthode:** `requestClientReview(Booking $booking)`
- Vérifie que la notification n'a pas déjà été envoyée
- Envoie `ReviewRequestedNotification` au client
- Marque les flags internes

**Méthode:** `verifyBookingByAdmin(Booking $booking, int $adminId, bool $applyCommissionReduction = true)`
- Calcule la réduction de 15% si `$applyCommissionReduction = true`
- Réduit `provider_amount` du montant calculé
- Marque comme vérifié avec le timestamp et l'ID de l'admin
- Envoie `BookingVerifiedByAdminNotification` au prestataire
- Change `payout_status` à 'ready'

**Méthode:** `hasClientReviewd(Booking $booking): bool`
- Vérifie si une critique existe pour cette réservation

## Notifications

### ReviewRequestedNotification
- **Pour:** Client
- **Quand:** Prestataire marque comme terminé
- **Contenu:** Demande de noter le prestataire
- **Lien d'action:** `/reviews/{booking->id}`

### BookingVerifiedByAdminNotification
- **Pour:** Prestataire
- **Quand:** Admin vérifie le travail
- **Contenu:** Service vérifié, paiement en attente
- **Montant de réduction:** Affiché si applicable

## Widgets

### PendingVerifications (Nouveau)
- **Localisation:** Dashboard admin
- **Tri:** Position 2 (avant PendingPayouts)
- **Affichage:**
  - Réservations payées avec `provider_done = true`
  - Filtrées par `admin_verification_status = 'pending'`
  - Colonnes: ID, Client, Prestataire, Montants, Notification de réduction appliquée
- **Actions:** 
  - Bouton "Vérifier" avec confirmation modale

### PendingPayouts (Modifié)
- **Nouvelles colonnes:**
  - `require_client_review` - Notation demandée
  - `review.id` - Noté par le client
  - `provider_commission_reduction` - Réduction appliquée

## Ressources Filament

### BookingResource
- **Nouvelles colonnes:** 
  - `require_client_review`
  - `review.id` (état)
  - `admin_verification_status`
  - `provider_commission_reduction`
- **Nouvelle action:** `verify_by_admin`
  - Disponible quand: réservation payée, provider_done, pas encore vérifié
  - Applique la vérification

## Flux complet d'exemple

```
1. Réservation créée & payée (status: confirmed, payment_status: paid)
2. Prestataire termine et met provider_done = true
   → Client reçoit ReviewRequestedNotification
   → require_client_review = true, client_review_requested_at = now()
3. Client note le prestataire (rating: 5, comment: "Excellent!")
   → Review créée dans la base de données
4. Admin voit la réservation dans le widget PendingVerifications
   → Clique sur "Vérifier"
   → provider_amount réduit de 15%
   → admin_verification_status = 'verified'
   → Prestataire reçoit BookingVerifiedByAdminNotification
5. Admin clique sur "Terminer et payer"
   → PayoutService::transfer() exécuté
   → Prestataire reçoit le montant réduit
   → payout_status = 'paid'
```

## Configuration Filament

Le widget `PendingVerifications` est enregistré dans `AdminPanelProvider`:
```php
->widgets([
    ...
    \App\Filament\Widgets\PendingVerifications::class,
    \App\Filament\Widgets\PendingPayouts::class,
])
```

## Cas d'utilisation spéciaux

### Que se passe-t-il si le client ne note pas?
- La réservation reste en `admin_verification_status = 'pending'`
- L'admin peut toujours vérifier manuellement
- La réduction de 15% sera quand même appliquée lors de la vérification
- Recommandation: ajouter un rappel après X jours si le client n'a pas noté

### Peut-on ne pas appliquer la réduction?
- Oui, le paramètre `applyCommissionReduction` peut être défini à `false`
- Dans ce cas, `verifyBookingByAdmin()` ne réduit pas le montant

### Est-ce qu'une réduction peut être modifiée après?
- Actuellement non, mais `provider_commission_reduction` peut être modifiée manuellement en base si nécessaire
- À l'avenir, implémenter une action d'ajustement dans l'interface admin
