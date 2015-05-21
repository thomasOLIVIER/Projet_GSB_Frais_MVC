    <!-- Division pour le sommaire -->
<div id="menuGauche">
    <div id="infosUtil">

        <h2>

        </h2>

    </div>
    <ul id="menuList">
        <li>
            <?php
            if ($_SESSION['etat'] == 'Visiteur') {
                ?>
                Visiteur :<br/>
                <?php echo $_SESSION['prenom'] . "  " . $_SESSION['nom']; ?>
            </li>
            <li class="smenu">
                <a href="index.php?uc=gererFrais&action=saisirFrais" title="Saisie fiche de frais ">Saisie fiche de frais</a>
            </li>
            <li class="smenu">
                <a href="index.php?uc=etatFrais&action=selectionnerMois" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
            </li>
            <?php
        }
        if ($_SESSION['etat'] == 'Comptable') {
            ?>
            Comptable:<br/>
            <?php echo $_SESSION['prenom'] . " " . $_SESSION['nom']; ?>
            
                    
            <ul class="ca-menu">
                    <li>
                        <a href="index.php?uc=validationFicheFrais&action=selectionnerVisiteur" title="Administration des thèmes">
                            <div class="ca-content">
                                <h2 class="ca-main">Valider</h2>
                                <h3 class="ca-sub">fiche de frais</h3>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?uc=suiviPaiement&action=selectionnerFrais">
                            <div class="ca-content">
                                <h2 class="ca-main">Suivi</h2>
                                <h3 class="ca-sub">fiche de frais</h3>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?uc=connexion&action=deconnexion">
                            <div class="ca-content">
                                <h2 class="ca-main">D&eacute;connexion</h2>
                                <h3 class="ca-sub"></h3>
                            </div>
                        </a>
                    </li>
                </ul>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
            
            
            
            
            
            <?php
        }
        ?>
    </ul>
</div>