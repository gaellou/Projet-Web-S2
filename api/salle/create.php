<?php

/*Crée une salle à partir de :
* - son nom, sa capacité
* - id de la ville
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

//
$salle = array();
$salle['nom'] = isset($_POST['nom']) ? $_POST['nom'] : NULL;
$salle['capacite'] = isset($_POST['capacite']) ? intval($_POST['capacite']) : NULL;

//
$ville = array();
$ville['id'] = isset($_POST['ville']) ? intval($_POST['ville']) : NULL;
//

//
/** FIN RÉCUP CLIENT **/

require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

/** VÉRIFICATIONS **/

if( !checkPost($salle, $ville, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$reponse = createVenue($salle, $ville, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>