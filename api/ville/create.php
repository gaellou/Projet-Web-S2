<?php

/*Crée une ville à partir de :
* - son nom, code postal
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
$ville = array();
$ville['nom'] = isset($_POST['nom']) ? $_POST['nom'] : NULL;
$ville['code_postal'] = isset($_POST['code_postal']) ? intval($_POST['code_postal']) : NULL;
//


//
/** FIN RÉCUP CLIENT **/

require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

/** VÉRIFICATIONS **/

if( !checkPost($ville, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

/** REQUÊTE BDD **/
require_once 'function.php';

$reponse = createTown($ville, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>