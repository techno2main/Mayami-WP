# Migration WordPress Mayami vers OVH

**Branche Git :** `feature/migration-ovh`  
**Date de création :** 2 juin 2026  
**Objectif :** Migrer le thème Mayami et son admin vers nouveau WordPress OVH

---

## 📋 PRÉREQUIS

### Accès OVH nécessaires
- [x] **URL du nouveau site WordPress OVH : `http://ubocrhy.cluster100.hosting.ovh.net/wp/wp-admin/`**
- [ ] Accès admin WordPress (login/mot de passe)
- [ ] Accès FTP (FileZilla ou similaire)
  - Host : `ftp.votredomaine.com`
  - Username : `________`
  - Password : `________`
  - Port : `21` (ou `22` si SFTP)
- [ ] Accès base de données MySQL
  - Host : `________`
  - Database name : `________`
  - Username : `________`
  - Password : `________`

### Fichiers sources (local)
- [ ] Thème Mayami complet : `wp-content/themes/mayami/`
- [ ] Plugins actifs utilisés par Mayami (liste ci-dessous)
- [ ] Export base de données (si migration complète)

---

## 🎯 ÉTAPE 1 : INVENTAIRE DES COMPOSANTS

### 1.1 Fichiers du thème à migrer

**Dossier principal :**
```
wp-content/themes/mayami/
├── functions.php (functions admin + hooks)
├── style.css (metadata thème)
├── screenshot.png (aperçu thème)
├── visual-links-builder/ (outil Visual Links Builder)
│   ├── visual-links-builder.html
│   ├── styles/builder.css
│   ├── exports-html/ (⚠️ ATTENTION : exports existants)
│   └── doc/
├── templates/ (si templates personnalisés)
├── assets/ (CSS/JS/images)
└── [autres fichiers thème]
```

**✅ Action :** Lister tous les fichiers du thème Mayami
```powershell
Get-ChildItem -Recurse "c:\xampp\htdocs\web-am\dev.tad\MyWebsites\mayami-wp\wp-content\themes\mayami" | 
    Where-Object {!$_.PSIsContainer} | 
    Select-Object FullName | 
    Out-File "migration-ovh\inventaire-fichiers-theme.txt"
```

### 1.2 Plugins WordPress requis

**Plugins utilisés par le thème (à vérifier) :**
- [ ] Advanced Custom Fields (ACF) ?
- [ ] Contact Form 7 ?
- [ ] Yoast SEO ?
- [ ] WooCommerce ?
- [ ] [Autres plugins custom]

**✅ Action :** Lister les plugins actifs localement
```sql
-- Depuis phpMyAdmin local
SELECT option_value FROM wp_options WHERE option_name = 'active_plugins';
```

### 1.3 Pages/Posts WordPress utilisant le thème

**Pages admin créées :**
- [ ] Page admin Visual Links Builder
- [ ] Page admin Visual Links Preview
- [ ] [Autres pages admin custom]

**✅ Action :** Identifier les pages WordPress custom

---

## 🚀 ÉTAPE 2 : PRÉPARATION DU PACKAGE DE MIGRATION

### 2.1 Créer archive du thème

**Option A : Archive ZIP complète**
```powershell
# Depuis PowerShell
cd "c:\xampp\htdocs\web-am\dev.tad\MyWebsites\mayami-wp\wp-content\themes"
Compress-Archive -Path "mayami" -DestinationPath "C:\Temp\mayami-theme-migration.zip" -Force
```

**Option B : Archive sélective (sans exports-html)**
```powershell
# Exclure les exports existants pour alléger
$source = "c:\xampp\htdocs\web-am\dev.tad\MyWebsites\mayami-wp\wp-content\themes\mayami"
$dest = "C:\Temp\mayami-theme-clean"
Copy-Item -Path $source -Destination $dest -Recurse -Exclude "exports-html"
Compress-Archive -Path $dest -DestinationPath "C:\Temp\mayami-theme-migration.zip" -Force
Remove-Item -Recurse -Force $dest
```

**⚠️ RAPPEL IMPORTANT :** Ne jamais toucher à `/exports-html/Mayami-EPK/template/` sur le serveur !

### 2.2 Export base de données (si migration complète)

**Si migration COMPLÈTE du site :**
```sql
-- Export depuis phpMyAdmin local
-- Tables à exporter :
-- wp_posts, wp_postmeta, wp_options, wp_users, wp_usermeta
```

**Si migration THÈME SEUL :**
- Pas besoin d'export DB complet
- Juste reconfigurer les pages admin sur le nouveau WordPress

---

## 📤 ÉTAPE 3 : UPLOAD VERS OVH

### 3.1 Connexion FTP/SFTP

**Via FileZilla :**
1. Ouvrir FileZilla
2. Host : `ftp.votredomaine.com` (à confirmer)
3. Username : `________`
4. Password : `________`
5. Port : `21` (FTP) ou `22` (SFTP)
6. Connexion rapide

### 3.2 Upload du thème

