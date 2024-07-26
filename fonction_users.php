<?php
// Auteur: Nathan DAMASSE
// Ce fichier contient les fonctions pour gérer les utilisateurs et leurs permissions dans la base de données des utilisateurs.

include 'db_connect_users.php';

// Fonction pour obtenir les informations de l'utilisateur par ID
function getUserInfo($pdo, $userId) {
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE UserID = ?');
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir le rôle de l'utilisateur
function getUserRole($pdo, $userId) {
    $stmt = $pdo->prepare('SELECT r.RoleName FROM Users u JOIN Roles r ON u.RoleID = r.RoleID WHERE u.UserID = ?');
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['RoleName'] : null;
}

// Fonction pour vérifier si un utilisateur est connecté
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour rediriger vers la page de login si l'utilisateur n'est pas connecté
function redirectToLoginIfNotLoggedIn() {
    if (!isUserLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Fonction pour vérifier si l'utilisateur a une permission spécifique
function userHasPermission($pdo_users, $userID, $permissionName) {
    $stmt = $pdo_users->prepare("
        SELECT COUNT(*) 
        FROM Users 
        JOIN Roles ON Users.RoleID = Roles.RoleID
        JOIN RolePermissions ON Roles.RoleID = RolePermissions.RoleID
        JOIN Permissions ON RolePermissions.PermissionID = Permissions.PermissionID
        WHERE Users.UserID = :userID AND Permissions.PermissionName = :permissionName
    ");
    $stmt->execute(['userID' => $userID, 'permissionName' => $permissionName]);
    return $stmt->fetchColumn() > 0;
}

// Fonction pour afficher le nom complet de l'utilisateur
function displayUserFullName($pdo_users, $userID) {
    $userInfo = getUserInfo($pdo_users, $userID);
    if ($userInfo) {
        echo htmlspecialchars($userInfo['FirstName'] . ' ' . $userInfo['LastName']);
    }
}
?>
