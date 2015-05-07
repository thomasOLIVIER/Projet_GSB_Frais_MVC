<div id="contenu">
    <h2>Renseigner ma fiche de frais du mois <?php echo $numMois."-".$numAnnee ?></h2>
    
	<form id="frmSaisieFicheFrais" action="index.php?uc=gererFrais&action=validerMajFraisForfait" method="post">
            <div class ="corpsForm">
            <fieldset>
                <legend>Frais forfaitises</legend>
                
	<!--<div id ="saisie" style="width:25%">-->
                    <?php
                    foreach ($lesFraisForfait as $unFrais) {
                        $idFrais = $unFrais['idfrais'];
                        $libelle = $unFrais['libelle'];
                        $quantite = $unFrais['quantite'];
                        ?>
                        <p>
                            <label for="idFrais"><?php echo $libelle ?></label>
                            <input type="text"  name="txtIdFrais[<?php echo $idFrais?>]" size="10" maxlength="5" value="<?php echo $quantite?>" >
                        </p>
                        <?php
                    }
                    ?>
            </fieldset>
        </div>
		<!--<table border ="10">
			<tr>
				<td>Montant</td>
				<td><input id="ok" type="text" value="Montant réglé" style="color:lightgrey" onFocus="this.value=''" onblur="if(this.value=='')this.value='Montant réglé'"/><br/></td>
			</tr>
				<tr>
				<td>Date</td>
				<td><input id="annuler" type="text" value="Date du règlement" style="color:lightgrey" onFocus="this.value=''" onblur="if(this.value=='')this.value='Date du règlement'"/><br/></td>
			</tr>
				<tr>
				<td>Libelle</td>
				<td><input id="annuler" type="text" value="Descriptif" style="color:lightgrey" onFocus="this.value=''" onblur="if(this.value=='')this.value='Descriptif'"/><br/></td>
			</tr>
		</table>
		<br/>
		<br/>
		<td colspan=2><input id="Valider" type="submit" text="ok"></td>-->
             <div class="piedForm">
            <p>
                <input id="cmdOk" type="submit" value="Valider" size="20" />
                <input id="brAnnuler" type="reset" value="Effacer" size="20" />
            </p> 
        </div>
            