**Chemin de destination OVH :**
```
/www/wp-content/themes/mayami/
```
OU
```
/public_html/wp-content/themes/mayami/
```
OU
```
/html/wp-content/themes/mayami/
```
(Vérifier la structure WordPress OVH)

**✅ Actions FileZilla :**
1. [ ] Naviguer vers `/wp-content/themes/` sur le serveur OVH
2. [ ] Créer dossier `mayami` si inexistant
3. [ ] Uploader TOUS les fichiers du thème
4. [ ] Vérifier les permissions (755 pour dossiers, 644 pour fichiers)

**Estimation temps upload :** ~5-15 minutes (selon taille thème + connexion)

### 3.3 Upload des plugins (si nécessaire)

**Chemin destination :**
```
/www/wp-content/plugins/
```

**✅ Action :** Uploader uniquement les plugins NON disponibles dans le répertoire WordPress officiel

---

## ⚙️ ÉTAPE 4 : CONFIGURATION WORDPRESS OVH

### 4.1 Activer le thème Mayami

**Via admin WordPress OVH :**
1. [ ] Se connecter à `https://votredomaine.com/wp-admin`
2. [ ] Apparence → Thèmes
3. [ ] Activer le thème "Mayami"
4. [ ] Vérifier l'aperçu du site

### 4.2 Configurer les pages admin custom

**Visual Links Builder :**

Le thème Mayami enregistre ces pages via `functions.php` :
- Page : `mayami_visual_links_builder` (menu admin)
- Page : `mayami_visual_links_preview` (submenu caché)

**✅ Actions :**
1. [ ] Vérifier que `functions.php` est bien uploadé
2. [ ] Aller dans admin WordPress → menu latéral
3. [ ] Chercher "Visual Links" ou menu custom Mayami
4. [ ] Tester l'accès aux pages admin

**Si pages admin manquantes :**
- Vérifier logs PHP : `/www/logs/` ou `/public_html/logs/`
- Vérifier que `add_menu_page()` fonctionne dans `functions.php`

### 4.3 Créer dossiers d'exports

**Créer structure pour exports :**
```
/www/wp-content/themes/mayami/visual-links-builder/exports-html/
```

**Via FTP :**
1. [ ] Créer dossier `exports-html` (si manquant)
2. [ ] Permissions : `755` (lecture/écriture/exécution)
3. [ ] Tester génération export depuis Visual Links Builder

### 4.4 Configurer les permaliens

**Réglages WordPress :**
1. [ ] Réglages → Permaliens
2. [ ] Choisir structure : `/%postname%/` (recommandé)
3. [ ] Enregistrer

---

## 🧪 ÉTAPE 5 : TESTS POST-MIGRATION

### 5.1 Tests fonctionnels du thème

