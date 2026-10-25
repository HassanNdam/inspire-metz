# Inspire Metz

Site de l'agence d'attractivité de l'Euro-Métropole de Metz.

WordPress sous [Bedrock](https://roots.io/bedrock/), thème sur mesure basé sur [Sage 11](https://roots.io/sage/) (Blade, Vite, Tailwind CSS 4). Les contenus se gèrent dans l'administration grâce à Secure Custom Fields.

## Prérequis

PHP 8.3, Composer 2, Node 20+, MySQL 8.

## Installation

```bash
composer install
cp .env.example .env            # base de données, URL, clés (https://roots.io/salts.html)

cd web/app/themes/inspire-metz
composer install
npm ci
npm run build                   # ou npm run dev pour le rechargement à chaud
```

Le serveur web pointe sur `web/`. WordPress est accessible sur `/wp/wp-admin`.

Contenu de démonstration (pages, menus, actualités, réglages…), relançable sans doublons :

```bash
vendor/bin/wp eval-file scripts/seed-demo.php
```

## Structure

| Dossier | Contenu |
| --- | --- |
| `web/app/themes/inspire-metz/` | Thème : vues Blade, styles, scripts, champs personnalisés (`app/Fields`, `app/Options`) |
| `web/app/mu-plugins/inspire-metz-core/` | Types de contenu et formulaires (contact, newsletter, ambassadeurs) |
| `config/` | Configuration WordPress par environnement |
| `scripts/` | Contenu de démonstration |

## Branches

Le projet suit Git flow :

- `main` : version en production, mise à jour uniquement par release ou hotfix ;
- `develop` : branche de travail ;
- `feature/<sujet>` : une évolution, fusionnée dans `develop` ;
- `release/<version>` et `hotfix/<version>` : mises en production, taguées `v<version>`.

```bash
git flow feature start mon-sujet
git flow feature finish mon-sujet
git flow release start 1.0.0 && git flow release finish 1.0.0
```

## Mise en ligne

Les dépendances et les fichiers compilés ne sont pas versionnés. Sur le serveur, après chaque déploiement :

```bash
composer install --no-dev
cd web/app/themes/inspire-metz && composer install --no-dev && npm ci && npm run build
```

Les photos et textes de démonstration proviennent du site inspire-metz.com et appartiennent à l'agence.
