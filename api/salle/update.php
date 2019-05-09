<?php

/*Mets à jour une salle  à partir de son id :
* - son nom, sa capacité, l'id de sa ville
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
if( !isset($_REQUEST["id"]) || !checkID(intval($_REQUEST["id"]), 'id', 'Salle', $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Identifiant absent ou incorrect." );
	echo json_encode($message);
	exit();
}

$id = intval($_REQUEST["id"]);

//
$salle = array('id' => $id);
$salle['nom'] = isset($_REQUEST['nom']) ? $_REQUEST['nom'] : NULL;
$salle['capacite'] = isset($_REQUEST['capacite']) ? intval($_REQUEST['capacite']) : NULL;
//
$ville = array();
$ville['id'] = isset($_POST['ville']) ? intval($_POST['ville']) : NULL;
//

/** FIN RÉCUP CLIENT **/


/** VÉRIFICATIONS **/

if( !checkPut($salle, $ville, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$ancien = selectVenue($id, $conn);

$reponse = updateVenue($ancien, $salle, $ville, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>