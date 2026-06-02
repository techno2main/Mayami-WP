# Changelog Migration OVH

## [En cours] 2026-06-02 - Préparation migration

### 🎯 Objectifs
- Migrer thème Mayami vers nouveau WordPress OVH
- Conserver Visual Links Builder fonctionnel
- Préserver exports existants

### 📦 Préparation
- [x] Branche Git `feature/migration-ovh` créée
- [x] Dossier `migration-ovh/` créé
- [x] Guide de migration rédigé
- [x] **URL OVH identifiée : `http://ubocrhy.cluster100.hosting.ovh.net/wp/wp-admin/`**
- [ ] Inventaire fichiers thème
- [ ] Archive ZIP créée
- [ ] Export base SQL XAMPP

### 🚀 Migration
- [ ] Upload FTP thème
- [ ] Activation thème WordPress OVH
- [ ] Configuration pages admin
- [ ] Tests fonctionnels

### ✅ Validation
- [ ] Thème s'affiche correctement
- [ ] Visual Links Builder fonctionne
- [ ] Exports fonctionnent
- [ ] Aucune erreur JavaScript/PHP

### 🐛 Problèmes rencontrés
- Aucun pour le moment

### 📌 Notes importantes
- ⚠️ NE JAMAIS TOUCHER : `/exports-html/Mayami-EPK/template/`
- Conserver backup local avant upload
- Tester sur environnement de staging si disponible

---

## Format pour futures entrées

```markdown
## YYYY-MM-DD - Titre de l'étape

### ✅ Réalisé
- Description action

### ⚙️ Configuration
- Paramètre modifié

### 🐛 Problèmes
- Problème + solution

### 📌 Notes
- Information importante
```
