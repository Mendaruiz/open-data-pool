<?php
    $vista = 9;
    define ("CLAVE_URI", "ELEC_ID");
	define ("VISTA_NECESITA", "11");										//El numero de la vista que necesita para completar sus datos
	define ("CLAVE_TIENE", "PARTIDOJUDICIAL");								//La clve que tiene para poder relacionarse
	define ("CLAVE_TIENE_DEPENDE", "DENOMINACION");                         //La clave que corresponde en el xml que depende
	define ("CLAVE_NECESITA","CODIGO_MUN"); 								//La clave que necesita
	define ("XML_DEPENDE", "vista_".VISTA_NECESITA."_1.xml"); 				//El xml que depende para sacar todos sus datos
	define ("RUTA_XML_DEPENDE", "../VistasXml/Vista".VISTA_NECESITA."/"); 	//La ruta del xml que necesita para completar datos
	
	include 'comun.php';
	
	if ($archivoCSV !== false) {
	    $codigosVistaNecesita = array (); //Codiogos de municipios de la vista que necesita
	    $codigosVistaNecesita ["ALCAÃ±IZ"] = [44013];
	    $codigosVistaNecesita ["ALMUNIA DE DOÃ±A GODINA, LA"] = [50025];
	    $codigosVistaNecesita ["BOLTAÃ±A"] = [22066];
	    $codigosVistaNecesita ["MONZÃ³N"] = [22158];
	    
	    crearCsvUnaDependencia2(CLAVE_URI, $codigosVistaNecesita);
	}	
	
?>