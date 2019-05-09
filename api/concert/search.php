<?php

/* Recherche un concert à partir de :
* - sa date (entre deux dates)
* - l'id de sa salle,
* - l'id du groupe.
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
/* ici on cherche un concert entre deux dates */
$concert['date_avant'] = isset($_GET['date_avant']) ? $_GET['date_avant'] : NULL;
$concert['date_apres'] = isset($_GET['date_apres']) ? $_GET['date_apres'] : NULL;
//
if( isset($_GET['groupe']) )
	$groupe['id'] = intval($_GET['groupe']);
else
	$groupe = NULL;
//
if( isset($_GET['salle']))
	$salle['id'] = intval($_GET['salle']);
else
	$salle = NULL;
//

//
/** FIN RÉCUP CLIENT **/


/** VÉRIFICATIONS **/


require_once 'check.php';
require_once "../data/MyPDO.musiciens-groupes.include.php";


$conn = MyPDO::getInstance();

if( !checkSearch($concert, $groupe, $salle, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$reponse = searchGig($concert, $groupe, $salle, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>