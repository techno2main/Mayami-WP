# Plan d'action - Mise en ligne de mayami.ellenemasri.com

## Hypothèses

- Le DNS de ellenemasri.com reste géré chez Wix.
- Le site principal ellenemasri.com doit rester inchangé sur Wix.
- La landing WordPress existe déjà et fonctionne sur un domaine technique (lié à ellenemasri.pro).
- Objectif cible: exposer cette même instance sur mayami.ellenemasri.com comme URL publique canonique.
- Tu veux une bascule propre, sans exécution immédiate.

## Prérequis

- Accès Wix: zone DNS du domaine ellenemasri.com.
- Accès hébergeur WordPress: panneau domaine, SSL, logs, redirections.
- Accès admin WordPress: réglages généraux, plugin SEO, cache/CDN.
- Inventaire technique minimal avant action:
- Type d'hébergement (mutualisé cPanel/Plesk, managé WP, VPS, PaaS).
- Valeur DNS à utiliser fournie par l'hébergeur (target CNAME ou IP A).
- Méthode SSL supportée (auto Let's Encrypt, manuel, DNS-01, HTTP-01).
- Possibilité d'attacher un domaine/sous-domaine custom à l'installation existante.
- Politique de redirection automatique éventuelle vers le domaine technique actuel.

## Plan d'action détaillé

1. Audit initial (sans changement)
- Confirmer URL actuelle de prod technique et comportement.
- Identifier plugins pouvant forcer les URLs (SEO, cache, sécurité, redirection).
- Vérifier présence d'URLs absolues vers l'ancien domaine dans contenus, médias, menus, scripts.
- Acteur responsable: Moi + WordPress
- Risque: Faible

2. Préparation hébergeur WordPress
- Ajouter mayami.ellenemasri.com comme domaine/sous-domaine additionnel dans le panneau.
- Définir ce host sur la même installation WordPress.
- Demander/obtenir la valeur DNS officielle (CNAME cible ou IP A).
- Vérifier qu'aucune redirection automatique vers ellenemasri.pro n'est active.
- Acteur responsable: Hébergeur + Moi
- Risque: Élevé

3. Préparation SSL
- Déclencher la procédure SSL prévue par l'hébergeur.
- Si SSL dépend de la propagation DNS, préparer la séquence pour éviter trou HTTPS.
- Confirmer certificat prévu pour mayami.ellenemasri.com uniquement (ou SAN/wildcard compatible).
- Acteur responsable: Hébergeur
- Risque: Élevé

4. Configuration DNS chez Wix
- Créer l'enregistrement du sous-domaine mayami selon la consigne host:
- CNAME mayami -> cible fournie, ou
- A mayami -> IP fournie (ajouter AAAA si fourni).
- Ne pas toucher aux enregistrements du domaine racine et du site Wix principal.
- Mettre TTL raisonnable (souvent 300 à 3600 selon politique).
- Acteur responsable: Moi + Wix
- Risque: Moyen

5. Vérification propagation et résolution
- Contrôler résolution DNS depuis plusieurs réseaux.
- Vérifier que mayami pointe bien vers la cible host attendue.
- Attendre stabilisation avant bascule applicative.
- Acteur responsable: Moi
- Risque: Moyen

6. Validation HTTPS et host routing
- Tester accès HTTPS sur mayami.ellenemasri.com.
- Vérifier certificat valide, chaîne correcte, pas d'erreur de nom.
- Vérifier que la bonne instance WordPress répond (pas page par défaut hébergeur).
- Acteur responsable: Moi + Hébergeur
- Risque: Élevé

7. Backup / Rollback plan (avant toute bascule)
- Sauvegarde fichiers complète (thème, plugins, uploads, configs serveur accessibles).
- Sauvegarde base de données complète (dump SQL horodaté et restaurable).
- Export des réglages critiques (URLs WP, permaliens, plugin SEO, redirections, cache/CDN, robots).
- Procédure de retour arrière documentée et testée sur le papier:
- Revenir DNS sur valeur précédente si incident.
- Restaurer base + fichiers de la version pré-bascule.
- Revalider login admin, page d'accueil, SSL et liens clés.
- Acteur responsable: Moi + Hébergeur + WordPress
- Risque: Élevé

8. Bascule WordPress canonique
- Définir URL du site et URL WordPress sur mayami.ellenemasri.com.
- Purger cache plugin, cache serveur, CDN éventuel.
- Régénérer permaliens.
- Mettre à jour canonicals/sitemaps dans le plugin SEO.
- Vérifier robots et indexation selon stratégie de lancement.
- Acteur responsable: Moi + WordPress
- Risque: Élevé

9. Nettoyage liens absolus et médias
- Contrôler assets CSS/JS/images: aucune référence cassée vers ancien domaine.
- Corriger liens internes absolus restants.
- Vérifier absence de mixed content (http non sécurisé).
- Acteur responsable: Moi + WordPress
- Risque: Moyen

10. Login/Admin et cookies
- Tester connexion admin via mayami.ellenemasri.com/wp-admin.
- Vérifier que session reste stable (pas boucle login).
- Éviter usage alterné ancien domaine/nouveau domaine pour admin.
- Valider upload média, éditeur, previews.
- Acteur responsable: Moi + WordPress
- Risque: Élevé

11. Redirections et cohérence finale
- Décider du rôle de ellenemasri.pro:
- Domaine technique privé (recommandé), non indexé.
- Option: redirection 301 publique vers mayami si exposé.
- Vérifier qu'aucune redirection involontaire ne renvoie vers l'ancien host.
- Contrôles finaux multi-device et navigation complète.
- Acteur responsable: Moi + Hébergeur + WordPress
- Risque: Élevé

12. Post-bascule J+1/J+2
- Re-vérifier SSL, erreurs 404, logs serveur, erreurs console navigateur.
- Vérifier formulaires, pixels, analytics, events.
- Surveiller performances et erreurs applicatives.
- Acteur responsable: Moi + WordPress
- Risque: Moyen

## Blocages éventuels avec l'hébergement

- Impossibilité d'ajouter mayami.ellenemasri.com comme domaine custom.
- Nécessité d'upgrade de plan pour domaine additionnel ou SSL.
- Pas d'IP/vhost correct pour le nouveau host.
- SSL impossible tant que DNS non propagé ou challenge bloqué.
- Redirection forcée au niveau plateforme vers ellenemasri.pro.
- WordPress configuré en dur sur ancien domaine (options, plugin, constantes).
- Médias/assets en URLs absolues cassées après bascule.
- Mixed content si certains liens restent en http.
- Cookies/admin instables si alternance de domaines.
- Reverse proxy de l'hébergeur non compatible host additionnel.
- Panneau restreint: pas d'accès règles Apache/Nginx nécessaires.
- CDN/caching avec host whitelist non mise à jour.
- Conflit SEO si deux domaines servent le même contenu sans canonical clair.

## Stratégie recommandée

- Conserver ellenemasri.pro comme environnement technique interne.
- Définir mayami.ellenemasri.com comme URL canonique publique unique.
- Utiliser majoritairement l'admin sur mayami.ellenemasri.com/wp-admin après bascule.
- Éviter exploitation quotidienne de l'admin sur l'ancien domaine pour limiter conflits cookies/URLs.
- Garder le site principal ellenemasri.com sur Wix sans le toucher.
- Appliquer redirection ou protection de l'ancien domaine public pour éviter duplication.

Pourquoi cette stratégie minimise la casse:

- Un seul host public canonique.
- Cohérence SEO/canonical/sitemap.
- Moins de confusion login/cookies.
- Migration progressive possible sans déplacer le site principal Wix.

## Questions à poser à l'hébergeur

Bonjour,
Je souhaite attacher le sous-domaine mayami.ellenemasri.com à mon installation WordPress existante actuellement accessible via un domaine technique.
Merci de me confirmer les points suivants:

1. Puis-je attacher mayami.ellenemasri.com à cette installation WordPress existante ?
2. Dois-je créer un CNAME ou un A record chez Wix ?
3. Quelle est la valeur DNS exacte à renseigner (target CNAME ou IP) ?
4. Faut-il déclarer le sous-domaine dans votre panneau avant propagation DNS ?
5. Le certificat SSL Let's Encrypt pour mayami.ellenemasri.com sera-t-il généré automatiquement ?
6. Quelles sont les conditions de génération SSL (propagation DNS, challenge HTTP, délais) ?
7. Y a-t-il des contraintes si le site existe déjà sur un domaine technique lié à ellenemasri.pro ?
8. Pouvez-vous garantir qu'il n'y aura pas de redirection forcée vers l'ancien domaine ?
9. Y a-t-il des restrictions de plan concernant domaine additionnel, SSL, ou vhost ?
10. En cas de besoin, pouvez-vous appliquer les règles serveur nécessaires (Apache/Nginx) pour que mayami soit l'host principal public ?

## Checklist finale

- [ ] Accès Wix et hébergeur validés.
- [ ] Type d'hébergement et capacités domaine custom confirmés.
- [ ] Valeur DNS exacte obtenue auprès de l'hébergeur.
- [ ] mayami.ellenemasri.com déclaré côté hébergeur.
- [ ] Stratégie SSL confirmée et prérequis connus.
- [ ] Enregistrement DNS créé chez Wix.
- [ ] Propagation DNS vérifiée multi-réseaux.
- [ ] HTTPS valide sur mayami.ellenemasri.com.
- [ ] WordPress configuré en URL canonique mayami.
- [ ] Cache/CDN purgés.
- [ ] Permaliens régénérés.
- [ ] Canonical/sitemap robots vérifiés.
- [ ] Liens internes, médias, assets testés.
- [ ] Mixed content vérifié.
- [ ] Login/admin validé sur mayami/wp-admin.
- [ ] Redirections validées (ancien domaine vers stratégie cible).
- [ ] Contrôles post-bascule J+1/J+2 effectués.

## Go / No-Go décision

Go uniquement si toutes les conditions suivantes sont vraies:

- Le sous-domaine mayami.ellenemasri.com est ajoutable et bien attaché à l'instance chez l'hébergeur.
- L'hébergeur confirme la capacité SSL pour mayami.ellenemasri.com (émission + renouvellement).
- Aucune redirection forcée vers l'ancien domaine technique (ellenemasri.pro) n'est active.
- Le login/admin est stable sur mayami.ellenemasri.com/wp-admin (pas de boucle, pas de perte de session).
- Le domaine custom est pleinement compatible avec le plan d'hébergement actuel (vhost/routage/certificat).
- Le plan Backup / Rollback est prêt et les sauvegardes sont disponibles.

No-Go immédiat (blocage chantier) si au moins une condition ci-dessous est vraie:

- L'hébergeur ne permet pas d'ajouter le sous-domaine custom.
- Le SSL ne peut pas être généré/servi de manière fiable pour mayami.ellenemasri.com.
- Une redirection forcée vers l'ancien domaine ne peut pas être désactivée.
- L'admin/login n'est pas stable sur le sous-domaine cible.
- Le plan d'hébergement ne supporte pas correctement le domaine custom (routage, reverse proxy, vhost, règles serveur).
- Aucune restauration fiable n'est possible en cas d'échec.
