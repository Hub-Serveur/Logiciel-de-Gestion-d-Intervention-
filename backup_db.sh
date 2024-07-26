#!/bin/bash

# Informations de connexion à la base de données
DB_HOST="localhost"
DB_USER="root"
DB_PASS="root"
DB_NAME="bdd_storAix"

# Répertoire où les backups seront sauvegardés
BACKUP_DIR="/var/www/html/Backups"

# Nom du fichier de backup avec la date
BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_$(date +'%Y-%m-%d').sql"

# Créer le répertoire de sauvegarde s'il n'existe pas
mkdir -p $BACKUP_DIR

# Commande mysqldump pour effectuer la sauvegarde
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_FILE

# Vérifier si la sauvegarde s'est bien passée
if [ $? -eq 0 ]; then
    echo "Sauvegarde de la base de données réussie : $BACKUP_FILE"
else
    echo "Erreur lors de la sauvegarde de la base de données"
    exit 1
fi

# Optionnel: supprimer les anciennes sauvegardes de plus de 30 jours
find $BACKUP_DIR -type f -name "*.sql" -mtime +30 -exec rm {} \;
