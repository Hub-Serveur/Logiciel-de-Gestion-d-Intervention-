-- Suppression de la base de données si elle existe déjà
DROP DATABASE IF EXISTS bdd_storAix;

-- Création de la nouvelle base de données
CREATE DATABASE bdd_storAix;

-- Utilisation de la base de données créée
USE bdd_storAix;

-- Création de la table Intervenant
CREATE TABLE Intervenant (
    ID_Intervenant INT AUTO_INCREMENT PRIMARY KEY,
    Nom VARCHAR(255) null,
    Prenom VARCHAR(255) null
);

-- Création de la table Equipe avec une colonne pour la couleur et le téléphone
CREATE TABLE Equipe (
    ID_Equipe INT AUTO_INCREMENT PRIMARY KEY,
    Nom_Equipe VARCHAR(255) null,
    Couleur VARCHAR(7) null, -- Couleurs hexadécimales
    Telephone VARCHAR(15) null
);

-- Création de la table Client
CREATE TABLE Client (
    ID_Client INT AUTO_INCREMENT PRIMARY KEY,
    Nom VARCHAR(255) null,
    Prenom VARCHAR(255) null,
    Adresse VARCHAR(255) null,
    Ville VARCHAR(255) null,
    Email VARCHAR(100) null ,
    TelephoneFixe VARCHAR(15),
    TelephoneMobile VARCHAR(15) null
);

-- Création de la table Locataire
CREATE TABLE Locataire (
    ID_Locataire INT AUTO_INCREMENT PRIMARY KEY,
    Nom VARCHAR(255),
    Prenom VARCHAR(255),
    TelephoneFixe VARCHAR(15),
    TelephoneMobile VARCHAR(15),
    ID_Client INT,
    FOREIGN KEY (ID_Client) REFERENCES Client(ID_Client) ON DELETE SET NULL
);

-- Création de la table Chantier
CREATE TABLE Chantier (
    ID_Chantier INT AUTO_INCREMENT PRIMARY KEY,
    Titre VARCHAR(255) null,
    Adresse VARCHAR(255) null,
    Ville VARCHAR(255) null,
    CodePostal VARCHAR(10) null, -- Ajout du code postal
    Zone VARCHAR(255) null,
    Description VARCHAR(255),
    NombreColis VARCHAR(255),
    NoteInterventionClient TEXT,
    NoteInterventionEquipe TEXT,
    StatutIntervention VARCHAR(255) null,
    StatutSAV VARCHAR(255) null,
    ID_Client INT,
    ID_Equipe INT,
    FOREIGN KEY (ID_Client) REFERENCES Client(ID_Client) ON DELETE SET NULL,
    FOREIGN KEY (ID_Equipe) REFERENCES Equipe(ID_Equipe) ON DELETE SET NULL
);

-- Création de la table Horaire_Chantier
CREATE TABLE Horaire_Chantier (
    Unique_Id INT AUTO_INCREMENT PRIMARY KEY,
    ID_Chantier INT,
    ID_Equipe INT,
    Date_Travail DATE null,
    Heure_Debut TIME null,
    Heure_Fin TIME null,
    FOREIGN KEY (ID_Chantier) REFERENCES Chantier(ID_Chantier) ON DELETE CASCADE,
    FOREIGN KEY (ID_Equipe) REFERENCES Equipe(ID_Equipe) ON DELETE CASCADE
);

-- Création de la table relationnelle Intervenant_Equipe
CREATE TABLE Intervenant_Equipe (
    ID_Intervenant INT,
    ID_Equipe INT,
    PRIMARY KEY (ID_Intervenant, ID_Equipe),
    FOREIGN KEY (ID_Intervenant) REFERENCES Intervenant(ID_Intervenant) ON DELETE CASCADE,
    FOREIGN KEY (ID_Equipe) REFERENCES Equipe(ID_Equipe) ON DELETE CASCADE
);


-- Création de la table relationnelle Equipe_Chantier
CREATE TABLE IF NOT EXISTS Equipe_Chantier (
    ID_Equipe INT,
    ID_Chantier INT,
    PRIMARY KEY (ID_Equipe, ID_Chantier),
    FOREIGN KEY (ID_Equipe) REFERENCES Equipe(ID_Equipe) ON DELETE CASCADE,
    FOREIGN KEY (ID_Chantier) REFERENCES Chantier(ID_Chantier) ON DELETE CASCADE
);


