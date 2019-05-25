<?php


header("Content-Type: application/json; charset=UTF-8");

$method = strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "get" )
{
	header(http_response_code(405));
	echo json_encode(array('message' => 'Cette méthode est inacceptable.'));
	exit();
}

require_once 'check.php';
require_once '../data/MyPDO.musiciens-groupes.include.php';

$conn = MyPDO::getInstance();

require_once '../data/commun.php';
if( !isset($_GET["id"]) || !checkID(intval($_GET["id"]), 'id', 'Musicien', $conn) )
{
	$message = array( "message" => "Identifiant incorrect ou absent.",
						'id' => $_GET['id']);
	echo json_encode($message);

	header(http_response_code(404));
	exit();
}


require_once "function.php";

$id = intval($_GET["id"]);
$resultat = selectMusician($id, $conn);


if( !$resultat )
{
	$message = array( "message" => "Erreur, musicien à l'id {$id} non trouvé." );
	echo json_encode($message);

	header(http_response_code(404));
	exit();
}

echo json_encode($resultat);
header( http_response_code(200) );

?>