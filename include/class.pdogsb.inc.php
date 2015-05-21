<?php

/**
 * Classe d'accès aux données. 

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=gsb_frais';   		
      	private static $user='root' ;    		
      	private static $mdp='' ;	
	private static $monPdo;
	private static $monPdoGsb=null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        PdoGsb::$monPdo = new PDO(PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
        PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb() {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur

     * @param $login 
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif 
     */
    public function getInfosVisiteur($login, $mdp){
		$req = "SELECT visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom , intitule_fonc.libelleFonc as libelleFonc
					FROM visiteur,fonction,intitule_fonc
					WHERE visiteur.id = fonction.id
					AND intitule_fonc.fonction = fonction.fonction
					AND visiteur.login= :login 
					AND visiteur.mdp= :mdp";
		$req_prepare = PdoGsb::$monPdo -> prepare($req);
		$req_prepare->bindParam(':login', $login, PDO::PARAM_STR);
		$req_prepare->bindParam(':mdp', $mdp, PDO::PARAM_STR);
		$req_prepare->execute();
		$ligne = $req_prepare->fetch();
		return $ligne;
	}

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments

     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
     */
    public function getLesFraisHorsForfait($idVisiteur,$mois) {
        $sql = "SELECT * FROM lignefraishorsforfait WHERE idUtilisateur = :idVisiteur AND mois = :mois";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->execute(array('idVisiteur' => $idVisiteur, 'mois' => $mois));
        $lesLignes = $req->fetchAll();
        $req->closeCursor();
        
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs 
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $sql = "SELECT fraisforfait.id AS idfrais, fraisforfait.libelle AS libelle, fraisforfait.montant AS montant,
                lignefraisforfait.quantite AS quantite
                FROM lignefraisforfait INNER JOIN fraisforfait ON fraisforfait.id = lignefraisforfait.idFraisForfait
                WHERE lignefraisforfait.idUtilisateur = :idVisiteur AND lignefraisforfait.mois = :mois
                ORDER BY lignefraisforfait.idFraisForfait";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->execute(array('idVisiteur' => $idVisiteur, 'mois' => $mois));
        $lesLignes = $req->fetchAll();
        $req->closeCursor();
        
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait

     * @return un tableau associatif 
     */
    public function getLesIdFrais() {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait

     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif 
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = " . $qte . " where lignefraisforfait.idvisiteur = '" . $idVisiteur . "' and lignefraisforfait.mois = '" . $mois . "' and lignefraisforfait.idfraisforfait = " . $unIdFrais . "";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs 
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux 
     */
    public function estPremierFraisMois($idVisiteur,$mois) {
        $ok = false;
        $sql = "SELECT COUNT(*) AS nbLignesFrais FROM fichefrais
                WHERE mois = :mois AND idUtilisateur = :idVisiteur";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->execute(array('mois' => $mois, 'idVisiteur' => $idVisiteur));
        $laLigne = $req->fetch();
        $req->closeCursor();
        
        if ($laLigne['nbLignesFrais'] == 0) {
            $ok = true;
        }
        
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur 
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $sql = "SELECT MAX(mois) AS dernierMois FROM fichefrais WHERE idUtilisateur = :idVisiteur";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->execute(array('idVisiteur' => $idVisiteur));
        $laLigne = $req->fetch();
        $req->closeCursor();
        $dernierMois = $laLigne['dernierMois'];
        
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait 
		values('','$idVisiteur','$mois','$libelle','$dateFr','$montant')";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais 
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }

    
    
    
    /**
     * Retourne la requête récupérant les mois disponibles pour un visiteur
     * 
     * Retourne une requête différente selon le status demandé (toutes, validées ou cloturées)
     * @param type $idVisiteur
     * @param type $status
     * @return type
     */
    public function getReqMoisDisponibles($idVisiteur, $status) {
        if ($status == "toutes") {
            $sql = "SELECT mois FROM fichefrais WHERE idUtilisateur = :idVisiteur ORDER BY mois DESC";
            $req = PdoGsb::$monPdo->prepare($sql);
            $req->execute(array('idVisiteur' => $idVisiteur));
        } else if ($status == "validees") {
            $sql = "SELECT mois FROM fichefrais WHERE idUtilisateur = :idVisiteur AND (idEtat = :va OR idEtat = :mp OR idEtat = :rb) ORDER BY mois DESC";
            $req = PdoGsb::$monPdo->prepare($sql);
            $req->execute(array('idVisiteur' => $idVisiteur, ':va' => 'VA', ':mp' => 'MP', 'rb' => 'RB')); 
        } else if ($status == "cloturees") {
            $sql = "SELECT mois FROM fichefrais WHERE idUtilisateur = :idVisiteur AND idEtat = :cl ORDER BY mois DESC";
            $req = PdoGsb::$monPdo->prepare($sql);
            $req->execute(array('idVisiteur' => $idVisiteur, ':cl' => 'CL'));
        }
        
        return $req;
    }
    
    
    
    
    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur 
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
     */
   public function getLesMoisDisponibles($idVisiteur, $status = "toutes") {
        $req = $this->getReqMoisDisponibles($idVisiteur, $status);
        
        $lesMois = array();
        $laLigne = $req->fetch();
        while($laLigne != null)	{
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois,0,4);
            $numMois = substr($mois,4,2);
            $lesMois[$mois] = array(
                "mois" => $mois,
                "numAnnee" => $numAnnee,
                "numMois" => $numMois
            );
            $laLigne = $req->fetch(); 		
        }
        $req->closeCursor();
        
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
     */
    public function getLesInfosFicheFrais($idVisiteur,$mois) {
        $sql = "SELECT FF.idEtat as idEtat, FF.dateModif, FF.nbJustificatifs, FF.montantValide, E.libelle as libEtat
                FROM fichefrais AS FF INNER JOIN etat AS E ON FF.idEtat = E.id
                WHERE FF.idUtilisateur = :idVisiteur AND FF.mois = :mois";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->execute(array('idVisiteur' => $idVisiteur, 'mois' => $mois));
        $laLigne = $req->fetch();
        $req->closeCursor();
        
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $req = "update ficheFrais set idEtat = '$etat', dateModif = now() 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Renvoie l'id, le nom et le prénom de chacun des visiteurs

     *
     * @return array Tableau contenant l'id, le nom, et le prénom
     */
    public function getLesVisiteurs() {
        $req = "select id, nom, prenom from visiteur order by nom";
        $ligneResultat = PdoGsb::$monPdo->query($req);
        return $ligneResultat;
    }

    /**
     * Ajoute "REFUSE :" devant le libelle du frais hors forfait.
     * 
     * @param $libelle du frais hors forfait
     * @param $id du frais hors forfait
     */
    public function majFraisHorsForfait($libelle, $id) {
        $req = "update lignefraishorsforfait set libelle = 'REFUSE : " . $libelle . "' where id = " . $id;
        PdoGsb::$monPdo->exec($req);
    }
    
    /**
     * Reporte un frais hors forfait au mois suivant.
     * 
     * @param $id de la table lignefraishorsforfait
     * @param $date sous la forme aaaamm
     * @param $idVisiteur id du visiteur
     */
    public function reportFraisHorsForfait($id, $date, $idVisiteur){
        $mois = substr($date, 4, 2);
        $mois++;
        if($mois > 12){
            $mois = "01";
        }
        $date = substr_replace($date, $mois, 4);
        if($this->estPremierFraisMois($idVisiteur,$date)){
            $this->creeNouvellesLignesFrais($idVisiteur, $date);
            $this->majEtatFicheFrais($idVisiteur, $date, 'CR');
        }
        $req = "update lignefraishorsforfait set mois = '" . $date . "' where id = " . $id;
        PdoGsb::$monPdo->exec($req);
    }
    
    /**
     * Retourne les fiches frais qui sont validées
     * 
     * @return $ligneResultat sous forme de tableau associatif content l'id, le nom ainsi que le prénom du visiteur, et le mois concerné.
     */
    public function getFichesFraisValidees(){
        $sql = "SELECT visiteur.id, visiteur.nom, visiteur.prenom, mois FROM fichefrais,visiteur Where fichefrais.idutilisateur = visiteur.id and idetat = 'VA'";
        $req = PdoGsb::$monPdo->prepare($sql);
        //$req->execute(array('idVisiteur' => $idVisiteur, 'mois' => $mois));
        $lesLignes = $req->fetchAll();
        $req->closeCursor();
        
        return $lesLignes;
    }
    
    /**
     * Retourne les montant de chaque frais forfait rangé dans l'ordre alphabétique dans un tableau
     * 
     */
    public function getLesMontants(){
        $req = "select montant from fraisforfait order by id";
        $ligneResultat = pdoGsb::$monPdo->query($req);
        $fetchAll = $ligneResultat->fetchall();
        return $fetchAll;
    }
    
    /**
     * Retourne les quantité de chaque frais rangé dans l'ordre alphabétique dans un tableau
     * 
     * @param $idVisiteur id du visiteur
     * @param $mois sous la forme aaamm
     */
    public function getLesQuantites($idVisiteur, $mois){
        $sql = "select quantite from lignefraisforfait where idvisiteur = '" . $idVisiteur . "' and mois = '" . $mois . "' order by idfraisforfait";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->execute(array('idVisiteur' => $idVisiteur, 'mois' => $mois));
        $laLigne = $req->fetch();
        
        return $laLigne;
    }
    
    /**
     * 
     */
    public function majMontantValide($idVisiteur, $mois, $montant){
        $req = "update fichefrais set montantvalide = " . $montant . " where idvisiteur = '" . $idVisiteur . "' and mois = '" . $mois . "'";
        PdoGsb::$monPdo->exec($req);
    }
    
    /**
     * 
     */
    public function getMontantHorsForfait($idVisiteur, $mois){
        $sql = "select sum(montant) from lignefraishorsforfait where idvisiteur = '" . $idVisiteur . "' and mois = '" . $mois . "'";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->execute(array('idVisiteur' => $idVisiteur, 'mois' => $mois));
        $laLigne = $req->fetch();
        $req->closeCursor();
        
        return $laLigne;
    }
    
    /**
     * 
     */
    public function dropVisiteur($idVisiteur){
        $req = "delete from visiteur where id = '" . $idVisiteur . "'";
        PdoGsb::$monPdo->exec($req);
    }
    
    /**
     * 
     */
    public function getLeVisiteur($idVisiteur){
        $req = "select * from visiteur where id = '" . $idVisiteur . "'";
        $resultat = PdoGsb::$monPdo->query($req);
        $fetch = $resultat->fetch();
        return $fetch;
    }
    
    /**
     * 
     */
    public function validerUpdateVisiteur($idVisiteur, $nomVisiteur, $prenomVisiteur){
        $req = "update visiteur set nom = '" . $nomVisiteur . "', prenom = '" . $prenomVisiteur . "' where id='" . $idVisiteur . "'";
        PdoGsb::$monPdo->exec($req);
    }
    
    /**
     * 
     */
    public function insertVisiteur($idVisiteur, $nomVisiteur, $prenomVisiteur, $loginVisiteur, $mdpVisiteur, $adresseVisiteur, 
            $cpVisiteur, $villeVisiteur, $dateEmbaucheVisiteur){
        $req = "insert into visiteur values('" . $idVisiteur . "', '" . $nomVisiteur . "', '" .$prenomVisiteur."', " . 
                $loginVisiteur . "', '" . $mdpVisiteur . "', '" . $adresseVisiteur . "', '" . $cpVisiteur . "', '" . 
                $villeVisiteur . "', '" . $dateEmbaucheVisiteur."')";
        echo $req;
    }
}