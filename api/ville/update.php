<?php

/*Mets à jour une ville à partir de :
* - son nom, code postal
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

//
$ville = array();
$ville['nom'] = isset($_REQUEST['nom']) ? $_REQUEST['nom'] : NULL;
$ville['code_postal'] = isset($_REQUEST['code_postal']) ? intval($_REQUEST['code_postal']) : NULL;
//


//
/** FIN RÉCUP CLIENT **/

require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();



/** VÉRIFICATIONS **/
require_once '../data/commun.php';
if( !isset($_REQUEST["id"]) || !checkID(intval($_REQUEST["id"]), 'id', 'Ville', $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Identifiant absent ou incorrect." );
	echo json_encode($message);
	exit();
}

$id = intval($_REQUEST['id']);

if( !checkPut($ville, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/
require_once 'function.php';

$ancien= selectTown($id, $conn);

$reponse = updateTown($ancien, $ville, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>