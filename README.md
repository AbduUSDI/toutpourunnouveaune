# Tout pour un nouveau-né - Site Web

Bienvenue sur le dépôt du projet "Tout pour un nouveau-né". Ce site web est conçu pour aider les nouveaux parents à naviguer dans leur nouvelle vie avec des ressources telles que des conseils médicaux, des recettes pour bébés, des quizz interactifs et un forum communautaire.

## Fonctionnalités principales

- **Avis Médicaux** : Consultez les conseils et recommandations de professionnels de santé pour assurer le bien-être de votre bébé.
- **Quizz** : Testez et améliorez vos connaissances sur les soins et le développement de votre bébé à travers des quizz interactifs.
- **Recettes pour Bébé** : Accédez à une variété de recettes saines et adaptées aux besoins nutritionnels des tout-petits.
- **Forum** : Participez à une communauté de parents pour partager des expériences, poser des questions et obtenir du soutien.
- **Guides** : Lisez des guides détaillés sur la nutrition, la santé et le développement des enfants.

## Installation

1. **Configurez XAMPP** après l'avoir installé sur le site officiel.
2. Après avoir configuré XAMPP, vous avez besoin de créer la variable d'environnement dans vos réglages Windows
3. Une fois fait, il faut aller sur l'application XAMPP Control Panel et l'executer en tant qu'administrateur pour éviter tout conflit, le logiciel affichera alors plusieurs logiciels à ouvrir, ouvrez Apache (le serveur) ensuite ouvrez MySQL(la base de données). Cliquez ensuite sur "Admin" sur la ligne MySQL.
4. La page http://localhost/phpmyadmin/ s'ouvrira sur votre navigateur par défaut ensuite cliquez sur "Importer".
5. Importez la base de données MySQL en utilisant le fichier zoo_arcadia.sql qui contient tout le code SQL pour créer la BDD complète contenant ses tables et ses valeurs.
6. Vérifiez que la base de donnée contient bien les tables du projet.
(8 bis. Télécharger le code source du projet en .zip et décompresser le tout dans un dossier nommé "zoo_arcadia" qui devra être dans votre répertoire "htdocs" qui se trouve dans le dossier "xampp" (tout dépend de où vous l'avez positionner pendant votre installation, si par défaut : le dossier se trouve dans "utilisateur" dans le Disque local).) 
7. Ouvrez un invité de commandes : aller à la racine du projet et installer les dépendances comme :  Créer le fichier composer.json en faisant : `composer init`  puis ensuite télécharger les dépendances :     `composer require mongodb/mongodb` ; `composer require phpmailer/phpmailer`
8. Une fois les dépendances installées il faut aller sur le dossier "ext" de PHP, l'adresse exacte est par défaut : `"C:\xampp\php\ext"` transférer le fichier `"php_mongodb.dll"` que vous pouvez télécharger grâce à ce lien : [https://pecl.php.net/](https://pecl.php.net/package/mongodb/1.18.1/windows). Télécharger le bon fichier selon votre version php. Après avoir déplacer le fichier correspondant dans le répertoire "ext" de "php", aller sur le fichier `"php.ini"` et chercher la ligne "extension" en utilisant la barre de recherche (raccourci CTRL+F) ajouter la ligne `"extension=php_mongodb.dll"`.
9. N'oublier pas de créer une base de données et une collection MongoDB dans MongoDB Compass ou Atlas, une fois fait si vous êtes sur le port par défaut vous devrez avoir comme URI, databaseName et collections (clicks) :  `$uri = '"mongodb://localhost:27017"` ; `"$databaseName = 'zoo_arcadia_click_counts';"` 
10. Vous pouvez maintenant lancez l'application via votre serveur local en utilisant l'url : http://localhost/toutpourunnouveaune/index.php sur votre navigateur par défaut.

### Prérequis

- PHP 7.4 ou supérieur
- Serveur Web (Apache, Nginx, etc.)
- MySQL 5.7 ou supérieur
- MongoDB 4.4 ou supérieur
- Composer

### Étapes d'installation

1. **Clonez le dépôt** :
    ```bash
    git clone https://github.com/votre-utilisateur/toutpourunnouveaune.git
    cd tout-pour-un-nouveau-ne
    ```

2. **Installez les dépendances via Composer** :
    ```bash
    composer install
    ```

3. **Configuration de la base de données** :
   - Créez une base de données MySQL pour le projet.
   - Renommez le fichier `config/Database.php.example` en `Database.php` et mettez à jour les informations de connexion MySQL.

4. **Configuration de MongoDB** :
   - Configurez MongoDB en modifiant le fichier `config/MongoDB.php` en ajoutant vos URI en fonction de vos préférences.

5. **Importez les fichiers SQL** :
   - Importez le fichiers SQL disponible `toupourunnouveaune.sql` pour configurer les tables nécessaires.

6. **Mettez en place le serveur web** :
   - Configurez votre serveur web (Apache, Nginx, etc.) pour pointer vers le répertoire du projet.

7. **Lancez l'application** :
   - Accédez à votre site web via votre navigateur en naviguant vers `http://localhost/toutpourunnouveaune/index.php`.

## Utilisation

### Connexion

Les utilisateurs peuvent se connecter avec leurs identifiants pour accéder aux fonctionnalités du site. Des rôles spécifiques sont attribués pour accéder à différentes sections du site, comme les médecins, les parents, etc.

### Forum

Les utilisateurs peuvent créer des discussions, y répondre, et interagir avec d'autres parents dans le forum.

### Gestion des Guides, Avis Médicaux, Recettes, Quizz

- Les administrateurs et les utilisateurs ayant les droits peuvent créer, modifier et supprimer des guides, des avis médicaux, des recettes et des quizz.

### Sécurité

Le site intègre une protection contre les attaques CSRF sur les formulaires critiques. Les utilisateurs doivent être authentifiés pour accéder aux fonctionnalités protégées.

## Contribuer

1. **Forkez le projet**.
2. **Créez une branche** (`git checkout -b testbranch`).
3. **Commitez vos modifications** (`git commit -m 'testcommit'`).
4. **Poussez sur la branche** (`git push origin testbranch`).
5. **Ouvrez une Pull Request**.

## Auteurs

- **Nom du développeur** - *Développeur principal* - [USDI Abdurahman](https://github.com/AbduUSDI)

## Remerciements

Merci à la communauté de parents pour leurs retours constructifs.

