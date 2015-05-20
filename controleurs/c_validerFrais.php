<?php

include('vues/v_sommaire.php');
$action = $_REQUEST['action'];

$tabVisiteurs = $pdo->getLesVisiteurs();
include ('vues/v_listeVisiteur.php');

switch ($action) {
    case "voirEtatFrais":
        $idVisiteur = $_POST['lstVisiteur'];
        $_SESSION['idVisiteur'] = $idVisiteur;
        $leMois = $_POST['lstMois'];
        $_SESSION['leMois'] = $leMois;
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = $lesInfosFicheFrais['dateModif'];
        $dateModif = dateAnglaisVersFrancais($dateModif);
        $readOnly = "";
        $button = "<td class='qteForfait'><input type='submit' value='Modifier'></td>";
        $report = "<td><input type='submit' name='btnReportRefus' value='Reporter'><br/>";
        $refuser = "<input type='submit' name='btnReportRefus' value='Refuser'></td>";
        $valider = 1;
        
        // Permet de verifier si il y a bien présence de fiche frais forfait ou de fiche frais hors forfait
        
        if((empty($lesFraisForfait)) && (empty($lesFraisHorsForfait))) {
            include("vues/v_pasDeFicheFrais.php");
        } else {
            include("vues/v_etatFrais.php");
        }
        break;
        
    case "validerFiche":
        $idVisiteur = $_SESSION['idVisiteur'];  
        $lesFrais = $_REQUEST['lesFrais'];
        $leMois = $_SESSION['leMois'];
        $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);
        include("vues/v_affichModif.php");
        break;
    
    case "reportRefus":
        $reportRefus = $_POST['btnReportRefus'];
        $id = $_POST['id'];
        $libelle = $_POST['libelle'];
        $leMois = $_SESSION['leMois'];
        $idVisiteur = $_SESSION['idVisiteur'];
        if($reportRefus == 'Refuser' && !preg_match("/REFUSE :/", $libelle)){
            $pdo->majFraisHorsForfait($libelle, $id);
            include("vues/v_refus.php");
        }else{
            if($reportRefus == 'Reporter'){
                $pdo->reportFraisHorsForfait($id, $leMois, $idVisiteur);
                include('vues/v_update.php');
            }
        }
        break;
    case "validerFrais": {
            $leVisiteur = $_SESSION['idVisiteur'];
            $leMois = $_SESSION['leMois'];
            $nbJustificatifs = $_REQUEST['nbJustificatifs'];
            $rs = $pdo->majEtatFicheFrais2($leVisiteur, $leMois, "VA", $nbJustificatifs);
            $tabMontant = $pdo->getLesMontants();
            
            $tabQuantites = $pdo->getLesQuantites($leVisiteur, $leMois);
            $montant = 0;
            for ($i = 0; $i < 4; $i++) {
                $montant += ($tabMontant[$i][0] * $tabQuantites[$i][0]);
            }
            $montantHorsForfait = $pdo->getMontantHorsForfait($leVisiteur, $leMois);
           
            $montant += $montantHorsForfait[0];
            $pdo->majMontantValide($leVisiteur, $leMois, $montant);
            if ($rs == 0) {
                ajouterErreur('La Fiche frais a bien été validé!');
                $type = 1;
                include("vues/v_erreurs.php");
            } else {
                ajouterErreur("La Fiche frais n'a pas été validé!");
                include("vues/v_erreurs.php");
            }
            break;
        }
        include("vues/v_valide.php");
}
include("vues/v_pied.php");
?>