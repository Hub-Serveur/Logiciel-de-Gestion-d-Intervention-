-- Suppression de la base de données si elle existe déjà
DROP DATABASE IF EXISTS bdd_users;

-- Création de la nouvelle base de données
CREATE DATABASE bdd_users;

-- Utilisation de la base de données créée
USE bdd_users;

-- Création de la table des rôles
CREATE TABLE Roles (
    RoleID INT AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR(255) NOT NULL
);

-- Création de la table des utilisateurs
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(255) NOT NULL,
    LastName VARCHAR(255) NOT NULL,
    Identifiant VARCHAR(191) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    PasswordPlain VARCHAR(255) NOT NULL, -- Ajout de la colonne PasswordPlain
    RoleID INT,
    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
);

-- Création de la table des permissions (facultative, pour une gestion plus fine)
CREATE TABLE Permissions (
    PermissionID INT AUTO_INCREMENT PRIMARY KEY,
    PermissionName VARCHAR(255) NOT NULL
);

-- Création de la table de liaison entre rôles et permissions
CREATE TABLE RolePermissions (
    RoleID INT,
    PermissionID INT,
    PRIMARY KEY (RoleID, PermissionID),
    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID),
    FOREIGN KEY (PermissionID) REFERENCES Permissions(PermissionID)
);

-- Insertion des rôles
INSERT INTO Roles (RoleName) VALUES ('Intervenant'), ('Gestionnaire'), ('Administrateur');

-- Insertion des permissions
INSERT INTO Permissions (PermissionName) VALUES ('View Agenda'), ('Edit Agenda'), ('Manage Users');

-- Permissions pour Intervenant
INSERT INTO RolePermissions (RoleID, PermissionID) VALUES (1, 1); -- Peut seulement voir l'agenda

-- Permissions pour Gestionnaire
INSERT INTO RolePermissions (RoleID, PermissionID) VALUES (2, 1), (2, 2); -- Peut voir et éditer l'agenda

-- Permissions pour Administrateur
INSERT INTO RolePermissions (RoleID, PermissionID) VALUES (3, 1), (3, 2), (3, 3); -- Peut tout faire

-- Ajout d'un utilisateur intervenant avec mot de passe haché et en clair
INSERT INTO Users (FirstName, LastName, Identifiant, PasswordHash, PasswordPlain, RoleID) VALUES ('Jean', 'Dupont', 'jean.dupont', SHA2('Interv', 256), 'Interv', 1);
INSERT INTO Users (FirstName, LastName, Identifiant, PasswordHash, PasswordPlain, RoleID) VALUES ('Alice', 'Martin', 'alice.martin', SHA2('Interv', 256), 'Interv', 1);
INSERT INTO Users (FirstName, LastName, Identifiant, PasswordHash, PasswordPlain, RoleID) VALUES ('Claire', 'Bernard', 'claire.bernard', SHA2('Interv', 256), 'Interv', 1);

-- Ajout d'un gestionnaire
INSERT INTO Users (FirstName, LastName, Identifiant, PasswordHash, PasswordPlain, RoleID) VALUES ('Marine', 'Martin', 'marine.martin', SHA2('Gestio', 256), 'Gestio', 2);

-- Ajout d'un administrateur
INSERT INTO Users (FirstName, LastName, Identifiant, PasswordHash, PasswordPlain, RoleID) VALUES ('dylan', 'Leroy', 'dylan.leroy', SHA2('Admin', 256), 'Admin', 3);

-- Sélection de tous les utilisateurs avec leurs rôles
SELECT Users.UserID, Users.FirstName, Users.LastName, Users.Identifiant, Roles.RoleName
FROM Users
JOIN Roles ON Users.RoleID = Roles.RoleID;

-- Sélection des utilisateurs avec leurs rôles et permissions
SELECT Users.FirstName, Users.LastName, Roles.RoleName, Permissions.PermissionName
FROM Users
JOIN Roles ON Users.RoleID = Roles.RoleID
JOIN RolePermissions ON Roles.RoleID = RolePermissions.RoleID
JOIN Permissions ON RolePermissions.PermissionID = Permissions.PermissionID;

-- Sélection des gestionnaires
SELECT Users.FirstName, Users.LastName, Users.Identifiant
FROM Users
JOIN Roles ON Users.RoleID = Roles.RoleID
WHERE Roles.RoleName = 'Gestionnaire';

-- Sélection des utilisateurs ayant la permission de gérer les utilisateurs
SELECT DISTINCT Users.FirstName, Users.LastName, Users.Identifiant
FROM Users
JOIN RolePermissions ON Users.RoleID = RolePermissions.RoleID
JOIN Permissions ON RolePermissions.PermissionID = Permissions.PermissionID
WHERE Permissions.PermissionName = 'Manage Users';

-- Sélection des informations d'un utilisateur spécifique
SELECT Users.FirstName, Users.LastName, Users.Identifiant, Roles.RoleName
FROM Users
JOIN Roles ON Users.RoleID = Roles.RoleID
WHERE Users.Identifiant = 'alice.martin';