-- Création de la table Images_Chantier
CREATE TABLE Images_Chantier (
    ID_Image INT AUTO_INCREMENT PRIMARY KEY,
    ID_Chantier INT,
    Image_Path VARCHAR(255),
    FOREIGN KEY (ID_Chantier) REFERENCES Chantier(ID_Chantier) ON DELETE CASCADE
);

-- Insertion des données pour les intervenants
INSERT INTO Intervenant (Nom, Prenom) VALUES
('Dupont', 'Jean'),
('Martin', 'Alice'),
('Bernard', 'Claire'),
('Thomas', 'Alexandre'),
('Petit', 'Sophie'),
-- Nouveaux intervenants pour compléter les équipes
('Moreau', 'Paul'),
('Durand', 'Marie'),
('Roux', 'Julien'),
('Lemoine', 'Chantal'),
('Blanc', 'Sylvie');

-- Insertion des données pour les équipes
INSERT INTO Equipe (Nom_Equipe, Couleur, Telephone) VALUES
('Equipe Alpha', '#FF5733', '0601010101'),
('Equipe Beta', '#4CAF50', '0602020202'),
('Equipe Gamma', '#2196F3', '0603030303'),
('Equipe Delta', '#FFC107', '0604040404'),
('Equipe Epsilon', '#673AB7', '0605050505');

-- Insertion des données pour les clients
INSERT INTO Client (Nom, Prenom, Adresse, Ville, Email, TelephoneFixe, TelephoneMobile) VALUES
('Leroy', 'Michel', '123 rue de la Paix', 'Paris', 'michel.leroy@example.com', '0123456789', '0123456789'),
('Moreau', 'Sophie', '456 avenue des Champs-Elysées', 'Paris', 'sophie.moreau@example.com', '9876543210', '9876543210'),
('Girard', 'Nicolas', '789 rue de la République', 'Lyon', 'nicolas.girard@example.com', '0234567891', '0234567891'),
('Lopez', 'Isabelle', '101 rue Saint-Lazare', 'Marseille', 'isabelle.lopez@example.com', '0345678902', '0345678902'),
('Bernier', 'Emilie', '202 avenue Victor Hugo', 'Bordeaux', 'emilie.bernier@example.com', '0456789012', '0456789012');

-- Insertion des données pour les locataires
INSERT INTO Locataire (Nom, Prenom, TelephoneFixe, TelephoneMobile, ID_Client) VALUES
('Duval', 'Marie', '0412345678', '0612345678', 1),
('Garcia', 'Pierre', '0423456789', '0623456789', 1),
('Lemoine', 'Isabelle', '0434567890', '0634567890', 2),
('Martinez', 'Julie', '0445678901', '0645678901', 3),
('Fernandez', 'Luc', '0456789012', '0656789012', 4);

-- Insertion des données pour les chantiers
INSERT INTO Chantier (Titre, Adresse, Ville, CodePostal, Zone, Description, NombreColis, NoteInterventionClient, NoteInterventionEquipe, StatutIntervention, StatutSAV, ID_Client, ID_Equipe) VALUES
('Projet Saint-Michel', '789 boulevard Saint-Michel', 'Paris', '75006', 'Île-de-France', 'Rénovation complète de l\'édifice', '30', 'L\'intervention s\'est bien déroulée, les délais ont été respectés et les équipes ont fait preuve d\'un grand professionnalisme.', 'Le chantier s\'est déroulé sans encombre. Les matériaux étaient de bonne qualité et les tâches ont été exécutées de manière très professionnelle.', 'En cours', 'Non Requis', 1, 1),
('Projet Rivoli', '1011 rue de Rivoli', 'Paris', '75001', 'Île-de-France', 'Construction d\'un nouveau bâtiment', '100', 'Très satisfait du travail réalisé, bien que quelques retards aient été notés. Le résultat final est conforme à nos attentes.', 'L\'équipe a bien coordonné les différentes phases du chantier. Quelques ajustements ont été nécessaires en cours de route, mais tout s\'est bien terminé.', 'Cloturé', 'Non Requis', 2, 3),
('Aménagement Parc', '132 avenue de Wagram', 'Paris', '75017', 'Île-de-France', 'Aménagement d\'un parc public', '50', 'L\'aménagement du parc a été réalisé avec soin. Les espaces verts sont magnifiques et les structures installées sont de qualité.', 'Malgré quelques retards, l\'équipe a su gérer efficacement les imprévus et a livré un parc très agréable.', 'En cours', 'Requis', 3, 1),
('Rénovation Musée', '58 rue de Rivoli', 'Paris', '75001', 'Île-de-France', 'Mise à jour des installations du musée', '20', 'Excellente intervention, les nouvelles installations du musée répondent parfaitement à nos besoins et attentes.', 'L\'équipe a fait un travail remarquable en respectant le patrimoine historique tout en modernisant les installations.', 'Cloturé', 'Non Requis', 4, 2),
('Construction École', '202 rue Saint-Martin', 'Paris', '75003', 'Île-de-France', 'Construction d\'une nouvelle école primaire', '80', 'Bon travail réalisé par l\'équipe. L\'école est fonctionnelle et les enfants pourront en profiter dès la rentrée prochaine.', 'Les travaux ont été effectués avec rigueur et professionnalisme, malgré quelques difficultés rencontrées en cours de projet.', 'Facturé', 'Requis', 5, 4);