**Checklist de validation :**
- [ ] Page d'accueil s'affiche correctement
- [ ] Menu de navigation fonctionne
- [ ] Styles CSS chargés (pas de 404 sur CSS)
- [ ] Scripts JS chargés (pas d'erreurs console)
- [ ] Images du thème s'affichent

### 5.2 Tests Visual Links Builder

**Accès et création :**
1. [ ] Menu admin "Visual Links" accessible
2. [ ] Bouton "Créer nouveau visuel" fonctionne
3. [ ] Upload d'image fonctionne
4. [ ] Création de zones cliquables fonctionne
5. [ ] Sauvegarde brouillon fonctionne
6. [ ] Preview fonctionne
7. [ ] Export Template HTML fonctionne
8. [ ] Export Template E-Mail fonctionne

**Vérifier les fichiers générés :**
- [ ] Exports sauvegardés dans `/exports-html/`
- [ ] Fichiers accessibles via URL publique
- [ ] Pas d'erreurs 403/404 sur exports

### 5.3 Tests base de données

**Vérifier tables WordPress :**
```sql
-- Depuis phpMyAdmin OVH
SHOW TABLES LIKE 'wp_%';

-- Vérifier table drafts Visual Links (si existe)
SHOW TABLES LIKE '%visual_links%';
```

**Actions si tables manquantes :**
- Exécuter scripts SQL de création (depuis migration-ovh/sql/)
- OU relancer hooks d'activation thème

### 5.4 Tests de sécurité

**Permissions fichiers :**
```bash
# Via SSH OVH (si accès disponible)
find /www/wp-content/themes/mayami -type d -exec chmod 755 {} \;
find /www/wp-content/themes/mayami -type f -exec chmod 644 {} \;
```

**Fichiers sensibles :**
- [ ] Vérifier que `.htaccess` protège `/exports-html/` (si nécessaire)
- [ ] Pas de fichiers `.git` uploadés (sécurité)

---

## 🐛 ÉTAPE 6 : DÉPANNAGE (TROUBLESHOOTING)

### 6.1 Erreur "Le thème est cassé"

**Symptôme :** WordPress affiche "Le thème est manquant le fichier style.css"

**Solutions :**
1. Vérifier upload complet du dossier `mayami/`
2. Vérifier présence de `style.css` à la racine du thème
3. Vérifier header CSS dans `style.css` :
```css
/*
Theme Name: Mayami
Theme URI: https://votredomaine.com
Description: Thème custom Mayami
Author: Votre Nom
Version: 1.0
*/
```

### 6.2 Erreur 500 (Internal Server Error)

**Causes possibles :**
- Erreur PHP dans `functions.php`
- Plugin incompatible
- Version PHP incompatible

**Solutions :**
1. Activer debug WordPress :
```php
// Dans wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```
2. Consulter `/wp-content/debug.log`
3. Désactiver plugins un par un

### 6.3 Visual Links Builder ne s'affiche pas

**Vérifications :**
1. [ ] `functions.php` contient bien `add_menu_page('mayami_visual_links_builder')`
2. [ ] Fichier `visual-links-builder/visual-links-builder.html` uploadé
3. [ ] URL d'accès correcte : `/wp-admin/admin.php?page=mayami_visual_links_builder`
4. [ ] Permissions utilisateur WordPress (admin requis)

### 6.4 Exports ne fonctionnent pas

**Symptôme :** Erreur lors de la génération d'export

**Solutions :**
1. Vérifier permissions dossier `exports-html/` (755)
2. Vérifier fonction AJAX dans `functions.php` :
   - `mayami_export_visual_links_html`
   - `mayami_save_visual_links_draft`
3. Tester manuellement écriture fichier via FTP

---

## 📝 ÉTAPE 7 : DOCUMENTATION POST-MIGRATION

### 7.1 URLs importantes

**Site OVH :**
- URL publique : `https://votredomaine.com`
- Admin WordPress : `https://votredomaine.com/wp-admin`
- Visual Links Builder : `https://votredomaine.com/wp-admin/admin.php?page=mayami_visual_links_builder`

### 7.2 Accès FTP/Base de données

**Conserver dans gestionnaire de mots de passe :**
- [ ] Accès FTP OVH
- [ ] Accès base de données MySQL OVH
- [ ] Accès admin WordPress OVH

### 7.3 Changelog migration

**Fichier :** `migration-ovh/CHANGELOG.md`

**Format :**
```markdown
## 2026-06-02 - Migration initiale OVH

### ✅ Fichiers migrés
- Thème Mayami complet (XXX fichiers)
- Visual Links Builder
- Styles CSS

### ⚙️ Configuration
- Thème activé
- Permaliens : /%postname%/
- Pages admin créées

### 🧪 Tests validés
- [x] Thème s'affiche
- [x] Visual Links fonctionne
- [x] Exports fonctionnent

### 🐛 Problèmes rencontrés
- Aucun

### 📌 Notes
- Conserver exports locaux en backup
```

---

## ✅ CHECKLIST FINALE DE VALIDATION

### Migration thème
- [ ] Tous les fichiers uploadés via FTP
- [ ] Thème activé dans WordPress
- [ ] Pas d'erreurs 404 sur CSS/JS
- [ ] Page d'accueil s'affiche

### Visual Links Builder
- [ ] Menu admin accessible
- [ ] Upload image fonctionne
- [ ] Zones cliquables fonctionnent
- [ ] Sauvegarde fonctionne
- [ ] Preview fonctionne
- [ ] Export Template HTML fonctionne
- [ ] Export Template E-Mail fonctionne
- [ ] Fichiers exports accessibles

### Sécurité
- [ ] Permissions fichiers correctes (755/644)
- [ ] Pas de fichiers `.git` exposés
- [ ] Debug WordPress désactivé en production
- [ ] Sauvegardes configurées

### Documentation
- [ ] URLs importantes documentées
- [ ] Accès sauvegardés (FTP/DB)
- [ ] Changelog créé
- [ ] Guide utilisateur mis à jour

---

## 🆘 SUPPORT ET RESSOURCES

### Documentation WordPress OVH
- https://docs.ovh.com/fr/hosting/
- https://docs.ovh.com/fr/hosting/configurer-multisite-wordpress/

### Logs et debugging
- Logs PHP OVH : `/www/logs/` ou `/logs/`
- Debug WordPress : `wp-content/debug.log`
- Console navigateur (F12) pour erreurs JS

### Backup et rollback
- Sauvegarder thème local AVANT upload
- Garder export base de données locale
- Si problème : restaurer thème WordPress par défaut

---

## 📅 PLANNING MIGRATION

**Durée estimée totale : 2-4 heures**

| Étape | Durée estimée | Statut |
|-------|---------------|--------|
| 1. Inventaire composants | 30 min | ⏳ TODO |
| 2. Préparation package | 30 min | ⏳ TODO |
| 3. Upload FTP | 15 min | ⏳ TODO |
| 4. Configuration WordPress | 45 min | ⏳ TODO |
| 5. Tests post-migration | 60 min | ⏳ TODO |
| 6. Dépannage (si nécessaire) | Variable | ⏳ TODO |
| 7. Documentation | 30 min | ⏳ TODO |

**🎯 Objectif :** Migration complète et fonctionnelle du thème Mayami sur OVH

---

**Dernière mise à jour :** 2 juin 2026  
**Branche Git :** `feature/migration-ovh`
