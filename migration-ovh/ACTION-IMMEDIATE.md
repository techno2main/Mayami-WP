# 🚀 ACTIONS IMMÉDIATES - Migration OVH

**Date :** 2 juin 2026  
**URL OVH :** http://ubocrhy.cluster100.hosting.ovh.net/wp/wp-admin/

---

## ⚠️ AVANT TOUTE IMPORTATION

### Étape 0 : Réglages WordPress OVH OBLIGATOIRES

**Sur l'admin OVH, vérifie et configure :**

#### 1. Augmenter la limite d'upload (CRITIQUE)
```
Réglages → Média → Taille maximale d'upload
```
- Par défaut souvent limité à **2 MB** → TROP PETIT pour thème
- **Vérifier via PHP Info** : `Outils → Santé du site → Info → Server`
  - Chercher : `upload_max_filesize` et `post_max_size`
  - Si < 64M, contacter OVH pour augmenter ou créer `.user.ini`

#### 2. Vérifier version PHP
```
Outils → Santé du site → Info → Server → Version de PHP
```
- **Minimum requis :** PHP 7.4
- **Recommandé :** PHP 8.0+
- Si PHP < 7.4 : changer via panneau OVH

#### 3. Activer débogage temporairement
```
Apparence → Éditeur de fichier de thème → wp-config.php
```
Ajouter avant `/* C'est tout, ne touchez pas à ce qui suit ! */` :
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```
*(On désactivera après migration)*

#### 4. Désactiver tous les plugins par défaut
```
Extensions → Extensions installées → Tout désactiver
```
*(Pour éviter conflits lors import)*

---

## 📦 ÉTAPE 1 : EXPORT LOCAL (XAMPP)

### 1.1 Export du thème Mayami

**Méthode manuelle (recommandé) :**

1. **Ouvrir l'explorateur Windows :**
```
c:\xampp\htdocs\web-am\dev.tad\MyWebsites\mayami-wp\wp-content\themes\
```

2. **Clic droit sur dossier `mayami/` → Envoyer vers → Dossier compressé**
   - Windows crée automatiquement `mayami.zip`

3. **Vérifier la taille du ZIP :**
   - Si > 64 MB → Exclure `/visual-links-builder/exports-html/` (sauf Mayami-EPK/template/)
   - Créer ZIP manuel sans les gros exports

4. **Placer le ZIP sur le Bureau** pour import facile

**⚠️ ATTENTION - Dossier protégé :**
```
NE PAS MODIFIER : /visual-links-builder/exports-html/Mayami-EPK/template/
```

---

### 1.2 Export base de données SQL (OPTIONNEL mais recommandé)

**Deux approches possibles :**

#### Option A : Export complet (ÉCRASER WordPress OVH)
⚠️ **DANGER : efface TOUT le WordPress OVH actuel !**

**Via phpMyAdmin XAMPP :**
1. Ouvrir `http://localhost/phpmyadmin/`
2. Cliquer sur base `mayami_wp` (ou nom de ta base)
3. Onglet **"Exporter"**
4. Méthode : **"Rapide"**
5. Format : **SQL**
6. Clic **"Exécuter"**
7. Fichier téléchargé : `mayami_wp.sql`

#### Option B : Export sélectif (RECOMMANDÉ pour début)
**Exporter UNIQUEMENT les tables custom :**

1. Dans phpMyAdmin, **cocher UNIQUEMENT** :
   - Tables commençant par `wp_` ET contenant tes données custom
   - **Pages/Articles** : `wp_posts`, `wp_postmeta`
   - **Utilisateurs** : `wp_users`, `wp_usermeta` (si nécessaire)
   - **Options custom** : vérifier `wp_options` pour settings thème
   
2. Exporter ces tables seulement

**💡 CONSEIL :** Pour premier test, **NE PAS importer SQL**, juste le thème !

---

## 🌐 ÉTAPE 2 : IMPORT VERS OVH

### 2.1 Import du thème via WordPress

**Sur l'admin OVH :**

1. **Aller dans :**
```
Apparence → Thèmes → Ajouter
```

2. **Cliquer "Téléverser un thème"**

3. **Sélectionner `mayami.zip`**

4. **Cliquer "Installer maintenant"**

5. **Attendre la barre de progression** (peut prendre 30-60 sec)

6. **Si erreur "dépassement taille" :**
   - Retour Étape 0 : augmenter `upload_max_filesize`
   - OU upload via FTP (voir section suivante)

7. **Une fois installé : ACTIVER LE THÈME**
```
Apparence → Thèmes → Mayami → Activer
```

---

### 2.2 Import via FTP (Alternative si upload échoue)

**Si WordPress refuse le ZIP :**

1. **Demander accès FTP OVH** (via panneau OVH ou support)

2. **Ouvrir FileZilla :**
   - Host : `ftp.cluster100.hosting.ovh.net` (à confirmer)
   - Username : *(fourni par OVH)*
   - Password : *(fourni par OVH)*
   - Port : 21

3. **Naviguer vers :**
```
/www/wp-content/themes/
```

4. **Glisser-déposer le dossier décompressé `mayami/`** (pas le ZIP)

5. **Vérifier upload complet** (tous les fichiers visibles)

6. **Dans admin WordPress OVH :**
```
Apparence → Thèmes → Mayami apparaît → Activer
```

---

### 2.3 Import base SQL (OPTIONNEL - SI EXPORT FAIT)

**⚠️ FAIRE UN BACKUP DU WORDPRESS OVH AVANT !**

**Via phpMyAdmin OVH :**

1. **Accéder phpMyAdmin OVH** (depuis panneau hébergement)

