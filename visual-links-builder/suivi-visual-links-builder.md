# Suivi EPK Builder (temps réel)

Dernière mise à jour: 2026-06-01
Objectif: reprise rapide en nouvelle session, avec état fiable des développements EPK Builder.

## 1) Vue d'ensemble
- EPK Builder est désormais un menu principal indépendant dans l'admin WordPress.
- Le builder utilise le fichier HTML standalone: `epk-builder/epk-map-creator.html`.
- La section EPK dans Mayami Landing a été masquée de la nav/listing landing (sans suppression de données).
- Un système de brouillons en base WordPress a été ajouté (nom + sauvegarde + chargement).

## 2) État fonctionnel actuel
### OK
- Création de zones cliquables sur le visuel (drag souris).
- Sauvegarde d'un brouillon EPK en base via AJAX.
- Chargement d'un brouillon existant.
- Bouton "Générer le code HTML" désactivé tant que l'état courant n'est pas sauvegardé en base.
- Renommage + déplacement du fichier HTML effectué: `epk-builder/epk-map-creator.html`.
- Footer WordPress admin masqué sur pages EPK.

### À vérifier côté instance serveur (post-upload)
- Sous-menu EPK Builder:
  - Pas de doublon "EPK Builder" dans les sous-entrées.
  - Présence de "New EPK".
  - Présence de chaque brouillon enregistré avec son nom.
- Ouverture des sous-entrées sans page blanche.
- Rechargement des brouillons multi-zones (cas réels, anciens brouillons inclus).

## 3) Problèmes rencontrés et causes probables
1. Menus dynamiques parfois non rendus comme attendu
- Cause: manipulation directe de `$submenu` parfois non fiable selon timing/hook/admin state.
- Correctif en cours: sous-menus enregistrés via `add_submenu_page` + callbacks directs.

2. Page blanche sur sous-menu brouillon / New EPK
- Cause: logique de redirection en callback admin.
- Correctif appliqué: callbacks qui rendent directement le builder (sans redirect).

3. Visuel absent à la réouverture
- Cause: `data:image/...` filtré à la sauvegarde.
- Correctif appliqué: acceptation des data URLs image dans la sanitization.

4. Zones incomplètes au chargement
- Cause: écart de dimensions d'affichage entre sauvegarde et relecture.
- Correctif appliqué: stockage `canvasWidth/canvasHeight` + remapping des coordonnées au chargement.

## 4) Fichiers clés EPK Builder
- `functions.php`
- `epk-builder/epk-map-creator.html`
- `assets/admin-nav.js` (masquage EPK dans Mayami Landing)

## 5) Journal des dernières actions
- [x] EPK Builder en menu principal indépendant.
- [x] Sous-dossier `epk-builder/` créé pour la documentation EPK.
- [x] Brouillons en base (save/load).
- [x] Nom de brouillon obligatoire.
- [x] Désactivation du bouton générer tant que non sauvegardé en base.
- [x] Correction drag souris (état bloqué).
- [x] Remapping des zones au chargement.
- [ ] Validation finale des sous-menus dynamiques sur serveur (UI réelle après upload).

## 6) Check de reprise rapide (nouvelle session)
1. Ouvrir l'admin > EPK Builder.
2. Vérifier sous-menu: "New EPK" + liste des brouillons nommés.
3. Cliquer un brouillon existant:
   - le visuel doit apparaître
   - toutes les zones attendues doivent être présentes
4. Modifier une zone:
   - statut "non sauvegardé"
   - bouton générer désactivé
5. Enregistrer le brouillon:
   - statut "sauvegardé"
   - bouton générer activé

## 7) Convention de suivi (temps réel)
À chaque changement EPK, mettre à jour ce fichier:
- date/heure
- fichiers modifiés
- résultat attendu
- statut validation (OK / KO)
- points à retester
