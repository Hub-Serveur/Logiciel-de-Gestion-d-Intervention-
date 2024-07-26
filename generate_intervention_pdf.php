<?php
// fichier: generate_intervention_pdf.php
// Auteur: Nathan DAMASSE
// Ce fichier génère un PDF de la fiche d'intervention en utilisant les détails d'un chantier.

require_once 'db_connect_StorAix.php';
require_once 'fonction_StorAix.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || !isset($_POST['uniqueId'])) {
        echo 'ID du chantier ou uniqueId non spécifié.';
        exit();
    }

    $idChantier = intval($_POST['id']);
    $uniqueId = intval($_POST['uniqueId']);
    $signature = isset($_POST['signature']) ? $_POST['signature'] : null;

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=bdd_storAix', 'root', 'root'); // Ajustez les informations de connexion selon votre configuration
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $chantier = getChantierDetailsByUniqueId($pdo, $idChantier, $uniqueId);
        if (!$chantier) {
            echo 'Détails du chantier introuvables.';
            exit();
        }

        $intervenants = getIntervenantsByChantier($pdo, $idChantier);
        $chantier['intervenants'] = $intervenants;

        $titre = htmlspecialchars($chantier["Titre"] ?? '');
        $nomEquipe = htmlspecialchars($chantier["Nom_Equipe"] ?? '');
        $dateTravail = htmlspecialchars($chantier["Date_Travail"] ?? '');

        $pdfName = $titre . '_' . $nomEquipe . '_' . date("d_m_Y", strtotime($dateTravail)) . '.pdf';

        ob_start();
        ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche d'Intervention</title>
    <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; color: #000; font-size: 12px; }
    .header img { width: 350px; height: auto; margin: 20px auto; display: block; }
    .content { margin: 20px; }
    .section { margin-bottom: 25px; }
    .section h2 { background-color: #f0f0f0; color: #000; padding: 5px; margin: 0; font-size: 12px; }
    .section p { margin: 10px 0; }
    .title-line { background-color: #143446; color: #FFF; padding: 3px; text-align: center; font-size: 14px; margin-top: 20px; }
    .footer { text-align: center; font-size: 6px; color: #000; }
    .page-number { text-align: center; font-size: 6px; color: #000; }
    img.signature { display: block; margin: 5px auto; max-width: 200px; max-height: 100px; height: auto; }
</style>

</head>
<body>
    <div class="header">
        <img src="img/carte_storaix.png" alt="Carte StorAix">
    </div>
    
    <div class="title-line">
        <h2>FICHE D'INTERVENTION</h2>
    </div>

    <div class="content">
        <div class="section">
            <h2>Objet de l'intervention</h2>
            <p><?php echo htmlspecialchars($chantier['Titre']); ?></p>
        </div>
        <div class="section">
            <h2>Intervention réalisée par</h2>
            <p><?php echo implode(', ', array_map(function ($intervenant) {
                return htmlspecialchars($intervenant['Prenom'] . ' ' . $intervenant['Nom']);
            }, $chantier['intervenants'])); ?></p>
        </div>
        <div class="section">
            <h2>Intervention prévue le</h2>
            <p><?php echo date('d/m/Y', strtotime($chantier['Date_Travail'])) . ' de ' . htmlspecialchars($chantier['Heure_Debut']) . ' à ' . htmlspecialchars($chantier['Heure_Fin']); ?></p>
        </div>
        <div class="section">
            <h2>Coordonnées du client</h2>
            <p>Nom : <?php echo htmlspecialchars($chantier['ClientNom'] . ' ' . $chantier['ClientPrenom']); ?></p>
            <p>Adresse d'intervention : <?php echo htmlspecialchars($chantier['Adresse']) . ', ' . htmlspecialchars($chantier['Ville']) . ', ' . htmlspecialchars($chantier['CodePostal']); ?></p>
            <p>Téléphone mobile : <?php echo htmlspecialchars($chantier['ClientTelephoneMobile']); ?></p>
            <p>Mail : <?php echo htmlspecialchars($chantier['ClientEmail']); ?></p>
        </div>
        <div class="section">
            <h2>Note d'intervention</h2>
            <p><?php echo nl2br(htmlspecialchars($chantier['NoteInterventionClient'])); ?></p>
        </div>
        <div class="section">
            <h2>Signature du client</h2>
            <?php if ($signature): ?>
                <img src="<?php echo htmlspecialchars($signature); ?>" alt="Signature" class="signature">
            <?php else: ?>
                <br>
                <br>
                <br>
                <p>___________________________</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
    <script>
        window.onload = function() {
            const element = document.body;
            const opt = {
                margin: 10,
                filename: '<?php echo $pdfName; ?>',
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
            };

            html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                const totalPages = pdf.internal.getNumberOfPages();
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);
                    pdf.setFontSize(6);  // Réduire la taille de la police
                    pdf.text('Page ' + i + ' of ' + totalPages, pdf.internal.pageSize.width / 2, pdf.internal.pageSize.height - 10, { align: 'center' });
                    pdf.text('SAS au capital social de 10 000 € / RCS : 344426143 / APE : 514S / N° TVA FR06344426143 / N° SIREN 344426143', pdf.internal.pageSize.width / 2, pdf.internal.pageSize.height - 5, { align: 'center' });
                }
            }).save().then(() => {
                window.close();
            });
        }
    </script>
</body>
</html>
        <?php
        $htmlContent = ob_get_clean();
        echo $htmlContent;
    } catch (PDOException $e) {
        echo 'Erreur de connexion : ' . $e->getMessage();
    }
} else {
    echo 'Méthode de requête non prise en charge.';
}
?>
