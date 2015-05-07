<h3>Fiche de frais du mois <?php echo $numMois . "-" . $numAnnee ?> : 
</h3>
<div class="encadre">
    <p>
        Etat : <?php echo $libEtat ?> depuis le <?php echo $dateModif ?> <br> Montant valid&eacute; : <?php echo $montantValide ?>


    </p>
    <form method="post" action="index.php?uc=validationFicheFrais&action=validFrais">
        <table class="listeLegere">
            <caption>El&eacute;ments forfaitis&eacute;s </caption>
            <tr>
                <?php
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $libelle = $unFraisForfait['libelle'];
                    ?>	
                    <th><?php echo $libelle; ?></th>
                    <?php
                }
                ?>
            </tr>
            <tr>
                <?php
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $quantite = $unFraisForfait['quantite'];
                    ?>
                    <td class="qteForfait"><input type="text" name="lesFrais['<?php echo $unFraisForfait['idfrais']; ?>']" value="<?php echo $quantite; ?>" <?php echo $readOnly; ?>/></td>
                    <?php
                }
                echo $button;
                ?>
            </tr>
        </table>
    </form>

    <table class="listeLegere">
        <caption>Descriptif des &eacute;l&eacute;ments hors forfait
        </caption>
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libell&eacute;</th>
            <th class='montant'>Montant</th>  
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = $unFraisHorsForfait['libelle'];
            $montant = $unFraisHorsForfait['montant'];
            $id = $unFraisHorsForfait['id'];
            ?>
            <tr>
            <form method="post" action="index.php?uc=validationFicheFrais&action=reportRefus">
                <input type="text" name="id" hidden="hidden" value="<?php echo $id; ?>">
                <td><input type="text" name="date" readOnly="readOnly" value="<?php echo $date; ?>"/></td>
                <td><input type="text" name="libelle" readOnly="readOnly" value="<?php echo $libelle; ?>"/></td>
                <td><input type="text" name="montant" readOnly="readOnly" value="<?php echo $montant; ?>"/></td>
                <?php echo $report . $refuser; ?>
            </form>
            </tr>
            <?php
        }
        ?>
    </table>

</div>
<?php
if ($valider == 1) {
    ?>
    <form method="post" action="index.php?uc=validationFicheFrais&action=validFiche">
        <input type="submit" name="validFrais" value="Valider"/>
    </form>
    <?php
} elseif ($valider == 2) {
    ?>
    <form method="post" action="index.php?uc=suiviPaiement&action=remboursement">
        <input type="submit" name="rembourserFrais" value="Rembourser"/>
    </form>
    <?php
} else {
    echo "";
}
?>
</div>