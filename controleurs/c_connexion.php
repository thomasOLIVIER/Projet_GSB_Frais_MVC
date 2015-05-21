<?php
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];
switch ($action) {
    case 'demandeConnexion': {
            include("vues/v_connexion.php");
            break;
        }
    case 'valideConnexion': {
            $login = $_REQUEST['login'];
            $mdp = $_REQUEST['mdp'];
            $visiteur = $pdo->getInfosVisiteur($login, $mdp);
            if (!is_array($visiteur)) {
                ajouterErreur("Login ou mot de passe incorrect");
                include("vues/v_erreurs.php");
                include("vues/v_connexion.php");
            } else {
                $id = $visiteur['id'];
                $nom = $visiteur['nom'];
                $prenom = $visiteur['prenom'];
                $etat = $visiteur['libelleFonc'];
                connecter($id, $nom, $prenom, $etat);
                include("vues/v_sommaire.php");
            }
            break;
        }
    case 'deconnexion': {
            session_destroy();
        }

    default : {
            include("vues/v_connexion.php");
            break;
        }
}
?>