<?php

/*Crée un musicien à partir de :
* - son nom, prénom, sa date de naissance  et l'id de sa ville,
* - facultativement des id des intstruments (avec année de début)
* et genres favoris.
*/


header("Content-Type: application/json; charset=UTF-8");

$method = strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "post"  || !isset($_POST) )
{
	header(http_response_code(405));
	echo json_encode(array('message' => 'Cette méthode est inacceptable.'));
	exit();
}

/** RÉCUP INFOS CLIENT **/

require_once 'check.php';
require_once '../data/commun.php';

//
$musicien = array();
$musicien['nom'] = isset($_POST['nom']) ? $_POST['nom'] : NULL;
$musicien['prenom'] = isset($_POST['prenom']) ? $_POST['prenom'] : NULL;
$musicien['date_naissance'] = isset($_POST['date_naissance']) ? $_POST['date_naissance'] : NULL;
//
if( isset($_POST['ville']) )
	$ville['id'] = intval($_POST['ville']);
else
	$ville = NULL;
//


if( isset($_POST['genres']) )
{
	$idGenres = isset($_POST['genres']) ? castArrayInt( explode(',',$_POST['genres']) ) : NULL;
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
if( isset($_POST['instruments']) && isset($_POST['annee_debut']) )
{
	$instruments = array();
	$idInstruments = castArrayInt( explode(',',$_POST['instruments']) );
	$annee_debut = explode(',', $_POST['annee_debut']);
	foreach( $idInstruments as $indice => $idInstrument )
	{
		$instruments[$indice]['id'] = $idInstrument;
		$instruments[$indice]['annee_debut'] = $annee_debut[$indice];
	}
	unset($idInstruments);
	unset($annee_debut);
}
else
{
	$instruments = NULL;
}
//
/** FIN RÉCUP CLIENT **/

require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

/** VÉRIFICATIONS **/



if( !checkPost($musicien, $ville, $genres, $instruments, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$reponse = createMusician($musicien, $ville, $instruments, $genres, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>