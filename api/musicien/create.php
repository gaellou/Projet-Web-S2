<?php

header("Content-Type: application/json; charset=UTF-8");

$method = strtolower($_SERVER["REQUEST_METHOD"]);
if( $method !== "post"  || !isset($_POST) )
{
	header(http_response_code(405));
	echo json_encode(array('message' => 'Cette méthode est inacceptable.'));
	exit();
}

require_once 'check.php';

//
$musicien = array();
$musicien['nom'] = $_POST['nom'];
$musicien['prenom'] = $_POST['prenom'];
$musicien['date_naissance'] = $_POST['date_naissance'];
$idVille = intval($_POST['ville']);
//

//
$genres = castArrayInt( explode(',',$_POST['genres']) );
//

//
$instruments = array();
$idInstruments = castArrayInt( explode(',',$_POST['instruments']) );
$annee_debut = explode(',', $_POST['annee_debut']);
foreach( $idInstruments as $index => $idInstrument )
{
	$instruments[$index]['id'] = $idInstrument;
	$instruments[$index]['annee_debut'] = $annee_debut[$index];
}
unset($idInstruments);
unset($annee_debut);
//

require_once "../data/MyPDO.musiciens-groupes.include.php";

$conn = MyPDO::getInstance();

$req_Musicien_text = <<<SQL
	INSERT INTO `Musicien` (`id`, `nom_musicien`, `prenom_musicien`, `date_naissance`, `id_ville`)
		VALUES ( NULL, "{$musicien['nom']}", "{$musicien['prenom']}", "{$musicien['date_naissance']}", {$idVille} )
SQL;

$req_Musicien = $conn->prepare($req_Musicien_text);

$req_Musicien->execute();
//On récupère l'identifiant
$id  = intval( $conn->lastInsertId() );

//On prépare les autres requêtes
$req_Aime = $conn->prepare(<<<SQL
	INSERT INTO `Aime` (`id_musicien`, `id_genre`)
		VALUES ({$id}, ?)
SQL
);

$req_Pratique = $conn->prepare(<<<SQL
	INSERT INTO `Pratique` (`id_musicien`, `id_instrument`, `annee_debut`)
		VALUES ({$id}, :id, :annee_debut)
SQL
);

//On les exécutent, pour tous les éléments des vecteurs
foreach ($genres as $idGenre) 
{
	$req_Aime->bindValue(1, $idGenre);
	$req_Aime->execute();
}
foreach ($instruments as $instrument) 
{
	$req_Pratique->execute( array( ':id' => $instrument['id'], 
						':annee_debut'=> $instrument['annee_debut']) );
}


if( !checkPost($musicien, $genres, $instruments) ) 
{
	$message = array( "message" => "Arguments incorrects ou absents." );
	echo json_encode($message);
	header(http_response_code(405));
	exit();
}

$musicien['id'] = $id;

echo json_incode($musicien);
header( http_response_code(200) );



?>