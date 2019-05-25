<?php

/* Recherche une salle  à partir de  :
* - son nom, sa capacité, l'id de sa ville
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


// on cherche les capacités dans une fourchette de valeurs.
$salle['nom'] = isset($_GET['nom']) ? $_GET['nom'] : NULL;
$salle['capacite_min'] = isset($_GET['capacite_min']) ? intval($_GET['capacite_min']) : NULL;
$salle['capacite_max'] = isset($_GET['capacite_max']) ? intval($_GET['capacite_max']) : NULL;
//
$ville = array();
$ville['id'] = isset($_GET['ville']) ? intval($_GET['ville']) : NULL;
//

/** FIN RÉCUP CLIENT **/


/** VÉRIFICATIONS **/

require_once 'check.php';
require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

if( !checkSearch($salle, $ville, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$reponse = searchVenue($salle, $ville, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>