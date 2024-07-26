
# Installation et Configuration du Projet StorAix

Ce guide explique comment installer et configurer le projet d'Agenda StorAix, ainsi que comment mettre en place une sauvegarde hebdomadaire de la base de données.

## Prérequis

- Serveur Web (Apache, Nginx, etc.)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Accès en ligne de commande (SSH)
- Git

## Étapes d'installation


1. Configurer les bases de données

Assurez-vous que les bases de données MySQL sont créées et accessibles. Vous pouvez créer les bases de données avec les commandes suivantes :

sql :

CREATE DATABASE bdd_storAix;
CREATE DATABASE bdd_users;

2. Configurer les fichiers de connexion

Modifiez les fichiers de connexion à la base de données db_connect_StorAix.php et db_connect_users.php pour refléter vos informations de connexion MySQL :

php

// db_connect_StorAix.php
$host = '127.0.0.1';
$db = 'bdd_storAix';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

php

// db_connect_users.php
$host = '127.0.0.1';
$db = 'bdd_users';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

3. Configurer le fichier de création de PDF pour la connexion :

Modifiez le fichier de génération de PDF generate_intervention_pdf.php pour refléter vos informations de connexion MySQL :

php

//generate_intervention_pdf.php
//ligne 20 
$pdo = new PDO('mysql:host=localhost;dbname=bdd_storAix', 'root', 'root');



4. Configurer les permissions

Assurez-vous que les permissions des répertoires sont correctes :

sh

sudo chown -R www-data:www-data /var/www/html/uploads
sudo chmod -R 755 /var/www/html/uploads

5. Mettre en place la sauvegarde automatique
modifiez le script de sauvegarde pour correspondre au information de connexion ainsi qu'au chemin du fichier Backups :


sh
//backup_db.sh

DB_HOST="localhost"
DB_USER="root"
DB_PASS="root"
DB_NAME="bdd_storAix"

BACKUP_DIR="/var/backups/db"



Rendez le script exécutable :

sh

sudo chmod +x /usr/local/bin/backup_db.sh

Configurer la tâche cron

Ouvrez le crontab pour l'utilisateur root pour ajouter la tâche cron :

sh

sudo crontab -e

Ajoutez la ligne suivante pour exécuter le script chaque semaine :

sh

0 2 * * 0 /usr/local/bin/backup_db.sh

6. Tester l'installation

Pour vérifier que tout fonctionne correctement :

    Accédez à l'URL de votre site pour vous assurer que le projet est bien déployé.
    Testez la sauvegarde manuellement :

sh

sudo /usr/local/bin/backup_db.sh
