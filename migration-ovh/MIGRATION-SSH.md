# 🚀 Migration OVH via SSH - Guide Complet

**Date :** 2 juin 2026  
**URL OVH :** http://ubocrhy.cluster100.hosting.ovh.net/wp/wp-admin/  
**Méthode :** SSH + SCP (beaucoup plus rapide que FTP/interface web)

---

## 📋 PRÉREQUIS SSH

### Informations de connexion nécessaires

- **Host SSH :** `________` (ex: `ssh.cluster100.hosting.ovh.net` ou IP)
- **Username :** `________`
- **Port SSH :** `________` (généralement `22`)
- **Mot de passe :** `________` (ou clé SSH si configurée)

---

## ✅ ÉTAPE 0 : Test connexion SSH

### Depuis PowerShell local

```powershell
# Test connexion SSH
ssh utilisateur@ssh.cluster100.hosting.ovh.net -p 22

# Si demande confirmation fingerprint → taper "yes"
# Entrer mot de passe

# Une fois connecté, vérifier où tu es :
pwd
# Devrait afficher quelque chose comme : /homez.XXX/utilisateur/

# Lister fichiers
ls -la

# Chercher dossier WordPress
ls -la www/
# OU
ls -la public_html/
```

---

## 🔍 ÉTAPE 1 : Identifier l'arborescence WordPress OVH

### Commandes de diagnostic

```bash
# Connexion SSH
ssh utilisateur@host -p 22

# Trouver le dossier WordPress
find ~/ -name "wp-config.php" -type f 2>/dev/null

# Exemple de résultats possibles :
# /homez.XXX/utilisateur/www/wp-config.php
# /homez.XXX/utilisateur/public_html/wp/wp-config.php

# Une fois trouvé, aller dans ce dossier
cd /chemin/vers/wordpress/

# Vérifier structure
ls -la wp-content/themes/
# Devrait montrer les thèmes par défaut (twentytwentyfour, etc.)
```

---

## 🐘 ÉTAPE 2 : Vérifier configuration PHP

### Version PHP et limites

```bash
# Vérifier version PHP
php -v
# OU si plusieurs versions installées :
php8.1 -v
php8.0 -v

# Vérifier limites upload
php -r "phpinfo();" | grep -E "upload_max_filesize|post_max_size|memory_limit"

# Exemple sortie :
# upload_max_filesize => 64M
# post_max_size => 64M
# memory_limit => 128M
```

### Si limites trop basses

**Créer fichier `.user.ini` dans racine WordPress :**

```bash
cd /chemin/vers/wordpress/

cat > .user.ini << EOF
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 300
memory_limit = 256M
EOF

# Vérifier création
cat .user.ini
```

**Note :** Modifications `.user.ini` prennent effet après ~5 minutes.

---

## 📦 ÉTAPE 3 : Upload thème Mayami (3 méthodes)

### Méthode A : SCP (Simple Copy - RECOMMANDÉ)

**Depuis PowerShell LOCAL (PAS en SSH) :**

```powershell
# 1. Créer ZIP du thème localement
cd "c:\xampp\htdocs\web-am\dev.tad\MyWebsites\mayami-wp\wp-content\themes\"
Compress-Archive -Path "mayami" -DestinationPath "$env:USERPROFILE\Desktop\mayami.zip" -Force

# 2. Copier ZIP vers serveur OVH via SCP
scp -P 22 "$env:USERPROFILE\Desktop\mayami.zip" utilisateur@ssh.cluster100.hosting.ovh.net:~/

# Entrer mot de passe SSH
# Attendre upload (affiche progression)
```

**Ensuite, sur serveur SSH OVH :**

```bash
# Se connecter SSH
ssh utilisateur@host -p 22

# Vérifier ZIP uploadé
ls -lh ~/mayami.zip

# Aller dans dossier themes WordPress
cd /chemin/vers/wordpress/wp-content/themes/

# Décompresser ZIP
unzip ~/mayami.zip

# Vérifier extraction
ls -la mayami/
# Devrait montrer : functions.php, style.css, visual-links-builder/, etc.

# Définir permissions correctes
chown -R utilisateur:utilisateur mayami/
chmod -R 755 mayami/
chmod 644 mayami/style.css mayami/functions.php

# Nettoyer ZIP temporaire
rm ~/mayami.zip
```

