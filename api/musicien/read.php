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
include_once '../data/MyPDO.musiciens-groupes.include.php';

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
SQL
	);


$req->execute();

while( ($musicien = $req->fetch()) != false )
{
	$id = intval( $musicien['id_mu'] );

	if( !isset($nbGenres[$id]) )
	{
		$musiciens[$id]['genres'] = array();
		$nbGenres[$id] = 0;
	}
	if( !isset($nbInstruments[$id]) )
	{
		$musiciens[$id]['instruments'] = array();
		$nbInstruments[$id] = 0;
	}	

	//musicien en tant que tel
	$musiciens[$id]['musicien']['id'] = $id;
	$musiciens[$id]['musicien']['nom'] = $musicien['nom_mu'];
	$musiciens[$id]['musicien']['prenom'] = $musicien['prenom_mu'];
	$musiciens[$id]['musicien']['date_naissance'] = $musicien['date_naissance'];

	//ville
	$musiciens[$id]['ville']['id'] =  intval($musicien['id_ville']);
	$musiciens[$id]['ville']['nom'] =  $musicien['nom_ville'];


	//genres
	if( !isElement( intval($musicien['id_genre']), $musiciens[$id]['genres'], 'id' ) )
	{
		$musiciens[$id]['genres'][$nbGenres[$id]]['id'] = intval($musicien['id_genre']);
		$musiciens[$id]['genres'][$nbGenres[$id]]['nom'] = $musicien['nom_genre'];
		$nbGenres[$id] += 1;

	}

	//instruments
	if( !isElement( intval($musicien['id_instrument']), $musiciens[$id]['instruments'], 'id' ) )
	{
		$musiciens[$id]['instruments'][(int)$nbInstruments[$id]]['id'] = intval($musicien['id_instrument']);
		$musiciens[$id]['instruments'][(int)$nbInstruments[$id]]['nom'] = $musicien['nom_instrument'];
		$musiciens[$id]['instruments'][(int)$nbInstruments[$id]]['annee_debut'] = $musicien['annee_debut'];
		$nbInstruments[$id] += 1;

	}
}

sort($musiciens);
echo json_encode($musiciens);
header( http_response_code(200) );




?>
