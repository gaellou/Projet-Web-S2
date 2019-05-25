<?php

/* Recherche une ville à partir de :
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

//
$ville = array();
$ville['nom'] = isset($_REQUEST['nom']) ? $_REQUEST['nom'] : NULL;
$ville['code_postal'] = isset($_REQUEST['code_postal']) ? intval($_REQUEST['code_postal']) : NULL;
//

//
/** FIN RÉCUP CLIENT **/


/** VÉRIFICATIONS **/
require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

require_once '../data/commun.php';
require_once 'check.php';

if( !isset($_REQUEST["id"]) || !checkID(intval($_REQUEST["id"]), 'id', 'Ville', $conn) )
{
	
	$message = array( "message" => "Identifiant absent ou incorrect.",
						'id' => $_REQUEST['id']);
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

$id = intval($_REQUEST['id']);

if( !checkPut($ville) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(404));
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