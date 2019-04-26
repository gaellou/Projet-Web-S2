<?php

/*Un groupe, à partir de :
* - son id,
* Mets à jour :
* - Nom,
* - l'id du genre,
* - les id des pratiques, et date d'entrées des membres,
* À NOTER : les id des pratiques égaux à -1 correspondent
* à la suppression de tous les id.
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
require_once '../data/commun.php';
require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

require_once '../data/commun.php';
if( !isset($_REQUEST["id"]) || !checkID(intval($_REQUEST["id"]), 'id', 'Groupe', $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Identifiant absent ou incorrect." );
	echo json_encode($message);
	exit();
}

$id = intval($_REQUEST["id"]);
/** RÉCUP INFOS CLIENT **/

require_once 'check.php';

//
$groupe = array();
$groupe['nom'] = isset($_REQUEST['nom']) ? $_REQUEST['nom'] : NULL;
//
if ( isset($_REQUEST['genre']) )
	$genre = array( 'id' => intval($_REQUEST['genre']) );
else
	$genre = NULL;
//

//
if( isset($_REQUEST['membres']) )
{
	$membres = array();
	$idMembres = castArrayInt( explode(',',$_REQUEST['membres']) );
	$date_entree = explode(',', $_REQUEST['date_entree']);
	foreach( $idMembres as $indice => $idMembre )
	{
		$membres[$indice]['id'] = $idMembre;
		$membres[$indice]['date_entree'] = $date_entree[$indice];
	}
	unset($idMembres);
	unset($date_entree);
}
else
	$membres = NULL;
//
/** FIN RÉCUP CLIENT **/


/** VÉRIF **/
if( !checkPut($groupe, $genre, $membres, $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Arguments inacceptables." );
	echo json_encode($message);
	exit();
}

/** TRAITEMENT **/

require_once "function.php";

$ancien = selectBand($id, $conn);

$resultat = updateBand($ancien, $groupe, $genre, $membres, $conn);

echo json_encode($resultat);
header( http_response_code(200) );

?>