2. **Sélectionner la base WordPress OVH**

3. **BACKUP ACTUEL :**
   - Onglet "Exporter" → Rapide → SQL → Exécuter
   - Sauvegarder le fichier `backup_ovh_avant_import.sql`

4. **Import de ta base XAMPP :**
   - Onglet "Importer"
   - Choisir fichier `mayami_wp.sql`
   - Cliquer "Exécuter"
   - ⏱️ Attendre (peut prendre plusieurs minutes)

5. **CRITIQUE : Modifier les URLs dans la base**

**Après import, OBLIGATOIRE exécuter SQL suivant :**
```sql
UPDATE wp_options 
SET option_value = 'http://ubocrhy.cluster100.hosting.ovh.net/wp' 
WHERE option_name = 'siteurl';

UPDATE wp_options 
SET option_value = 'http://ubocrhy.cluster100.hosting.ovh.net/wp' 
WHERE option_name = 'home';
```

**Puis chercher/remplacer URLs anciennes :**
```sql
-- Remplacer dans posts
UPDATE wp_posts 
SET post_content = REPLACE(post_content, 
  'http://localhost/mayami-wp', 
  'http://ubocrhy.cluster100.hosting.ovh.net/wp');

-- Remplacer dans post meta
UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 
  'http://localhost/mayami-wp', 
  'http://ubocrhy.cluster100.hosting.ovh.net/wp');
```

---

## ✅ ÉTAPE 3 : VÉRIFICATIONS POST-IMPORT

### 3.1 Vérifier le thème s'affiche

1. **Visiter la page d'accueil du site :**
```
http://ubocrhy.cluster100.hosting.ovh.net/wp/
```

2. **Vérifier :**
   - ✅ Thème Mayami est actif
   - ✅ Pas d'erreur affichée
   - ✅ Styles CSS chargés

---

### 3.2 Vérifier Visual Links Builder

1. **Dans admin OVH :**
```
Menu latéral → Chercher "Visual Links Builder"
```

2. **Si pas visible :**
   - Vérifier `functions.php` du thème
   - Fonction `mayami_admin_menu()` doit être présente
   - Vérifier logs PHP : `wp-content/debug.log`

3. **Si visible : cliquer pour ouvrir**

4. **Vérifier :**
   - ✅ Interface builder s'affiche
   - ✅ Pas d'erreur JavaScript console navigateur (F12)
   - ✅ Upload image fonctionne
   - ✅ Boutons fonctionnent

---

### 3.3 Vérifier exports existants

**Si exports HTML migrés :**

1. **Via FTP, vérifier présence :**
```
/www/wp-content/themes/mayami/visual-links-builder/exports-html/
```

2. **Tester liens exports** (si URLs conservées)

3. **⚠️ VÉRIFIER DOSSIER PROTÉGÉ intact :**
```
/www/wp-content/themes/mayami/visual-links-builder/exports-html/Mayami-EPK/template/
```

---

## 🐛 DÉPANNAGE RAPIDE

### Erreur "Le thème n'a pas de stylesheet"
**Cause :** ZIP mal formé ou fichier `style.css` manquant  
**Solution :** Vérifier que `style.css` contient bien :
```css
/*
Theme Name: Mayami
...
*/
```

---

### Erreur upload "dépassement taille maximale"
**Cause :** `upload_max_filesize` trop bas  
**Solution :** 
1. Contacter support OVH pour augmenter
2. OU créer `.user.ini` dans `/www/` :
```ini
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 300
```

---

### Visual Links Builder n'apparaît pas dans menu
**Cause :** Fonction admin non enregistrée  
**Solution :**
1. Vérifier `wp-content/themes/mayami/functions.php`
2. Chercher `add_action('admin_menu', 'mayami_admin_menu')`
3. Si absent : ajouter le code (voir MIGRATION-GUIDE.md section dépannage)

---

### Erreurs JavaScript console navigateur
**Cause :** Chemins fichiers incorrects  
**Solution :**
1. F12 → Onglet Console
2. Noter les fichiers en erreur 404
3. Vérifier chemins dans `visual-links-builder.html`
4. Corriger chemins relatifs si nécessaire

---

## 📝 CHECKLIST FINALE

Avant de considérer migration terminée :

- [ ] Thème Mayami activé sur OVH
- [ ] Page d'accueil s'affiche correctement
- [ ] Visual Links Builder accessible dans admin
- [ ] Interface builder fonctionne (upload, zones, export)
- [ ] Aucune erreur JavaScript console
- [ ] Aucune erreur PHP dans debug.log
- [ ] Exports HTML accessibles (si migrés)
- [ ] Dossier protégé Mayami-EPK/template/ intact
- [ ] CHANGELOG.md mis à jour avec statut migration

---

## 🎯 ORDRE D'EXÉCUTION RECOMMANDÉ

**Pour première migration test :**

1. ✅ **Étape 0** : Réglages WordPress OVH
2. ✅ **Étape 1.1** : Export thème ZIP
3. ✅ **Étape 2.1** : Import thème via admin WordPress
4. ✅ **Étape 3** : Vérifications complètes
5. ⏸️ **Étape 1.2 + 2.3** : Import SQL (OPTIONNEL - faire APRÈS si thème OK)

**Raison :** Tester d'abord le thème seul, puis ajouter la base SQL si nécessaire.

---

## 📞 SI BLOQUÉ

**Informations à fournir pour debug :**
1. Message d'erreur exact (copier-coller)
2. Étape en cours
3. Taille du fichier ZIP thème
4. Version PHP sur OVH (Outils → Santé du site)
5. Capture d'écran erreur si possible

---

**🚀 Bon courage pour la migration !**
