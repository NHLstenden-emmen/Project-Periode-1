<div class='devider'>
    <div class='pageContentBlock'>
        <main class="content">

<?php 
    /* Action Handlers */

    if(isset($_POST['aanpassenSubmit'])){
        if(isset($_POST['vakNaam'])){
            $vakNaam = $_POST['vakNaam'];
            $vakJaarlaag = $_POST['vakJaarlaag'];
            $vakPeriode = $_POST['vakPeriode'];
            $vakDocent = $_POST['vakDocent'];
            $vakID = $_POST['boekVakID'];

            if(empty($_FILES["vakBoek"]["name"])){
                //Geen nieuw moduleboek
                $DB->Get("UPDATE vakken SET vak = '{$vakNaam}', jaarlaag = '{$vakJaarlaag}', periode = '{$vakPeriode}' WHERE vak_id = '{$vakID}'");
            }
            else {
                //Moduleboek toegevoegd
                $fileName = basename($_FILES["vakBoek"]["name"]); 
                $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
                
                if($fileType == 'pdf'){ 
                    $pdf = $_FILES['vakBoek']['tmp_name']; 
                    $pdfContent = addslashes(file_get_contents($pdf)); 
                    $DB->Get("UPDATE vakken SET vak = '{$vakNaam}', jaarlaag = '{$vakJaarlaag}', periode = '{$vakPeriode}', moduleboek = '{$pdfContent}' WHERE vak_id = '{$vakID}'");
                }
                else {
                    echo "Je mag alleen een .pdf bestand uploaden.";
                } 
            }

                $DB->Get("UPDATE docenten_vakken SET docent_id = '{$vakDocent}', vak_id = '{$vakID}'");

                if(isset($_POST['vakKlas'])){
                    // verwijder alle klassen die het vak hadden
                        $DB->Get("DELETE FROM klassen_vakken WHERE vak_id='{$vakID}'");
                    foreach ($_POST['vakKlas'] as $key => $klasID) {
                        // voeg alle klassen toe die zijn ingevuld
                        $DB->Get("INSERT INTO klassen_vakken (klas_id, vak_id) VALUES ('{$klasID}','{$vakID}')");
                    }
            }

        }
        else {
            echo "Vaknaam is niet ingevuld.";
        }
    }


    //Moduleboek downloaden
    if(isset($_POST['boekView'])){
        if(isset($_POST['boekVakID'])){

            //Gegevens uit database halen
            $downloadModuleboek = $DB->Get("SELECT * FROM vakken WHERE vak_id ='{$_POST['boekVakID']}'");
            $moduleboekData = $downloadModuleboek->fetch_assoc();

            $fileName = $moduleboekData['vak'].' '.$moduleboekData['jaarlaag'].'-'.$moduleboekData['periode'].' - moduleboek.pdf';

            echo $Core->downloadFile($moduleboekData['moduleboek'], $fileName);
        }
    }

    //Invoegen
    if(isset($_POST['submitInvoegen'])){
        if(isset($_POST['vakNaam'])){
            
            $vakNaam = $_POST['vakNaam'];
            $vakJaarlaag = $_POST['vakJaarlaag'];
            $vakPeriode = $_POST['vakPeriode'];
            $vakDocent = $_POST['vakDocent'];

            if(empty($_FILES["vakBoek"]["name"])){
                //Geen moduleboek
                
                $insertResult = $DB->Get("INSERT INTO 
                                        vakken (vak, jaarlaag, periode)
                                        VALUES ('{$vakNaam}', '{$vakJaarlaag}', '{$vakPeriode}')");//>vakken
                    
            }
            else {
                //Moduleboek toegevoegd
                $fileName = basename($_FILES["vakBoek"]["name"]); 
                $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
                
                if($fileType == 'pdf'){ 
                    $pdf = $_FILES['vakBoek']['tmp_name']; 
                    $pdfContent = addslashes(file_get_contents($pdf)); 
                    
                    $insertResult = $DB->Get("INSERT INTO 
                                            vakken (vak, jaarlaag, periode, moduleboek)
                                            VALUES ('{$vakNaam}', '{$vakJaarlaag}', '{$vakPeriode}', '{$pdfContent}')");//>vakken 
                    
                }
                else {
                    echo "Je mag alleen een .pdf bestand uploaden.";
                } 
            }
                $vakID = $DB->LastID();
                $DB->Get("INSERT INTO docenten_vakken (docent_id, vak_id) VALUES ('{$vakDocent}','{$vakID}')");

                foreach ($_POST['vakKlas'] as $key => $klasID) {
                    $DB->Get("INSERT INTO klassen_vakken (klas_id, vak_id) VALUES ('{$klasID}','{$vakID}')");
                }
        }
        else {
            echo "Vaknaam is niet ingevuld.";
        }
    }


    if($Core->AuthCheck()){

        $docentID = intval($_COOKIE['userID']);

        //Laat de weergave pagina zien
        if(!isset($_POST['invoegenPage']) && !isset($_POST['submitDelete']) && !isset($_POST['boekView']) && !isset($_POST['aanpassenPage'])){
            echo "<div class='subTitle'>Vakkenbeheer | Keuzemenu </div>";
            //lijst met vakken met optie om ze aan te passen. (verwijderen)
            //knop voor nieuw vak

            $vakkenResult = $DB->Get("	SELECT vakken.vak_id, vakken.vak, vakken.jaarlaag, vakken.periode 
            FROM docenten_vakken INNER JOIN vakken 
            ON docenten_vakken.vak_id = vakken.vak_id 
            WHERE docent_id = '{$docentID}'
            ORDER BY vakken.jaarlaag ASC, vakken.periode ASC"); //Haalt alle vakken van de ID docent op.

            echo "<table>";
            while($vakkenData = $vakkenResult->fetch_assoc()){
                echo "<tr>";
                    echo "<td>{$vakkenData['vak']}</td>";
                    echo "<td>Jaar {$vakkenData['jaarlaag']}</td>";
                    echo "<td>Periode {$vakkenData['periode']}</td>";
                    echo "<td><form method='post'><input type='hidden' value='{$vakkenData['vak_id']}' name='aanpassenID'><button type='submit' name='aanpassenPage'><i class='fa fa-pencil' aria-hidden='true'></i></button></form></td>";
                    echo "<td><form method='post'><input type='hidden' value='{$vakkenData['vak_id']}' name='verwijderID'><button type='submit' name='submitDelete'><i class='fa fa-trash' aria-hidden='true'></i></button></form></td>";
                echo "</tr>";
            }
            echo "</table><form method='post'><button type='submit' name='invoegenPage'>Invoegen</button></form>";

        }
        //Laat de invoegen pagina zien
        else if(isset($_POST['invoegenPage']) && !isset($_POST['submitDelete']) && !isset($_POST['boekView']) && !isset($_POST['aanpassenPage'])){
                echo '<div class="subTitle">Vakkenbeheer | Invoegen</div><hr style="width: 30%"  />
                        <form method="POST" enctype="multipart/form-data"> 
                            <div class="subTitle">Vakinformatie</div>
                            
                            <label for="vakNaam">Vak*</label><br />
                            <input type="text" name="vakNaam" placeholder="Vaknaam" style="width: 65%;" required><br />
                            
                            <label for="vakJaarlaag">Jaarlaag*</label><br />
                            <select name="vakJaarlaag" style="width: 65%;">
                                <option value="1">Jaar 1</option>
                                <option value="2">Jaar 2</option>
                                <option value="3">Jaar 3</option>
                                <option value="4">Jaar 4</option>
                            </select><br />

                            <label for="vakPeriode">Periode*</label><br />
                            <select name="vakPeriode" style="width: 65%;">
                                <option value="1">Periode 1</option>
                                <option value="2">Periode 2</option>
                                <option value="3">Periode 3</option>
                                <option value="4">Periode 4</option>
                            </select><br />
                            <div class="subTitle">Klassen</div>';
                    
                    $klassenResult = $DB->Get("SELECT * FROM klassen");

                    echo "<label for='vakPeriode'>Klassen*</label><br />
                            <select class='selectMult' name='vakKlas[]' multiple style='width: 65%;'>";
                    while($klassenData = $klassenResult->fetch_assoc()){
                        echo "<option value='{$klassenData['klas_id']}'>{$klassenData['klas_naam']}</option>";
                    }
                   
                    echo '</select><br />';
                    echo '
                    <div class="subTitle">Docent(en)</div>';
 
                    $docentResult = $DB->Get("SELECT docent_id, voornaam, achternaam FROM docenten");
            
                    echo "<label for='vakDocent'>Docent*</label><br />
                            <select name='vakDocent'>";

                    while($docentData = $docentResult->fetch_assoc()){
                        echo "<option value='{$docentData['docent_id']}'>{$docentData['voornaam']} {$docentData['achternaam']}</option>";
                    }
                    
                    echo "</select><br />        
                    <div class='subTitle'>Vakbestanden</div>
                        <label for='vakBoek'>Moduleboek (.pdf)</label><br />
                        <input type='file' name='vakBoek' style='width: 65%;'><br />
                        <p>Vakken met een * zijn verplicht</p>
                        <button type='submit' name='submitInvoegen'>opslaan</button>
                        <button type='button' onclick="."window.location.href='vakkenbeheer'".">annuleren</button>
                    </form>";
    }
    //Laat de aanpassen pagina zien
    else if(!isset($_POST['invoegenPage']) && !isset($_POST['submitDelete']) && isset($_POST['aanpassenPage']) && !isset($_POST['boekView']) && intval($_POST['aanpassenID'])){

        //Haal huidige data op
        $currentResult = $DB->Get("SELECT vakken.vak_id, vakken.vak, vakken.jaarlaag, vakken.periode, docenten.voornaam, docenten.achternaam, docenten_vakken.docent_id,
                                    vakken.moduleboek
                                FROM vakken 
                                INNER JOIN docenten_vakken ON vakken.vak_id  = docenten_vakken.vak_id
                                INNER JOIN docenten ON docenten_vakken.docent_id = docenten.docent_id
                                WHERE vakken.vak_id = '{$_POST['aanpassenID']}'
                                LIMIT 1");

        $currentData = $currentResult->fetch_assoc();

    echo '<div class="subTitle">Vakkenbeheer | Aanpassen</div><hr style="width: 30%"  />
            <form method="POST" enctype="multipart/form-data"> 
            <div class="subTitle">Vakinformatie</div>
            
            <label for="vakNaam">Vak*</label><br />
            <input value="'.$currentData['vak'].'" type="text" name="vakNaam" placeholder="Vaknaam" style="width: 65%;" required><br />
            
            <label for="vakJaarlaag">Jaarlaag*</label><br />
            <select name="vakJaarlaag" style="width: 65%;">';

                for ($i=1; $i <= 4; $i++) { 
                    if($i == $currentData['jaarlaag']){
                        echo '<option class="optionSelected" value="'.$currentData['jaarlaag'].'" selected>Jaar '.$currentData['jaarlaag'].' (geselecteerd)</option>';
                    }
                    else if($i != $currentData['jaarlaag']){
                        echo '<option value="'.$i.'">Jaar '.$i.'</option>';
                    }
                }

            echo '</select><br />
            <label for="vakPeriode">Periode*</label><br />
            <select name="vakPeriode" style="width: 65%;">';

            for ($i=1; $i <= 4; $i++) { 
                if($i == $currentData['periode']){
                    echo '<option class="optionSelected" value="'.$currentData['periode'].'" selected>Periode '.$currentData['periode'].' (geselecteerd)</option>';
                }
                else if($i != $currentData['periode']){
                    echo '<option value="'.$i.'">Periode '.$i.'</option>';
                }
            }

            echo '</select><br />';
                    
            
               
            $klassen_vakkenResult = $DB->Get("SELECT klassen_vakken.klas_id, klassen.klas_naam FROM klassen_vakken
                                        INNER JOIN klassen 
                                        ON klassen_vakken.klas_id = klassen.klas_id
                                        WHERE klassen_vakken.vak_id = '{$currentData['vak_id']}'");
            

            $klassenResult = $DB->Get("SELECT * FROM klassen");

            echo "<label for='vakPeriode'>Klassen* (Selecteer meerdere met control.)</label><br />
                    <select class='selectMult' name='vakKlas[]' multiple style='width: 65%;'>";


           $klassenVakData = $klassen_vakkenResult->fetch_assoc();
           while($klassenData = $klassenResult->fetch_assoc()){
               print_r($klassenVakData);
                if(in_array($klassenData['klas_id'], $klassenVakData)){
                    $klassenVakData = $klassen_vakkenResult->fetch_assoc();
                    //selected
                    echo "<option class='optionSelected' value='{$klassenData['klas_id']}' selected >{$klassenData['klas_naam']}</option>";
                }
                else if(!in_array($klassenData['klas_id'], $klassenVakData)){
                    //unselected
                    echo "<option value='{$klassenData['klas_id']}'>{$klassenData['klas_naam']}</option>";
                }
            }
           
            echo '</select><br />
            <div class="subTitle">Docent(en)</div>';
 
            $docentResult = $DB->Get("SELECT docent_id, voornaam, achternaam FROM docenten");
           
            echo "<label for='vakDocent'>Docent*</label><br />
                    <select name='vakDocent'>";
            while($docentData = $docentResult->fetch_assoc()){
                if($docentData['docent_id'] == $currentData['docent_id']){
                    echo "<option class='optionSelected' value='{$currentData['docent_id']}' selected>{$currentData['voornaam']} {$currentData ['achternaam']} (geselecteerd)</option>";
                }
                else {
                    echo "<option value='{$docentData['docent_id']}'>{$docentData['voornaam']} {$docentData['achternaam']}</option>";
                }
            }
            echo "</select><br />
            <div class='subTitle'>Vakbestanden</div>";
                if(empty($currentData['moduleboek'])){
                    echo "<label for='vakBoek'>Moduleboek (.pdf)</label> <b>Momenteel niks geupload</b><br />";
                }
                else {
                    echo "<label for='vakBoek'>Moduleboek (.pdf) 
                <button type='submit' name='boekView'>weergeven</button></label><br />";
                }

                echo "
                <input type='file' name='vakBoek' style='width: 65%;'><br />
                <p>Vakken met een * zijn verplicht</p>
                <input type='hidden' name='boekVakID' value='{$currentData['vak_id']}'>
                <button type='submit' name='aanpassenSubmit'>opslaan</button>
                <button type='button' onclick="."window.location.href='vakkenbeheer'".">annuleren</button>
            </form>
        <br />";

    }
    //Gebruik de verwijderen pagina en controleert of de ingevoegde value wel een integer is.
    else if(!isset($_POST['invoegenPage']) && isset($_POST['submitDelete']) && !isset($_POST['aanpassenPage']) && !isset($_POST['boekView']) && !isset($_POST['aanpassenPage']) && intval($_POST['verwijderID'])){
        $DB->Get("DELETE FROM vakken WHERE vak_id='{$_POST['verwijderID']}'");
        header('location: vakkenbeheer');
    }
    else if(!isset($_POST['invoegenPage']) && !isset($_POST['submitDelete']) && !isset($_POST['aanpassenPage']) && !isset($_POST['boekView']) && intval($_POST['verwijderID'])){
        $DB->Get("DELETE FROM vakken WHERE vak_id='{$_POST['verwijderID']}'");
        header('location: vakkenbeheer');
    }

    }
    else {
        //Niet ingelogd, dus ga naar de nieuwspagina.
        header("Location: nieuws");
    }   
    ?>
        </div>
</main> 
    </div>