<!-- Division principale -->
<script type="text/javascript" src="./include/ajax.js"></script>
 <div id="contenu">
     <h2 style="padding-top: 10px;">Validation des Frais</h2>
     <h3> Validation des frais par visiteur </h3>
     <div class="corpsForm">
         <input type="hidden" name="etape" value="rechercher" />
            <p style="float: left;width: 280px;margin-left: 20px;">
                <label for="lstVisiteur"> Visiteur : </label>
                    <select id="lstVisiteur" name="lstVisiteur" 
                            style="border-color: #77AADD;" onchange="javascript:recupMois()">
                        <option value="-1">Choisir un visiteur</option>
                       <?php
                           // on propose tous les visiteurs ayant une fiche frais du mois
                           foreach($VisiteurAyantFiche as $ligneVisiteur) { ?>  
                               
                               <option value="<?php echo $ligneVisiteur['id']; ?>"> <?php echo $ligneVisiteur['nom']; ?>
                                  <?php echo $ligneVisiteur['prenom']; ?></option>
                           <?php
                           }
                           ?>
                     </select>
              </p>
             <p>
                    <label for="lstMois" style="width: 70px;"> Mois :</label>
                    <select id="lstMois" name="lstMois" >
                        <option value='-1'>Choisir un mois</option>
                    </select>
               </p>
               <input type="submit" value="afficher" onclick="javascript=Affichage()">
               
               <div id="Affichage">
                   
               </div>

       </div>