<?php

/*Mets à jour une ville à partir de son id:
* - son nom, code postal
*/


header("Content-Type: application/json; charset=UTF-8");

$method = strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "get"  || !isset($_GET) )
{
	header(http_response_code(405));
	echo json_encode(array('message' => 'Cette méthode est inacceptable.'));
	exit();
}

/** RÉCUP INFOS CLIENT **/
//
$ville = array();
$ville['nom'] = isset($_GET['nom']) ? $_GET['nom'] : NULL;
$ville['code_min'] = isset($_GET['code_min']) ? intval($_GET['code_min']) : NULL;
$ville['code_max'] = isset($_GET['code_max']) ? intval($_GET['code_max']) : NULL;
//


//
/** FIN RÉCUP CLIENT **/



/** VÉRIFICATIONS **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'check.php';

$conn = MyPDO::getInstance();


if( !checkSearch($ville) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

/** REQUÊTE BDD **/
require_once 'function.php';

$reponse = searchTown($ville, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>