---

### Méthode B : rsync (Synchronisation directe - AVANCÉ)

**Depuis PowerShell LOCAL (nécessite WSL ou rsync Windows) :**

```bash
# Si rsync disponible (via WSL ou Cygwin)
rsync -avz --progress -e "ssh -p 22" \
  "/mnt/c/xampp/htdocs/web-am/dev.tad/MyWebsites/mayami-wp/wp-content/themes/mayami/" \
  utilisateur@ssh.cluster100.hosting.ovh.net:/chemin/vers/wordpress/wp-content/themes/mayami/

# Avantages :
# - Plus rapide (upload incrémental)
# - Pas de compression/décompression
# - Conserve permissions
```

---

### Méthode C : Git pull (SI repo Git accessible)

**Sur serveur SSH OVH :**

```bash
cd /chemin/vers/wordpress/wp-content/themes/

# Cloner branche migration
git clone -b feature/migration-ovh https://github.com/ton-repo/mayami.git

# OU si déjà cloné, pull
cd mayami/
git pull origin feature/migration-ovh
```

**⚠️ Problème :** Dossier `.git/` inutile en production + expose historique.

---

## 🗄️ ÉTAPE 4 : Export/Import base de données

### Export depuis XAMPP (LOCAL)

**Via phpMyAdmin (interface graphique) :**

1. `http://localhost/phpmyadmin/`
2. Sélectionner base WordPress
3. Exporter → Rapide → SQL
4. Sauvegarder fichier : `mayami_wp.sql`

**Via ligne de commande (PowerShell LOCAL - PLUS RAPIDE) :**

```powershell
# Depuis PowerShell local
cd "c:\xampp\mysql\bin\"

# Export complet
.\mysqldump.exe -u root -p mayami_wp > "$env:USERPROFILE\Desktop\mayami_wp.sql"

# Entrer mot de passe MySQL (souvent vide sur XAMPP)
# Fichier créé sur Bureau : mayami_wp.sql
```

---

### Upload SQL vers serveur OVH

**Via SCP (PowerShell LOCAL) :**

```powershell
scp -P 22 "$env:USERPROFILE\Desktop\mayami_wp.sql" utilisateur@ssh.cluster100.hosting.ovh.net:~/
```

---

### Import dans MySQL OVH

**Sur serveur SSH OVH :**

```bash
# Récupérer infos MySQL depuis wp-config.php
cd /chemin/vers/wordpress/
cat wp-config.php | grep -E "DB_NAME|DB_USER|DB_PASSWORD|DB_HOST"

# Exemple sortie :
# define('DB_NAME', 'nom_base_ovh');
# define('DB_USER', 'user_ovh');
# define('DB_PASSWORD', 'motdepasse');
# define('DB_HOST', 'mysql5-XXX.pro');

# BACKUP base actuelle OVH AVANT import (CRUCIAL)
mysqldump -h mysql5-XXX.pro -u user_ovh -p nom_base_ovh > backup_ovh_avant_import.sql
# Entrer mot de passe MySQL OVH

# Import base XAMPP
mysql -h mysql5-XXX.pro -u user_ovh -p nom_base_ovh < ~/mayami_wp.sql
# Entrer mot de passe MySQL OVH
# Attendre import (peut prendre 1-5 min selon taille)

# ⚠️ CRUCIAL : Modifier URLs dans base
mysql -h mysql5-XXX.pro -u user_ovh -p nom_base_ovh << EOF
UPDATE wp_options 
SET option_value = 'http://ubocrhy.cluster100.hosting.ovh.net/wp' 
WHERE option_name IN ('siteurl', 'home');

UPDATE wp_posts 
SET post_content = REPLACE(post_content, 
  'http://localhost/mayami-wp', 
  'http://ubocrhy.cluster100.hosting.ovh.net/wp');

UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 
  'http://localhost/mayami-wp', 
  'http://ubocrhy.cluster100.hosting.ovh.net/wp');
EOF
```

---

## ✅ ÉTAPE 5 : Activation thème via SSH

### Méthode 1 : Via WP-CLI (SI DISPONIBLE)

```bash
# Vérifier si WP-CLI installé
wp --info

# Si disponible :
cd /chemin/vers/wordpress/

# Lister thèmes
wp theme list

# Activer Mayami
wp theme activate mayami

# Vérifier activation
wp theme list | grep mayami
# Devrait montrer "active"
```

