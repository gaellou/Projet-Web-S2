<?php



/*Un musicien, à partir de :
* - son id,
* Mets à jour :
* - Nom, prénom, date de naissance,
* - l'id de sa ville,
* - les id des genres,
* - les id des instruments, pratique
* À NOTER : les id de genres et instruments égaux à -1 correspondent
* à la suppression de tous les id de chacun
*/


header("Content-Type: application/json; charset=UTF-8");


$method =strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "get")
{
	header(http_response_code(405));
	$message = array( "message" => "Méthode non supportée." );
	echo json_encode($message);
	exit();
}


/** RÉCUP INFOS CLIENT **/


require_once "check.php";
require_once '../data/commun.php';
require_once "../data/MyPDO.musiciens-groupes.include.php";


////
$groupe = array();
$groupe['nom'] = isset($_GET['nom']) ? $_GET['nom'] : NULL;
//
if ( isset($_GET['genre']) )
	$genre = array( 'id' => intval($_GET['genre']) );
else
	$genre = NULL;
//

//
if( isset($_GET['musiciens']) )
{
	$musiciens = array();
	$idMusiciens = castArrayInt( explode(',',$_GET['musiciens']) );
	foreach( $idMusiciens as $indice => $idMusicien )
	{
		$musiciens[$indice]['id'] = $idMusicien;
	}
	unset($idMusiciens);
}
else
	$musiciens = NULL;
//
/** FIN RÉCUP CLIENT **/

if( isset($_GET['instruments']) )
{
	$instruments = array();
	$idInstruments = castArrayInt( explode(',',$_GET['instruments']) );
	foreach( $idInstruments as $index => $idInstrument )
	{
			$instruments[$index]['id'] = $idInstrument;
	}
	unset($idInstruments);
	
}
else
	$instruments = NULL;
//

$conn = MyPDO::getInstance();

/** VÉRIF **/
if( !checkSearch($groupe, $genre, $musiciens, $instruments, $conn) )
{
	header(http_response_code(406));
	$message = array( "message" => "Arguments inacceptables." );
	echo json_encode($message);
	exit();
}

/** TRAITEMENT **/

require_once "function.php";

$resultat = searchBand($groupe, $genre, $musiciens, $instruments, $conn);

echo json_encode($resultat);
header( http_response_code(200) );

?>