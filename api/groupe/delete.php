<?php


/*Supprime un groupe à partir de :
* - son id,
* Renvoie un message d'erreur si l'id ne correspond à aucun groupe.
*/

header("Content-Type: application/json; charset=UTF-8");

$method = strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== 'delete' )
{
	header(http_response_code(405));
	echo json_encode(array('message' => 'Cette méthode est inacceptable.'));
	exit();
}

require_once 'check.php';
require_once '../data/MyPDO.musiciens-groupes.include.php';


$conn = MyPDO::getInstance();

/** VÉRIF **/
require_once '../data/commun.php';
if( !isset($_GET["id"]) || !checkID(intval($_GET["id"]), 'id', 'Groupe', $conn) )
{
	$message = array( "message" => "Identifiant incorrect ou absent, échec de suppression." );
	echo json_encode($message);

	header(http_response_code(406) );
	exit();
}

/** TRAITEMENT **/
require_once 'function.php';


$id = intval($_GET["id"]);
deleteBand($id, $conn);

$message = array(
				'id' => $id,	
				'message' => 'Groupe supprimé avec succès.'
			);
echo json_encode($message);

header( http_response_code(200) );

?>

