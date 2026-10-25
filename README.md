# Inspire Metz

Site de l'agence d'attractivité de l'Eurométropole de Metz : WordPress sous [Bedrock](https://roots.io/bedrock/), thème sur mesure basé sur [Sage 11](https://roots.io/sage/) (Blade, Acorn, Vite, Tailwind CSS 4).

La maquette de référence est `Inspire METZ site.pdf`, à la racine. Elle n'est pas versionnée.

## Organisation

| Dossier | Rôle |
| --- | --- |
| `config/`, `.env` | Configuration WordPress (Bedrock) |
| `web/app/themes/inspire-metz/` | Thème : vues Blade, design system, champs SCF |
| `web/app/themes/inspire-metz/app/Fields`, `app/Options` | Champs personnalisés (acf-composer + Secure Custom Fields) |
| `web/app/mu-plugins/inspire-metz-core/` | Types de contenu et formulaires (contact, newsletter, ambassadeurs). Indépendant du thème. |

## Installation locale (Laragon)

1. `composer install` à la racine, puis dans `web/app/themes/inspire-metz/`.
2. Copier `.env.example` en `.env` et renseigner la base de données.
3. Vhost Apache : `inspire-metz.localhost` → `web/` (voir `C:/laragon/etc/apache2/sites-enabled/inspire-metz.localhost.conf`).
4. Thème : `npm install`, puis `npm run dev` (rechargement à chaud) ou `npm run build`.

URL utiles :

- Site : http://inspire-metz.localhost
- Administration : http://inspire-metz.localhost/wp/wp-admin
- Design system : http://inspire-metz.localhost/design-system/
- phpMyAdmin : http://localhost/phpmyadmin
- E-mails de test (Mailpit) : http://localhost:8025

> Si `npm run dev` est interrompu brutalement, supprimer `web/app/themes/inspire-metz/public/hot`. Sinon le site continue de charger ses assets depuis un serveur Vite arrêté.

## WP-CLI

```bash
php vendor/wp-cli/wp-cli/php/boot-fs.php <commande>   # Git Bash
vendor\bin\wp.bat <commande>                           # PowerShell
```

Exemples : `acorn acf:field NomDuGroupe`, `acorn acf:clear`, `acorn view:clear`.

## Branches (Git flow)

| Branche | Rôle |
| --- | --- |
| `main` | Code en production. On n'y commite jamais directement. |
| `develop` | Intégration : base de toutes les nouvelles fonctionnalités. |
| `feature/<sujet>` | Une fonctionnalité, partie de `develop` et fusionnée dans `develop`. |
| `release/<version>` | Préparation d'une mise en production (`develop` → `main`, avec tag `v<version>`). |
| `hotfix/<version>` | Correctif urgent parti de `main`, fusionné dans `main` et `develop`. |

Avec l'extension git-flow (fournie avec Git pour Windows) :

```bash
git flow feature start formulaire-contact   # crée feature/formulaire-contact depuis develop
git flow feature finish formulaire-contact  # fusionne dans develop
git flow release start 1.0.0                # prépare la version
git flow release finish 1.0.0               # fusionne dans main + develop, tag v1.0.0
git flow hotfix start 1.0.1                 # correctif depuis main
```

Sans l'extension, les mêmes étapes se font avec `git checkout -b` et `git merge --no-ff`.

## Contenu de démonstration

```bash
php vendor/wp-cli/wp-cli/php/boot-fs.php eval-file scripts/seed-demo.php
```

Le script crée les pages, menus, actualités, communiqués, Jeem, newsletters, vidéos, les 46 communes et les réglages. On peut le relancer : il met à jour sans créer de doublons.

Les photos et les textes sont repris du site en ligne inspire-metz.com. Ce contenu appartient à l'agence : à valider avant toute mise en production.

## Contenu éditable

- **Inspire Metz** (menu d'admin) : coordonnées, réseaux sociaux, missions, pied de page, destinataires des formulaires.
- **Pages** : en-tête (chapô, bandeau) et constructeur de blocs (texte, texte + image, chiffres clés, fichiers, galerie, vidéo, formulaire…).
- **Actualités, Communiqués, Jeem, Newsletters envoyées, Vidéos, Communes** : un type de contenu chacun.
- **Formulaires** : réponses aux formulaires, avec suivi (Nouveau / Lu / Traité) et export CSV.
