<?php

/*Un musicien, à partir de :
* - son id,
* Mets à jour :
* - Nom, prénom, date de naissance,
* - l'id de sa ville,
* - les id des genres,
* - les id des instruments, pratique
* À NOTER : les id de genres et instruments égaux à -1 correspondent
* à la suppression de tous les id de chacun
*/


header("Content-Type: application/json; charset=UTF-8");


$method =strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "put")
{
	header(http_response_code(405));
	$message = array( "message" => "Méthode non supportée." );
	echo json_encode($message);
	exit();
}


/** RÉCUP INFOS CLIENT **/


require_once "check.php";
require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

require_once '../data/commun.php';
if( !isset($_REQUEST["id"]) || !checkID(intval($_REQUEST["id"]), 'id', 'Musicien', $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Identifiant absent ou incorrect." );
	echo json_encode($message);
	exit();
}

$id = intval($_REQUEST["id"]);



//
$musicien = array();
$musicien['nom'] = isset($_REQUEST['nom']) ? $_REQUEST['nom'] : NULL;
$musicien['prenom'] = isset($_REQUEST['prenom']) ? $_REQUEST['prenom'] : NULL;
$musicien['date_naissance'] = isset($_REQUEST['date_naissance']) ? $_REQUEST['date_naissance'] : NULL;
//
if( isset($_REQUEST['ville']) )
{
	$ville = array();
	$ville['id'] = intval($_REQUEST['ville']);
}
else
	$ville = NULL;
//
if( isset($_REQUEST['genres']) )
{
	$idGenres = castArrayInt( explode(',',$_REQUEST['genres']) );
	$genres = array();
	foreach ($idGenres as $indice => $idGenre) 
	{
		$genres[$indice]['id'] = $idGenre;
	}
	unset($idGenres);
}
else
	$genres = NULL;

//
if( isset($_REQUEST['instruments']) )
{
	$instruments = array();
	$idInstruments = castArrayInt( explode(',',$_REQUEST['instruments']) );
	if( !in_array(-1, $idInstruments) )
	{
		$annee_debut = explode(',', $_REQUEST['annee_debut']);
		foreach( $idInstruments as $index => $idInstrument )
		{
			$instruments[$index]['id'] = $idInstrument;
			$instruments[$index]['annee_debut'] = $annee_debut[$index];
		}
		unset($annee_debut);
	}
	unset($idInstruments);
	
}
else
	$instruments = NULL;
//



/** VÉRIF **/
if( !checkPut($musicien, $ville, $genres, $instruments, $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Arguments inacceptables." );
	echo json_encode($message);
	exit();
}

/** TRAITEMENT **/

require_once "function.php";

$ancien = selectMusician($id, $conn);

$resultat = updateMusician($ancien, $musicien, $ville, $instruments, $genres, $conn);

echo json_encode($resultat);
header( http_response_code(200) );

?>