-- Insertion des relations entre intervenants et équipes
INSERT INTO Intervenant_Equipe (ID_Intervenant, ID_Equipe) VALUES
(1, 1), (6, 1), -- Jean Dupont et Paul Moreau dans Equipe Alpha
(2, 2), (7, 2), -- Alice Martin et Marie Durand dans Equipe Beta
(3, 3), (8, 3), -- Claire Bernard et Julien Roux dans Equipe Gamma
(4, 4), (9, 4), -- Alexandre Thomas et Chantal Lemoine dans Equipe Delta
(5, 5), (10, 5); -- Sophie Petit et Sylvie Blanc dans Equipe Epsilon

-- Insertion des horaires de chantier avec un maximum de 2 équipes par chantier
INSERT INTO Horaire_Chantier (ID_Chantier, ID_Equipe, Date_Travail, Heure_Debut, Heure_Fin) VALUES
-- Semaine 1
(1, 1, '2024-05-27', '09:30', '12:00'),
(1, 1, '2024-05-27', '13:15', '16:00'),
(2, 3, '2024-05-28', '09:45', '12:00'),
(2, 4, '2024-05-28', '13:00', '16:30'),
(2, 3, '2024-05-29', '09:45', '12:00'),
(2, 4, '2024-05-29', '13:00', '16:30'),
(3, 1, '2024-05-29', '09:00', '12:45'),
(3, 5, '2024-05-29', '13:00', '16:15'),
(1, 5, '2024-05-30', '13:00', '16:15'),
(4, 2, '2024-05-30', '09:00', '12:00'),
(4, 3, '2024-05-30', '13:00', '16:00'),
(5, 4, '2024-05-31', '09:00', '12:00'),
(5, 5, '2024-05-31', '13:00', '16:00'),
(1, 1, '2024-06-01', '08:00', '11:00'),
(1, 2, '2024-06-01', '12:00', '15:00'),
(2, 3, '2024-06-02', '08:00', '11:00'),
(2, 4, '2024-06-02', '12:00', '15:00'),
(3, 1, '2024-06-03', '08:00', '11:00'),
(3, 5, '2024-06-03', '12:00', '15:00'),
(4, 2, '2024-06-04', '09:00', '12:00'),
(4, 3, '2024-06-04', '13:00', '16:00'),
(5, 4, '2024-06-05', '09:00', '12:00'),
(5, 5, '2024-06-05', '13:00', '16:00'),
-- Semaine 2
(1, 1, '2024-06-06', '09:00', '12:00'),
(1, 2, '2024-06-06', '13:00', '16:00'),
(2, 3, '2024-06-07', '09:00', '12:00'),
(2, 4, '2024-06-07', '13:00', '16:00'),
(3, 1, '2024-06-08', '09:00', '12:00'),
(3, 5, '2024-06-08', '13:00', '16:00'),
(4, 2, '2024-06-09', '08:00', '11:00'),
(4, 3, '2024-06-09', '12:00', '15:00'),
(5, 4, '2024-06-10', '08:00', '11:00'),
(5, 5, '2024-06-10', '12:00', '15:00'),
(1, 1, '2024-06-11', '09:00', '12:00'),
(1, 2, '2024-06-11', '13:00', '16:00'),
(2, 3, '2024-06-12', '09:00', '12:00'),
(2, 4, '2024-06-12', '13:00', '16:00'),
(3, 1, '2024-06-13', '09:00', '12:00'),
(3, 5, '2024-06-13', '13:00', '16:00'),
(4, 2, '2024-06-14', '09:00', '12:00'),
(4, 3, '2024-06-14', '13:00', '16:00'),
(5, 4, '2024-06-15', '09:00', '12:00'),
(5, 5, '2024-06-15', '13:00', '16:00'),
(1, 1, '2024-06-16', '08:00', '11:00'),
(1, 2, '2024-06-16', '12:00', '15:00'),
(2, 3, '2024-06-17', '08:00', '11:00'),
(2, 4, '2024-06-17', '12:00', '15:00'),
(3, 1, '2024-06-18', '08:00', '11:00'),
(3, 5, '2024-06-18', '12:00', '15:00'),
(4, 2, '2024-06-19', '09:00', '12:00'),
(4, 3, '2024-06-19', '13:00', '16:00'),
(5, 4, '2024-06-20', '09:00', '12:00'),
(5, 5, '2024-06-20', '13:00', '16:00'),
-- Semaine 3 avec des heures variées
(1, 1, '2024-06-21', '07:00', '10:00'),
(1, 2, '2024-06-21', '10:00', '13:00'),
(2, 3, '2024-06-22', '13:00', '16:00'),
(2, 4, '2024-06-22', '16:00', '19:00'),
(3, 1, '2024-06-23', '07:00', '10:00'),
(3, 5, '2024-06-23', '10:00', '13:00'),
(4, 2, '2024-06-24', '07:00', '10:00'),
(4, 3, '2024-06-24', '10:00', '13:00'),
(5, 4, '2024-06-25', '13:00', '16:00'),
(5, 5, '2024-06-25', '16:00', '19:00'),
(1, 1, '2024-06-26', '07:00', '10:00'),
(1, 2, '2024-06-26', '10:00', '19:00'),
(2, 3, '2024-06-27', '13:00', '19:00'),
(1, 4, '2024-06-27', '07:00', '14:00'),
(3, 1, '2024-06-28', '07:00', '10:00'),
(3, 5, '2024-06-28', '10:00', '13:00'),
(4, 2, '2024-06-29', '07:00', '10:00'),
(4, 3, '2024-06-29', '10:00', '19:00'),
(5, 4, '2024-06-30', '13:00', '16:00'),
(5, 5, '2024-06-30', '16:00', '19:00');



