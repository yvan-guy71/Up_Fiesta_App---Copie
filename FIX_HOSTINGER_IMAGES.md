# 🚀 Correction du Problème d'Images sur Hostinger

## Étape 1 : Connexion SSH à Hostinger

```bash
ssh utilisateur@hostinger  # Utilisez vos credentials Hostinger
```

Ou via le Terminal Hostinger si vous êtes dans cPanel.

## Étape 2 : Naviguer vers votre application Laravel

```bash
cd public_html  # Ou public_www selon vos paramètres
# ou si Laravel est dans un sous-dossier:
cd public_html/votre-app
```

## Étape 3 : Vérifier la structure

```bash
ls -la
# Vous devez voir: public/, storage/, etc.
```

## Étape 4 : Créer le lien symbolique

Exécutez **UNE SEULE** de ces commandes :

### Option A : Via Artisan (Recommandé)
```bash
php artisan storage:link
```

**Résultat attendu :**
```
The [public/storage] directory has been linked to [storage/app/public] successfully.
```

### Option B : Commande manuelle (si Artisan ne fonctionne pas)
```bash
ln -s storage/app/public public/storage
```

## Étape 5 : Vérifier que ça fonctionne

```bash
ls -la public/storage
# Vous devez voir les dossiers:
# -> providers-logos
# -> providers
# -> verification
```

## Étape 6 : Vider le cache

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

---

## 🔍 Si ça ne marche pas :

### Vérifier les permissions
```bash
chmod -R 755 storage/
chmod -R 755 public/
```

### Vérifier que les fichiers existent
```bash
ls -R storage/app/public/
# Vous devez voir:
# storage/app/public/providers-logos/
# storage/app/public/providers/
# storage/app/public/verification/
```

### Vérifier le lien
```bash
readlink public/storage
# Doit afficher: storage/app/public
```

### Voir les logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📝 Vérification dans cPanel

1. Connectez-vous à **cPanel**
2. Allez dans **File Manager** (Gestionnaire de fichiers)
3. Ouvrez le dossier **public_html**
4. Vous devez voir un dossier **storage** avec une **flèche** (symbole du lien)
5. Double-cliquez dessus → doit ouvrir `storage/app/public`

---

## ✅ Test Final

Une fois le lien créé, accédez à:
```
https://votre-domaine.com/storage/providers-logos/
```

Vous devez voir la liste des fichiers ou les images doivent s'afficher sur votre site.

