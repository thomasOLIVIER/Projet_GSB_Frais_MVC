<?php
include("vues/v_sommaire.php");
$idVisiteur = $_SESSION['idVisiteur'];
$mois = getMois(date("d/m/Y"));
$numAnnee = substr($mois,0,4);
$numMois = substr($mois,4,2);
$action = htmlspecialchars($_GET['action']);

switch($action) {
    case 'saisirFrais':
        if($pdo->estPremierFraisMois($idVisiteur, $mois)) {
            $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
        }
        break;
    case 'validerMajFraisForfait':
        $lesFrais = $_POST['txtIdFrais'];

        if(lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
        } else {
            ajouterErreur("Les valeurs des frais doivent être numériques");
            include("vues/v_erreurs.php");
            unset($_SESSION['erreurs']);
        }
        break;
    case 'validerCreationFrais':
        $dateFrais = htmlspecialchars($_POST['txtDateHF']);
        $libelle = htmlspecialchars($_POST['txtLibelleHF']);
        $montant = htmlspecialchars($_POST['txtMontantHF']);
        valideInfosFrais($dateFrais, $libelle, $montant);
        if (nbErreurs() != 0) {
            include("vues/v_erreurs.php");
            unset($_SESSION['erreurs']);
        } else {
            $pdo->creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $dateFrais, $montant);
        }
        break;
    case 'supprimerFrais':
        $idFrais = htmlspecialchars($_GET['idFrais']);
        $pdo->supprimerFraisHorsForfait($idFrais);
        break;
}

$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
$lesFraisForfait= $pdo->getLesFraisForfait($idVisiteur, $mois);
include("vues/v_fraisForfaitises.php");
include("vues/v_fraisHorsForfait.php");

?>
?>