-- Affichage des données pour les intervenants
SELECT * FROM Intervenant;

-- Affichage des données pour les équipes
SELECT * FROM Equipe;

-- Affichage des données pour les clients
SELECT * FROM Client;

-- Affichage des données pour les locataires
SELECT * FROM Locataire;

-- Affichage des données pour les chantiers
SELECT * FROM Chantier;

-- Affichage des horaires des chantiers
SELECT * FROM Horaire_Chantier;

-- Affichage des données pour la relation Intervenant_Equipe
SELECT * FROM Intervenant_Equipe;

-- Affichage des données pour la relation Equipe_Chantier
SELECT * FROM Equipe_Chantier;

-- Affichage des noms et prénoms des intervenants et le nom de leur équipe associée
SELECT
    i.Nom AS IntervenantNom,
    i.Prenom AS IntervenantPrenom,
    e.Nom_Equipe AS EquipeNom,
    e.Telephone AS EquipeTelephone
FROM
    Intervenant i
JOIN
    Intervenant_Equipe ie ON i.ID_Intervenant = ie.ID_Intervenant
JOIN
    Equipe e ON ie.ID_Equipe = e.ID_Equipe
ORDER BY
    i.Nom, i.Prenom;

-- Affichage des horaires des chantiers avec les noms des équipes et les titres des chantiers
SELECT
    h.Unique_Id,
    c.Titre AS Chantier,
    e.Nom_Equipe AS Equipe,
    h.Date_Travail,
    h.Heure_Debut,
    h.Heure_Fin
FROM
    Horaire_Chantier h
JOIN
    Chantier c ON h.ID_Chantier = c.ID_Chantier
JOIN
    Equipe e ON h.ID_Equipe = e.ID_Equipe
ORDER BY
    h.Date_Travail, h.Heure_Debut;

-- Affichage des données pour les images des chantiers
SELECT * FROM Intervenant;

SELECT * FROM Chantier WHERE Titre LIKE '%Projet Saint-Michel%';
SELECT * FROM Client WHERE Nom LIKE '%Leroy%' OR Prenom LIKE '%Michel%';
SELECT * FROM Equipe WHERE Nom_Equipe LIKE '%Alpha%';


