     <!-- Division pour le sommaire -->
  
<div id="bande"> 
<!-- Division pour le sommaire -->

<img src="./images/logo.png" class="displayed" id="img" alt="Laboratoire Galaxy-Swiss Bourdin" title="Laboratoire Galaxy-Swiss Bourdin" />

<h2>Comptable :<br><?php echo "Bienvenue"."  ".$_SESSION['prenom']."  ".$_SESSION['nom']  ?></h2> 
<div id='cssmenu'>
  
<ul>
    <li class='active'><a href='index.php?uc=connexion&action=valideConnexion'><span>Accueil</span></a></li>
   <li><a href="index.php?uc=validerfichefrais&action=choisirVisiteur">Valider fiche de frais</a></li>
   <li><a href="index.php?uc=suiviFrais&action=selectionnerFicheDeFrais">Suivi fiche de frais</a></li>
   <li>
        <a href="index.php?uc=connexion&action=deconnexion">Se déconnecter</a>
      </li>
</ul>
</div>
</div>