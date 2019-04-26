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
if( !isset($_GET["id"]) || !checkID(intval($_GET["id"]), 'id', 'Genre', $conn) )
{
	$message = array( "message" => "Identifiant incorrect ou absent." );
	echo json_encode($message);

	header(http_response_code(406));
	exit();
}


require_once "function.php";

$id = intval($_GET["id"]);
$resultat = selectGenre($id, $conn);


if( !$resultat )
{
	$message = array( "message" => "Erreur, genre à l'id {$id} non trouvé (n'est pas censé se produire)." );
	echo json_encode($message);

	header(http_response_code(406));
	exit();
}

echo json_encode($resultat);
header( http_response_code(200) );

?>