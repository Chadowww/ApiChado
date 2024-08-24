# API-Chado
Projet de formation en refonte avec PHP/SYMFONY(squelette) en API et Vue.js en front (https://github.com/Chadowww/Job-It-Better-FRONT). Le choix a été fait de ne pas utiliser Doctrine dans un but de développement de compétences.

# Stack Technique

* PHP 8.3
* Symfony 6.3
* PHPUnit 9.5

# Bibliothèques

* LexikJWTAuthenticationBundle (Gestion de Token JWT)
* NelmioApiDocBundle (Génération de documents API)
* NelmioCorsBundle (Gestion des requêtes CORS)
* Zircote Swagger-PHP (Création de documents OpenAPI)
* Symfony HTTP Client (Pour les requêtes HTTP)
* Monolog Bundle (Pour le logging)

# Bundles

* Symfony Security Bundle (Authentification)
* Symfony Serializer (Serialisation de données)
* Symfony Validator (Validation de données)
* Symfony Twig Bundle (Pour le rendu de vue avec Twig)
* Symfony Framework Bundle (Fournit une structure de base pour Symfony)
* Symfony Mime (Pour la manipulation de MIME)
* Symfony Console (Pour la création de commandes console)
* Symfony Dotenv (Pour le chargement des variables d'environnement à partir de `.env`)
* Symfony Asset (Pour la gestion des actifs tels que les CSS, JavaScript, images)

Remarque: Ce projet utilise également PHPUnit pour les tests unitaires et d'intégration, ainsi que le bundle Symfony BrowserKit pour simuler le navigateur et le bundle Symfony CssSelector pour aider à filtrer et à manipuler HTML/XML.

# Configuration

* `autoload` et `autoload-dev` ont été configurés pour la norme PSR-4, avec le namespace `App\` pointant vers le répertoire `src/` et le namespace `App\Tests\` pointant vers le répertoire `tests/`.

* Le script `post-install-cmd` a été configuré pour exécuter `cache:clear` et `assets:install %PUBLIC_DIR%` après chaque `composer install`.

* Le script `post-update-cmd` a été configuré pour exécuter les mêmes scripts après chaque `composer update`.

* Le projet est configuré pour utiliser Symfony 6.3.*.

# Sécurité

* Le projet utilise le package `roave/security-advisories` qui assure qu'aucune dépendance avec des failles de sécurité connues n'est installée.
