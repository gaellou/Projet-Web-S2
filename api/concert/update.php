<?php

/*Crée un concert à partir de :
* - son nom, prénom, sa date de naissance  et l'id de sa salle,
* - facultativement des id des intstruments (avec année de début)
* et genres favoris.
*/


header("Content-Type: application/json; charset=UTF-8");

$method = strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "put"  || !isset($_REQUEST) )
{
	header(http_response_code(405));
	echo json_encode(array('message' => 'Cette méthode est inacceptable.'));
	exit();
}

/** RÉCUP INFOS CLIENT **/

require_once 'check.php';
require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

require_once '../data/commun.php';
if( !isset($_REQUEST["id"]) || !checkID(intval($_REQUEST["id"]), 'id', 'Concert', $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Identifiant absent ou incorrect." );
	echo json_encode($message);
	exit();
}

$id = intval($_REQUEST["id"]);

//
$concert = array('id' => $id);
$concert['date_concert'] = isset($_REQUEST['date_concert']) ? $_REQUEST['date_concert'] : NULL;
//
if( isset($_REQUEST['groupe']) )
	$groupe['id'] = intval($_REQUEST['groupe']);
else
	$groupe = NULL;
//
if( isset($_REQUEST['salle']))
	$salle['id'] = intval($_REQUEST['salle']);
else
	$salle = NULL;
//

//
/** FIN RÉCUP CLIENT **/


/** VÉRIFICATIONS **/

if( !checkPut($concert, $groupe, $salle, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$ancien = selectGig($id, $conn);

$reponse = updateGig($ancien, $concert, $groupe, $salle, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>