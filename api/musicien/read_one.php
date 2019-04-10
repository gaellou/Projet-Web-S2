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


if( !isset($_GET["id"]) || !checkID(intval($_GET["id"]), 'id', 'Musicien') )
{
	$message = array( "message" => "Identifiant incorrect ou absent." );
	echo json_encode($message);

	header(http_response_code(404));
	exit();
}


require_once '../data/MyPDO.musiciens-groupes.include.php';

$id = intval($_GET["id"]);


$conn = MyPDO::getInstance();

$req = $conn->prepare(<<<SQL
	SELECT mu.id AS 'id_mu', mu.nom_musicien AS 'nom_mu', mu.prenom_musicien AS 'prenom_mu', mu.date_naissance AS 'date_naissance',
		 v.id AS 'id_ville', v.nom_ville AS 'nom_ville', 
		 g.id AS 'id_genre', g.nom_genre AS 'nom_genre', 
		 i.id AS 'id_instrument', i.nom_instrument AS 'nom_instrument', p.annee_debut AS 'annee_debut'
	FROM Musicien AS mu
	INNER JOIN Ville AS v ON v.id = mu.id_ville
	INNER JOIN Aime AS a ON a.id_musicien = mu.id
	INNER JOIN Genre AS g ON g.id = a.id_genre
	INNER JOIN Pratique AS p ON p.id_musicien = mu.id
	INNER JOIN Instrument AS i ON i.id = p.id_instrument
	WHERE mu.id = {$id}
SQL
	);


$req->execute();


$musiciens['genres'] = array();
$nbGenres = 0;
$musiciens['instruments'] = array();
$nbInstruments = 0;

$found = false;
while( ($musicien = $req->fetch()) != false )
{
	$found = true;


	//musicien en tant que tel
	$musiciens['musicien']['id'] = $id;
	$musiciens['musicien']['nom'] = $musicien['nom_mu'];
	$musiciens['musicien']['prenom'] = $musicien['prenom_mu'];
	$musiciens['musicien']['date_naissance'] = $musicien['date_naissance'];

	//ville
	$musiciens['ville']['id'] =  intval($musicien['id_ville']);
	$musiciens['ville']['nom'] =  $musicien['nom_ville'];


	//genres
	if( !isElement( intval($musicien['id_genre']), $musiciens['genres'], 'id' ) )
	{
		$musiciens['genres'][$nbGenres]['id'] = intval($musicien['id_genre']);
		$musiciens['genres'][$nbGenres]['nom'] = $musicien['nom_genre'];
		$nbGenres += 1;

	}

	//instruments
	if( !isElement( intval($musicien['id_instrument']), $musiciens['instruments'], 'id' ) )
	{
		$musiciens['instruments'][$nbInstruments]['id'] = intval($musicien['id_instrument']);
		$musiciens['instruments'][$nbInstruments]['nom'] = $musicien['nom_instrument'];
		$musiciens['instruments'][$nbInstruments]['annee_debut'] = $musicien['annee_debut'];
		$nbInstruments += 1;

	}
}

if( !$found )
{
	$message = array( "message" => "Erreur, musicien à l'id {$id} non trouvé (n'est pas censé se produire)." );
	echo json_encode($message);

	header(http_response_code(405));
	exit();
}

echo json_encode($musiciens);
header( http_response_code(200) );

?>