---

### Méthode 2 : Via MySQL direct

```bash
# Si WP-CLI indisponible, modifier base directement
mysql -h mysql5-XXX.pro -u user_ovh -p nom_base_ovh -e \
  "UPDATE wp_options SET option_value = 'mayami' WHERE option_name = 'template'; \
   UPDATE wp_options SET option_value = 'mayami' WHERE option_name = 'stylesheet';"
```

---

### Méthode 3 : Via interface WordPress (fallback)

```
http://ubocrhy.cluster100.hosting.ovh.net/wp/wp-admin/
→ Apparence → Thèmes → Mayami → Activer
```

---

## 🔍 ÉTAPE 6 : Vérifications post-migration

### Vérifier thème installé

```bash
# Sur SSH OVH
cd /chemin/vers/wordpress/wp-content/themes/mayami/

# Lister fichiers
ls -la

# Vérifier fichiers critiques
ls -lh style.css functions.php
ls -lh visual-links-builder/visual-links-builder.html

# Vérifier permissions
# Fichiers : 644 (rw-r--r--)
# Dossiers : 755 (rwxr-xr-x)
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

---

### Vérifier logs erreurs

```bash
# Activer debug WordPress temporairement
cd /chemin/vers/wordpress/

# Backup wp-config.php
cp wp-config.php wp-config.php.backup

# Éditer wp-config.php (avec nano ou vi)
nano wp-config.php

# Ajouter AVANT la ligne /* C'est tout, ne touchez pas à ce qui suit ! */ :
# define('WP_DEBUG', true);
# define('WP_DEBUG_LOG', true);
# define('WP_DEBUG_DISPLAY', false);

# Sauvegarder (Ctrl+X, Y, Enter)

# Visiter site WordPress dans navigateur
# Puis vérifier logs :
tail -f wp-content/debug.log
# (Ctrl+C pour quitter)
```

---

### Test Visual Links Builder

```bash
# Vérifier dossier VLB
ls -la /chemin/vers/wordpress/wp-content/themes/mayami/visual-links-builder/

# Vérifier exports
ls -la /chemin/vers/wordpress/wp-content/themes/mayami/visual-links-builder/exports-html/

# Permissions exports (doit être writable pour PHP)
chmod 755 visual-links-builder/exports-html/
```

---

## 🐛 DÉPANNAGE SSH

### Erreur "Permission denied"

```bash
# Vérifier utilisateur actuel
whoami

# Vérifier ownership dossiers
ls -la /chemin/vers/wordpress/wp-content/

# Corriger ownership si nécessaire
chown -R utilisateur:utilisateur /chemin/vers/wordpress/wp-content/themes/mayami/
```

---

### Erreur "unzip: command not found"

```bash
# Alternative : utiliser jar (Java)
jar xf mayami.zip

# OU installer unzip (si droits sudo - rare sur mutualisé)
# Demander à support OVH
```

---

### Erreur connexion MySQL "Access denied"

```bash
# Vérifier credentials dans wp-config.php
cat wp-config.php | grep DB_

# Tester connexion MySQL
mysql -h mysql5-XXX.pro -u user_ovh -p
# Entrer mot de passe
# Si connexion OK : show databases;
# Exit : exit
```

---

## 📝 WORKFLOW COMPLET (Ordre recommandé)

### Phase 1 : Préparation (LOCAL)

```powershell
# PowerShell Windows
cd "c:\xampp\htdocs\web-am\dev.tad\MyWebsites\mayami-wp\wp-content\themes\"

# 1. Créer ZIP thème
Compress-Archive -Path "mayami" -DestinationPath "$env:USERPROFILE\Desktop\mayami.zip" -Force

# 2. Export base SQL
cd "c:\xampp\mysql\bin\"
.\mysqldump.exe -u root -p mayami_wp > "$env:USERPROFILE\Desktop\mayami_wp.sql"
```

---

### Phase 2 : Upload (SCP depuis LOCAL)

```powershell
# Upload thème
scp -P 22 "$env:USERPROFILE\Desktop\mayami.zip" utilisateur@ssh.cluster100.hosting.ovh.net:~/

