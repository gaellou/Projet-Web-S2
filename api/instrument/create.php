<?php

/*Crée un instrument à partir de :
* - son nom
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

//
$instrument = array();
$instrument['nom'] = isset($_POST['nom']) ? $_POST['nom'] : NULL;
//
/** FIN RÉCUP CLIENT **/


require_once 'check.php';
/** VÉRIFICATIONS **/

if( !checkPost($instrument) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$conn = MyPDO::getInstance();

$reponse = createInstrument($instrument, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>