# SUIVI REFONTE VLB — TEMPS RÉEL
**Date création** : 2 juin 2026  
**Statut projet** : EN COURS — PHASE 1  
**Version** : 1.0

---

## État actuel
- **Phase en cours** : Phase 1 - Migration EPK → VLB
- **Sous-étape en cours** : Initialisation projet
- **Statut** : En cours
- **Date/heure mise à jour** : 2 juin 2026 - Début Phase 1

## Objectif sous-étape
- **Description** : Créer infrastructure projet (document suivi, branche Git)
- **Périmètre traité** : Setup initial avant migration code

## Fichiers modifiés
- `SUIVI_REFONTE_VLB.md` (création)
- Branche Git à créer

## Impacts et risques
- **Impacts potentiels** : Aucun (setup initial)
- **Risques identifiés** : Aucun

## Problèmes rencontrés
- Aucun pour l'instant

## État VSC/Problems
- **Erreurs** : À vérifier après setup
- **Warnings** : À vérifier après setup
- **Nature erreurs principales** : N/A
- **Statut** : Initial
- **Justification** : Setup initial

## Décision
- **GO / NO-GO pour suite** : En attente création branche Git
- **Rollback possible** : N/A (rien modifié encore)
- **Reste à faire** : Créer branche, analyser périmètre migration

## État Git
- **Branche active** : feature/vlb-refonte-v2
- **Dernier commit** : 35302b9
- **Message commit** : feat: mise à jour de la documentation du module WP Visual Links Builder
- **Point de rollback** : 35302b9 (commit stable dev avant démarrage refonte)

---

## Journal chronologique

### 2 juin 2026
- **[Début]** Création document suivi SUIVI_REFONTE_VLB.md
- **[OK]** Création branche feature/vlb-refonte-v2 (point rollback: 35302b9)
- **[En cours]** Phase 1.1 - Analyse périmètre migration EPK→VLB

---

## Points de validation client

- [ ] **Fin Phase 1** : Migration EPK→VLB complète et validée
- [ ] **Fin Phase 2** : Moteur image-map implémenté et testé
- [ ] **Fin Phase 3** : Fallback robuste repensé et validé
- [ ] **Décision finale** : Choix image-map vs fallback après tests multi-clients

---

## Incidents et résolutions

_Aucun incident pour l'instant_

---

## Mapping Migration EPK → VLB (Référence)

### Options WordPress
- `epk_draft_payload` → `visual_links_draft_payload`
- `epk_published_payload` → `visual_links_published_payload`
- `epk_validation_ready` → `visual_links_validation_ready`

### Fonctions PHP
- `mayami_*_epk_*` → `mayami_*_visual_links_*`

### Actions & Hooks
- `admin_post_mayami_publish_epk_draft` → `admin_post_mayami_publish_visual_links_draft`
- `wp_ajax_mayami_export_epk_html` → `wp_ajax_mayami_export_visual_links_html`

### Nonces
- `mayami_epk_preview` → `mayami_visual_links_preview`

### IDs CMB2
- `epk_draft_payload` → `visual_links_draft_payload`

### Classes CSS
- `.mayami-epk-*` → `.mayami-vlb-*`

### Attributs data
- `[data-epk-*]` → `[data-vlb-*]`

### Paramètres GET
- `mayami_preview=epk` → `mayami_preview=visual_links`

---

## Phases du projet

### ✅ Phase 0 : Setup (EN COURS)
- [x] Création document suivi
- [x] Création branche Git feature/vlb-refonte-v2
- [x] Identification commit rollback (35302b9)

### ⏳ Phase 1 : Migration EPK → VLB
- [ ] Analyse périmètre complet (~300 occurrences)
- [ ] Script migration ONE-TIME
- [ ] Migration options WordPress
- [ ] Migration fonctions PHP
- [ ] Migration hooks/AJAX
- [ ] Migration classes CSS/attributs data
- [ ] Tests workflow draft→preview→publish
- [ ] Validation VSC/Problems
- [ ] **ARRÊT OBLIGATOIRE - Validation client**

### ⏸️ Phase 2 : Moteur image-map
_En attente validation Phase 1_

### ⏸️ Phase 3 : Fallback robuste
_En attente validation Phase 2_

### ⏸️ Phase 4 : Mitigation YouTube
_En attente validation Phase 3_

### ⏸️ Phase 5 : Logs/métadonnées
_En attente validation Phase 4_

### ⏸️ Phase 6 : Tests multi-clients
_En attente validation Phase 5_

---

**Prochaine action** : Créer branche Git feature/vlb-refonte-v2
