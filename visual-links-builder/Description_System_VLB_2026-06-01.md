# Récapitulatif complet - Module WP Visual Links Builder

Date de mise à jour: 2026-06-02
Périmètre: architecture WordPress, CMB2, administration des visuels, export HTML/Email, renommage EPK -> Visual Links, état des bugs.

## 1) Objectif du module

Le module Visual Links Builder permet de:
- charger un visuel,
- dessiner des zones cliquables,
- sauvegarder des brouillons en base,
- générer des templates exportables (Template-HTML et Template-Email),
- publier/retirer une version front depuis l’admin.

## 2) Architecture générale

### 2.1. Couches principales

1. Couche admin WordPress (menus/pages)
- menu top-level Visual Links Builder,
- sous-pages Nouveau visuel et Liste des visuels,
- iframe du builder HTML dans l’admin.

2. Couche builder front-end (fichier HTML autonome)
- UI d’édition des zones,
- sauvegarde AJAX,
- preview,
- export Template-HTML / Template-Email,
- purge des exports.

3. Couche serveur (PHP)
- endpoints AJAX de save/load/export/upload/purge,
- sanitation du payload,
- persistance des drafts en option WordPress.

4. Couche front public
- rendu du visuel + hotspots cliquables,
- logique preview privée admin (draft) vs version publiée.

## 3) Intégration CMB2 (gestion des visuels dans l’environnement WP)

### 3.1. Page options

Le système repose sur la page options CMB2 Mayami Landing.

Clé d’option active:
- mayami_landing_options

Compat legacy conservée:
- mayami_options (lecture fallback via helper)

### 3.2. Section Visual Links dans CMB2

La section est présente dans la config CMB2 avec des IDs de stockage hérités (conservés pour compatibilité des données):
- section_epk_title (label affiché renommé en Visual Links)
- epk_draft_image_source
- epk_builder (type custom rendu par le module)
- epk_validation_ready
- epk_draft_payload
- epk_published_payload

Important:
- Les labels UI ont été renommés Visual Links.
- Les IDs techniques epk_* sont maintenus volontairement pour éviter de casser les données existantes.

### 3.3. Rendu custom CMB2

Le champ custom de type builder est rendu par:
- cmb2_render_mayami_epk_builder

Le code de rendu est dans:
- inc/visual-links.php

Ce rendu inclut:
- panneau upload visuel,
- zones cliquables,
- statuts brouillon/publié/validation,
- actions preview/publish/unpublish.

## 4) Menu admin et navigation

### 4.1. Slugs actifs

Nouveaux slugs admin:
- mayami_visual_links_builder
- mayami_visual_links_builder_new
- mayami_visual_links_drafts

### 4.2. Compatibilité legacy

Les anciens slugs EPK restent redirigés automatiquement:
- mayami_epk_html_builder -> mayami_visual_links_builder
- mayami_epk_html_builder_new -> mayami_visual_links_builder_new
- mayami_epk_drafts -> mayami_visual_links_drafts

Objectif:
- ne pas casser les anciens favoris/liens d’admin.

## 5) Stockage des données

### 5.1. Store des brouillons Visual Links

Option store utilisée:
- mayami_epk_drafts_store

Structure type d’un draft:
- id
- name
- payload (imageUrl, zones, canvasWidth/Height, PDF fields)
- updated_at
- metadata d’export (template html/email + timestamps)

### 5.2. Payload front publié

Dans mayami_landing_options:
- epk_draft_payload (brouillon)
- epk_published_payload (publié)
- epk_validation_ready (garde-fou publication)

## 6) Endpoints et actions serveur

### 6.1. Brouillons

Actions principales:
- mayami_save_visual_links_draft (alias vers handler historique)
- mayami_get_visual_links_draft (alias vers handler historique)

Actions legacy maintenues:
- mayami_save_epk_draft
- mayami_get_epk_draft

### 6.2. Exports et assets email

Actions:
- mayami_export_visual_links_html (et alias legacy interne)
- mayami_upload_visual_links_slice
- mayami_purge_visual_export_bucket

### 6.3. Publication front

Actions admin_post (legacy conservé):
- mayami_publish_epk_draft
- mayami_unpublish_epk

## 7) Pipeline export (état implémenté)

### 7.1. Arborescence d’export

Dossier builder (renommé au pluriel):
- wp-content/themes/mayami/visual-links-builder

Exports:
- visual-links-builder/exports-html/<visuel>/Template-HTML
- visual-links-builder/exports-html/<visuel>/Template-Email
- visual-links-builder/exports-html/<visuel>/Template-Email/img

### 7.2. Séquence export

1. Purge bucket Template-HTML
2. Purge bucket Template-Email
3. Export Template-HTML
4. Génération Template-Email
- slices cliquables,
- upload des images slices,
- écriture HTML + TXT.

### 7.3. Moteur email actuel

Le moteur actif est basé sur slices cliquables (table HTML email-compatible), pas sur image map intégrée.

Raison:
- meilleure robustesse de cliquabilité dans Gmail, surtout mobile.

## 8) Pourquoi Gmail desktop affiche parfois un bloc YouTube

Ce bloc est un enrichissement Gmail (link preview card), pas un composant volontaire du layout du builder.

Cause probable:
- présence d’un lien YouTube détecté dans le contenu HTML/TXT du mail.

Effet:
- Gmail injecte une carte d’aperçu sous le contenu principal.

## 9) État des bugs connu

### 9.1. Confirmé

1. Gmail Desktop
- apparition possible d’un bloc preview YouTube non désiré.

2. Gmail Mobile
- rendu encore jugé dégradé,
- qualité visuelle et comportement des liens à confirmer sur tests finaux.

### 9.2. Non confirmé définitivement

- la dernière implémentation slices compile sans erreur, mais nécessite validation UX réelle finale sur clients email cibles.

## 10) Renommage EPK -> Visual Links déjà effectué

### 10.1. Nettoyage structurel fait

Fichiers EPK supprimés:
- inc/epk.php
- template-parts/sections/epk.php
- assets/epk.css

Nouveaux fichiers actifs:
- inc/visual-links.php
- template-parts/sections/visual-links.php
- assets/visual-links.css

Dossier builder renommé:
- visual-link-builder -> visual-links-builder

### 10.2. Compatibilité conservée

- alias/reirections legacy maintenus pour ne pas casser les liens/admin/actions historiques.
- clés de données epk_* conservées en base pour éviter migration risquée immédiate.

## 11) Fichiers cœur du module

### 11.1. Serveur / WP
- wp-content/themes/mayami/functions.php
- wp-content/themes/mayami/inc/cmb2-config.php
- wp-content/themes/mayami/inc/visual-links.php

### 11.2. Builder
- wp-content/themes/mayami/visual-links-builder/visual-links-builder.html

### 11.3. Front
- wp-content/themes/mayami/front-page.php
- wp-content/themes/mayami/template-parts/sections/visual-links.php
- wp-content/themes/mayami/assets/visual-links.css

## 12) Points de vigilance pour la suite

1. Phase 2 éventuelle (optionnelle)
- renommer les clés techniques epk_* en visual_links_* avec migration de données.

2. Validation email finale
- tests Gmail desktop/mobile,
- vérification cliquabilité des zones,
- vérification absence d’artefacts visuels,
- neutralisation des previews YouTube si exigé.

---

Ce document est la version de référence à date sur l’environnement WP Visual Links Builder, son architecture réelle et son état fonctionnel.