# Upload SQL
scp -P 22 "$env:USERPROFILE\Desktop\mayami_wp.sql" utilisateur@ssh.cluster100.hosting.ovh.net:~/
```

---

### Phase 3 : Installation (SSH sur OVH)

```bash
# Connexion SSH
ssh utilisateur@ssh.cluster100.hosting.ovh.net -p 22

# 1. Identifier dossier WordPress
find ~/ -name "wp-config.php" -type f 2>/dev/null
# Résultat ex: /homez.XXX/utilisateur/www/wp-config.php

# 2. Extraire thème
cd /homez.XXX/utilisateur/www/wp-content/themes/
unzip ~/mayami.zip
chmod -R 755 mayami/

# 3. Import SQL
cd /homez.XXX/utilisateur/www/
cat wp-config.php | grep -E "DB_NAME|DB_USER|DB_PASSWORD|DB_HOST"
# Noter les valeurs

# Backup base actuelle
mysqldump -h DB_HOST -u DB_USER -p DB_NAME > backup_ovh.sql

# Import nouvelle base
mysql -h DB_HOST -u DB_USER -p DB_NAME < ~/mayami_wp.sql

# Modifier URLs
mysql -h DB_HOST -u DB_USER -p DB_NAME -e "
UPDATE wp_options SET option_value = 'http://ubocrhy.cluster100.hosting.ovh.net/wp' WHERE option_name IN ('siteurl', 'home');
"

# 4. Activer thème
mysql -h DB_HOST -u DB_USER -p DB_NAME -e "
UPDATE wp_options SET option_value = 'mayami' WHERE option_name IN ('template', 'stylesheet');
"

# 5. Nettoyer fichiers temporaires
rm ~/mayami.zip ~/mayami_wp.sql
```

---

### Phase 4 : Vérification (Navigateur + SSH)

```bash
# Sur SSH : vérifier logs
tail -f /chemin/vers/wordpress/wp-content/debug.log

# Dans navigateur :
# http://ubocrhy.cluster100.hosting.ovh.net/wp/
# http://ubocrhy.cluster100.hosting.ovh.net/wp/wp-admin/
```

---

## 🎯 COMMANDES RAPIDES (Copier-coller)

### Test connexion SSH

```bash
ssh utilisateur@ssh.cluster100.hosting.ovh.net -p 22
pwd
ls -la
```

---

### Trouver WordPress

```bash
find ~/ -name "wp-config.php" -type f 2>/dev/null
```

---

### Upload + Install thème (complet)

```bash
# LOCAL (PowerShell)
Compress-Archive -Path "c:\xampp\htdocs\web-am\dev.tad\MyWebsites\mayami-wp\wp-content\themes\mayami" -DestinationPath "$env:USERPROFILE\Desktop\mayami.zip" -Force
scp -P 22 "$env:USERPROFILE\Desktop\mayami.zip" user@host:~/

# OVH (SSH)
cd /chemin/vers/wp/wp-content/themes/
unzip ~/mayami.zip
chmod -R 755 mayami/
rm ~/mayami.zip
```

---

## ⚠️ RAPPEL SÉCURITÉ

**DOSSIER INTERDIT - NE JAMAIS MODIFIER :**

```bash
# Sur OVH SSH, VÉRIFIER que ce dossier existe et est intact :
ls -la /chemin/vers/wp/wp-content/themes/mayami/visual-links-builder/exports-html/Mayami-EPK/template/

# Si ce dossier contient des fichiers → NE RIEN TOUCHER !
```

---

## 📞 INFORMATIONS NÉCESSAIRES

**Pour commencer, fournis-moi :**

1. **Host SSH :** `________`
2. **Username SSH :** `________`
3. **Port SSH :** `________` (probablement 22)
4. **Mot de passe :** `________` (ou indique si clé SSH)

**Je te donnerai les commandes exactes à exécuter !**

---

## 🚀 AVANTAGES SSH vs Interface Web

✅ **Plus rapide** : Upload direct, pas de limite interface web  
✅ **Plus fiable** : Pas de timeout navigateur  
✅ **Plus de contrôle** : Permissions, ownership, logs en temps réel  
✅ **Débogage facile** : Accès direct aux logs, fichiers, base  
✅ **Automatisable** : Script complet possible  

---

**Prêt à commencer ? Fournis-moi les infos SSH et je te guide pas à pas ! 🎯**
