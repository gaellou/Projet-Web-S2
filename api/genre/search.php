<?php

/*Crée un genre à partir de :
* - son nom
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
$genre = array();
$genre['nom'] = isset($_GET['nom']) ? $_GET['nom'] : NULL;
//
/** FIN RÉCUP CLIENT **/


/** VÉRIFICATIONS **/

require_once 'check.php';

if( !checkSearch($genre) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/

require_once 'function.php';
require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

$reponse = searchGenre($genre, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );


?>