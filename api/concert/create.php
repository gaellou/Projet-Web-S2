<?php

/*Crée un concert à partir de :
* - son nom,
* - id du groupe, et salle
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
$concert = array();
$concert['date_concert'] = isset($_POST['date_concert']) ? $_POST['date_concert'] : NULL;
//
$groupe = array();
$groupe['id'] = isset($_POST['groupe']) ? intval($_POST['groupe']) : NULL;
//
$salle = array();
$salle['id'] = isset($_POST['salle']) ? intval($_POST['salle']) : NULL;
//

//
/** FIN RÉCUP CLIENT **/

require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

/** VÉRIFICATIONS **/

if( !checkPost($concert, $groupe, $salle, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(404));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$reponse = createGig($concert, $groupe, $salle, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>