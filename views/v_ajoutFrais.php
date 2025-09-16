<div class="myFrom">
    <h2>Saisie de frais</h2>
    <form action="index.php?uc=ajoutFrais&action=ajouterFrais" method="post">
        <table >
            <tr>
                <td><h3>VISITEUR : </h3></td> </tr>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td> <label for="numeroVisiteur"> Numéro : </label> </td>
                            <td> <select name="numeroVisiteur" id="numeroVisiteur">
                            <?php foreach ($listIdVisiteurs as $idVisiteur){?>
                                <option value = "<?php echo $idVisiteur ?>">
                                                <?php echo $infosVisiteurs[$idVisiteur] ?> 
                                        </option>
                            <?php
                            }
                            ?>
                            </select></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>PERIODE D'ENGAGEMENT : </h3> </td></tr>
            <tr> <td></td></tr>
            <table>
                <tr>
                    <td> <label for="moisEngagement"></label> Mois : </td>
                    <td>
                        <select name="moisEngagement">
                            <?php
                            $listeMois = array("01","02","03","04","05","06","07","08","09","10","11","12");
                            foreach ($listeMois as $mois) {
                                ?>
                                <option value="<?php echo $mois ?>"><?php echo $mois ?></option>
                                <?php
                            }?>
                        </select>
                    </td>
                    <td> <label for="anneeEngagement"></label> Année : </td>
                    <td>
                        <input type="number" name="anneeEngagement" id="anneeFrais" min="1960" max="<?php echo date('Y'); ?>" required>
                    </td>
                </tr>
            </table>
            </table>
            </tr>

            <tr><h3>Frais Forfait : </h3></tr>
            <table>
                <tr><td> <label for="repasFrais"> Repas midi : </label> </td>
                    <td> <input type="number" name="repasFrais" id="repasFrais"></td></tr>
                <tr><td> <label for="nuiteeFrais"> Nuitée : </label> </td>
                    <td> <input type="number" name="nuiteeFrais" id="nuiteeFrais"></td></tr>
                <tr><td> <label for="etapeFrais"> Etape : </label> </td>
                    <td> <input type="number" name="etapeFrais" id="etapeFrais"></td></tr>
                <tr><td> <label for="kmFrais"> Km : </label> </td>
                    <td> <input type="number" name="kmFrais" id="kmFrais"></td></tr>
                <tr>
                    <td colspan="8" style="text-align: center;">
                        <br><br>
                        <input type="submit" value="VALIDER">
                    </td>
                </tr>
        </table>


    </form>
</div>