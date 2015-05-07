   <table class="listeLegere">
        <caption>Descriptif des éléments hors forfait</caption>

        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>  
            <th class="montant">Montant</th>  
            <th class="action">&nbsp;</th>              
        </tr>

        <?php    
        foreach( $lesFraisHorsForfait as $unFraisHorsForfait) {
            $libelle = $unFraisHorsForfait['libelle'];
            $date = $unFraisHorsForfait['date'];
            $montant = $unFraisHorsForfait['montant'];
            $id = $unFraisHorsForfait['id'];
            ?>		
            <tr>
                <td> <?php echo $date; ?></td>
                <td><?php echo $libelle; ?></td>
                <td><?php echo $montant; ?></td>
                <td><a href="index.php?uc=gererFrais&action=supprimerFrais&idFrais=<?php echo $id; ?>" 
                    onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">Supprimer ce frais</a>
                </td>
            </tr>
            <?php		 
        }
        ?>                                         
    </table>

    <form id="frmCreationFrais" action="index.php?uc=gererFrais&action=validerCreationFrais" method="post">
        <div class="corpsForm">
            <fieldset>
                <legend>Nouvel élément hors forfait</legend>
                
                <p>
                    <label for="txtDateHF">Date (jj/mm/aaaa): </label>
                    <input type="text" id="txtDateHF" name="txtDateHF" size="10" maxlength="10" value="" />
                </p>
                
                <p>
                    <label for="txtLibelleHF">Libellé</label>
                    <input type="text" id="txtLibelleHF" name="txtLibelleHF" size="70" maxlength="256" value="" />
                </p>
                
                <p>
                    <label for="txtMontantHF">Montant : </label>
                    <input type="text" id="txtMontantHF" name="txtMontantHF" size="10" maxlength="10" value="" />
                </p>
            </fieldset>
        </div>
        
        <div class="piedForm">
            <p>
                <input id="cmdAjouter" type="submit" value="Ajouter" size="20" />
                <input id="cmdEffacer" type="reset" value="Effacer" size="20" />
            </p> 
        </div>
    </form>
</div>
