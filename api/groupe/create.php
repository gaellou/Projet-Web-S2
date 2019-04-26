<?php

/* Crée un groupe avec son nom, l'id de SON SEUL genre et :
	* - l'id des pratiques des membres, avec leur date d'entrée
	*
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
require_once '../data/commun.php';

//
$groupe = array();
$groupe['nom'] = isset($_POST['nom']) ? $_POST['nom'] : NULL;
//
if ( isset($_POST['genre']) )
	$genre = array( 'id' => intval($_POST['genre']) );
else
	$genre = NULL;
//



//

//
if( isset($_POST['membres']) )
{
	$membres = array();
	$idMembres = castArrayInt( explode(',',$_POST['membres']) );
	$date_entree = explode(',', $_POST['date_entree']);
	foreach( $idMembres as $indice => $idMembre )
	{
		$membres[$indice]['id'] = $idMembre;
		$membres[$indice]['date_entree'] = $date_entree[$indice];
	}
	unset($idMembres);
	unset($date_entree);
}
else
	$membres = NULL;
//
/** FIN RÉCUP CLIENT **/

require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

/** VÉRIFICATIONS **/



if( !checkPost($groupe, $genre, $membres, $conn) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(406));
	exit();
}

/** REQUÊTE BDD **/
require_once "../data/MyPDO.musiciens-groupes.include.php";
require_once 'function.php';

$reponse = createBand($groupe, $genre, $membres, $conn);


/** FIN TRAITEMENT **/

echo json_encode($reponse);
header( http_response_code(200) );



?>