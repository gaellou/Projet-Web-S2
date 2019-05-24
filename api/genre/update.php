<?php

/*Mets à jour un le nom d'un genre à partir de son id.
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
if( !isset($_REQUEST["id"]) || !checkID(intval($_REQUEST["id"]), 'id', 'Genre', $conn) )
{
	
	$message = array( "message" => "Identifiant absent ou incorrect.", 
						'id' => $_REQUEST['id']);
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

$id = intval($_REQUEST["id"]);

//
$genre = array();
$genre['nom'] = isset($_REQUEST['nom']) ? $_REQUEST['nom'] : NULL;
//
/** FIN RÉCUP CLIENT **/


/** VÉRIFICATIONS **/

if( !checkPut($genre) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

/** REQUÊTE BDD **/

require_once 'function.php';


$ancien = selectGenre($id, $conn);

$reponse = updateGenre($ancien, $genre, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>