# Migration Thème Mayami vers WordPress OVH

**Branche Git :** `feature/migration-ovh`  
**Date :** 2 juin 2026

## 📚 Documents disponibles

### 1. MIGRATION-GUIDE.md
**Guide complet étape par étape** pour la migration du thème Mayami vers OVH.
- Prérequis et accès nécessaires
- 7 étapes détaillées de migration
- Troubleshooting et dépannage
- Checklist de validation finale

👉 **Commencer par lire ce document**

### 2. CHANGELOG.md
**Journal des modifications** de la migration.
- Suivi de progression
- Problèmes rencontrés et solutions
- Notes importantes

👉 **Mettre à jour après chaque étape**

### 3. Fichiers à créer pendant la migration

**Inventaires (générés automatiquement) :**
- `inventaire-fichiers-theme.txt` - Liste complète des fichiers du thème
- `inventaire-plugins.txt` - Liste des plugins WordPress requis

**Sauvegardes :**
- `backup-mayami-theme-YYYYMMDD.zip` - Archive du thème avant migration
- `backup-database-YYYYMMDD.sql` - Export base de données (si migration complète)

**Scripts SQL (si nécessaire) :**
- `sql/create-tables.sql` - Création tables custom
- `sql/import-data.sql` - Import données Visual Links

## 🚀 Démarrage rapide

### Étape 1 : Lecture préalable
```bash
# Lire le guide de migration
cat MIGRATION-GUIDE.md
```

### Étape 2 : Inventaire
```powershell
# Depuis PowerShell - Lister fichiers thème
Get-ChildItem -Recurse ..\* | Where-Object {!$_.PSIsContainer} | Select-Object FullName > inventaire-fichiers-theme.txt
```

### Étape 3 : Créer archive
```powershell
# Créer ZIP du thème
cd ..
Compress-Archive -Path "mayami" -DestinationPath "migration-ovh\backup-mayami-theme-20260602.zip"
```

### Étape 4 : Suivre le guide
Ouvrir `MIGRATION-GUIDE.md` et suivre les 7 étapes.

## ⚠️ Règles de sécurité

### INTERDICTION ABSOLUE
**NE JAMAIS TOUCHER au dossier :**
```
/var/www/ellenemasri/wp-content/themes/mayami/visual-links-builder/exports-html/Mayami-EPK/template/
```

Ce dossier contient des exports critiques qui ne doivent JAMAIS être modifiés ou supprimés.

### Bonnes pratiques
- ✅ Toujours créer un backup AVANT toute migration
- ✅ Tester sur environnement de staging si possible
- ✅ Conserver copies locales des fichiers
- ✅ Documenter chaque modification dans CHANGELOG.md
- ✅ Vérifier permissions fichiers après upload (755/644)

## 📞 Support

### Problèmes courants
Consulter section "ÉTAPE 6 : DÉPANNAGE" dans MIGRATION-GUIDE.md

### Logs à consulter
- **WordPress debug :** `/wp-content/debug.log`
- **PHP errors OVH :** `/www/logs/error.log`
- **Console navigateur :** F12 (erreurs JavaScript)

### Ressources externes
- [Documentation WordPress](https://wordpress.org/documentation/)
- [Documentation OVH Hébergement](https://docs.ovh.com/fr/hosting/)
- [FileZilla Guide](https://wiki.filezilla-project.org/)

## 📅 Statut migration

**Dernière mise à jour :** 2 juin 2026

| Étape | Description | Statut |
|-------|-------------|--------|
| 1 | Inventaire composants | ⏳ TODO |
| 2 | Préparation package | ⏳ TODO |
| 3 | Upload FTP | ⏳ TODO |
| 4 | Configuration WordPress | ⏳ TODO |
| 5 | Tests post-migration | ⏳ TODO |
| 6 | Dépannage | ⏳ TODO |
| 7 | Documentation finale | ⏳ TODO |

**Légende :**
- ⏳ TODO - À faire
- 🔄 EN COURS - En cours de réalisation
- ✅ TERMINÉ - Complété avec succès
- ❌ BLOQUÉ - Nécessite intervention

---

**Bonne migration ! 🚀**
