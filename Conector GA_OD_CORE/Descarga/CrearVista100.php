<?php

    $vista = 100;
    define ("CLAVE_URI1", "ANYO");
    define ("CLAVE_URI2", "CODIGO_PROVINCIA");
    define ("CLAVE_URI3", "CLAVE_CALLE");
    define ("VISTA_NECESITA", "11");										//El numero de la vista que necesita para completar sus datos
    define ("CLAVE_TIENE", "NOMBRE_MUNICIPIO");								//La clve que tiene para poder relacionarse
    define ("CLAVE_TIENE_DEPENDE", "DENOMINACION");
    define ("CLAVE_NECESITA","CODIGO_MUN"); 								//La clave que necesita
    define("URL_CLAVE_PRO", "../VistasCsv/Relacion/DatosCodUrlPro.csv");     //Ruta al achivo de datos externo para en campo de UrlProvincia
    define ("CLAVE_PRO", "UrlProvincia");                                   //El campo del mapeo al cual que que ponerle la url de la provincia
    define ("COD_PRO", "CODIGO_PROVINCIA");                                 //El nombre del campo para sacar el codigo de la provincia
    define ("XML_DEPENDE", "vista_".VISTA_NECESITA."_1.xml"); 				//El xml que depende para sacar todos sus datos
    define ("RUTA_XML_DEPENDE", "../VistasXml/Vista".VISTA_NECESITA."/"); 	//La ruta del xml que necesita para completar datos
    
    include 'comun.php';
    
    if ($archivoCSV !== false) {
        $arrayProvincias = obtenerURL (URL_CLAVE_PRO); 
        $codigosVistaNecesita = array (); //Codiogos de municipios de la vista que necesita
        
        //Obtenermos los datos del xml que depende, del cual depende para poder realizar el csv
        if (file_exists (RUTA_XML_DEPENDE)) {
            
            $datosArchivo = file_get_contents (RUTA_XML_DEPENDE.XML_DEPENDE);
            $xmlDepende = simplexml_load_string($datosArchivo);
            
            
            
            for ($i = 0; $i < ($xmlDepende->count ()); $i++) {
                $claveTiene = $xmlDepende->item[$i]->{CLAVE_TIENE_DEPENDE}->__toString();
                $claveNecesita = $xmlDepende->item[$i]->{CLAVE_NECESITA}->__toString();
                
                $claveTieneSinSaltos = preg_replace("/\r|\n/", "", $claveTiene);
                $claveNecesitaSinSaltos = preg_replace("/\r|\n/", "", $claveNecesita);
                $claveTieneSinSaltos = trim($claveTieneSinSaltos);
                $claveNecesitaSinSaltos = trim($claveNecesitaSinSaltos);
                
                $codigosVistaNecesita [$claveTieneSinSaltos] = [$claveNecesitaSinSaltos];
            }
            
            
            
        }
        
        array_push ($keys,CLAVE_NECESITA); //Le añadimos la clave que necesita y no la tiene el xml
        fwrite ($archivoCSV, "\"".CLAVE_NECESITA."\";"); //y la añadidomos al csv
        
        array_push ($keys,CLAVE_PRO); //Le añadimos la clave que necesita y no la tiene el xml
        fwrite ($archivoCSV, "\"".CLAVE_PRO."\";"); //y la añadidomos al csv
        
        fwrite ($archivoCSV, "\n"); //introducimos un salto de linea para separar las keys del resto de los elemntos
        
        //se leen los archivos xml de la vista de los datos y se crea el archivo csv correspondientes a la vista
        for ($i = 1; $i <= $numeroArchivos; $i++) {
            $datosXml2 = file_get_contents (RUTA_XML."vista_".$vista."_$i.xml");
            $xml2 = simplexml_load_string($datosXml2);
            
            for ($z = 0; $z < ($xml2->count ()); $z++) {
                foreach ($keys as $key) {
                    $elemento = $xml2->item[$z]->$key;
                    
                    if ($key == CLAVE_NECESITA){ //Si es el elemento del codigo de provincia que no esta en el xml se busca en el array creado antes y se inserta en el documento
                        $idTiene = $xml2->item[$z]->{CLAVE_TIENE}->__toString();
                        $idTiene = preg_replace("/\r|\n/", "", $idTiene);	//Quitamos los saltos de linea porque sino da error
                        $idTiene = mb_strtoupper($idTiene);
                        $idNecesita = $codigosVistaNecesita[$idTiene];
                        $elemento = $idNecesita [0]; //OJO, obtenemos el codigo de municipio, porque la linea anterior devuelve un array
                    }
                    
                    if ($key == CLAVE_PRO) {
                        $idPro =  $xml2->item[$z]->{COD_PRO}->__toString();
                        $elemento = $arrayProvincias [$idPro];
                    }
                    
                    if ($key == CLAVE_URL) {
                        $elemento = obtenerUrlVinculacionTresClaves($xml2, $z, $vista, CLAVE_URI1, CLAVE_URI2, CLAVE_URI3);
                    }
                    
                    editarElemento($elemento);
                    
                    fwrite ($GLOBALS["archivoCSV"], "\"$elemento\";");
                }
                
                fwrite($GLOBALS["archivoCSV"], "\n");
            }
        }
        
        fclose ($GLOBALS["archivoCSV"]);
    }
?>