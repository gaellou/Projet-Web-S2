<?php

/*Un musicien, à partir de :
* - Nom, prénom, date de naissance,
* - l'id de sa ville,
* - les id des genres,
* - les id des instruments
*/


header("Content-Type: application/json; charset=UTF-8");


$method =strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "get")
{
	header(http_response_code(405));
	$message = array( "message" => "Méthode non supportée." );
	echo json_encode($message);
	exit();
}


/** RÉCUP INFOS CLIENT **/


require_once "check.php";
require_once "../data/commun.php";
require_once "../data/MyPDO.musiciens-groupes.include.php";



//
$musicien = array();
$musicien['nom'] = isset($_GET['nom']) ? $_GET['nom'] : NULL;
$musicien['prenom'] = isset($_GET['prenom']) ? $_GET['prenom'] : NULL;
//
$dates['apres'] = isset($_GET['date_apres']) ? $_GET['date_apres'] : NULL;
$dates['avant'] = isset($_GET['date_avant']) ? $_GET['date_avant'] : NULL;
//
if( isset($_GET['ville']) )
{
	$ville = array();
	$ville['id'] = intval($_GET['ville']);
}
else
	$ville = NULL;
//
if( isset($_GET['genres']) )
{
	$idGenres = castArrayInt( explode(',',$_GET['genres']) );
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
if( isset($_GET['instruments']) )
{
	$instruments = array();
	$idInstruments = castArrayInt( explode(',',$_GET['instruments']) );
	if( !in_array(-1, $idInstruments) )
	{
		foreach( $idInstruments as $index => $idInstrument )
		{
			$instruments[$index]['id'] = $idInstrument;
		}
	}
	unset($idInstruments);
	
}
else
	$instruments = NULL;
//

$conn = MyPDO::getInstance();


/** VÉRIF **/
if( !checkSearch($musicien, $dates, $ville, $genres, $instruments, $conn) )
{
	header(http_response_code(404));
	$message = array( "message" => "Arguments inacceptables." );
	echo json_encode($message);
	exit();
}

/** TRAITEMENT **/

require_once "function.php";

$resultat = searchMusician($musicien, $dates, $ville, $instruments, $genres, $conn);

echo json_encode($resultat);
header( http_response_code(200) );

?>