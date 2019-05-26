<?php

function selectAllMusicians($conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';
	require_once '../data/commun.php';


	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT mu.id AS 'id_mu', mu.nom_musicien AS 'nom_mu', mu.prenom_musicien AS 'prenom_mu', mu.date_naissance AS 'date_naissance',
			 v.id AS 'id_ville', v.nom_ville AS 'nom_ville', 
			 g.id AS 'id_genre', g.nom_genre AS 'nom_genre', 
			 i.id AS 'id_instrument', i.nom_instrument AS 'nom_instrument',
			 p.id AS 'id_pratique', p.annee_debut AS 'annee_debut'
		FROM Musicien AS mu
		LEFT JOIN Ville AS v ON v.id = mu.id_ville
		LEFT JOIN Aime AS a ON a.id_musicien = mu.id
		LEFT JOIN Genre AS g ON g.id = a.id_genre
		LEFT JOIN Pratique AS p ON p.id_musicien = mu.id
		LEFT JOIN Instrument AS i ON i.id = p.id_instrument
SQL
		);


	$req->execute();

	$nbMusiciens = 0;
	while( ($musicien = $req->fetch()) !== false )
	{
		$id = intval( $musicien['id_mu'] );

		if( !isset($nbGenres[$id]) )
		{
			$resultat[$id]['genres'] = array();
			$nbGenres[$id] = 0;
		}
		if( !isset($nbInstruments[$id]) )
		{
			$resultat[$id]['instruments'] = array();
			$nbInstruments[$id] = 0;
		}	

		//musicien en tant que tel
		if( !isset(	$resultat[$id]['musicien']) )
		{
			$resultat[$id]['musicien']['id'] = $id;
			$resultat[$id]['musicien']['nom'] = $musicien['nom_mu'];
			$resultat[$id]['musicien']['prenom'] = $musicien['prenom_mu'];
			$resultat[$id]['musicien']['date_naissance'] = $musicien['date_naissance'];
			$nbMusiciens++;
		}

		//ville
		$resultat[$id]['ville']['id'] =  intval($musicien['id_ville']);
		$resultat[$id]['ville']['nom'] =  $musicien['nom_ville'];


		//genres
		if( !isElement( intval($musicien['id_genre']), $resultat[$id]['genres'], 'id' ) )
		{
			$resultat[$id]['genres'][$nbGenres[$id]]['id'] = intval($musicien['id_genre']);
			$resultat[$id]['genres'][$nbGenres[$id]]['nom'] = $musicien['nom_genre'];
			$nbGenres[$id] += 1;

		}

		//instruments
		if( !isElement( intval($musicien['id_instrument']), $resultat[$id]['instruments'], 'id' ) )
		{
			$resultat[$id]['instruments'][(int)$nbInstruments[$id]]['id'] = intval($musicien['id_instrument']);
			$resultat[$id]['instruments'][(int)$nbInstruments[$id]]['nom'] = $musicien['nom_instrument'];
			$resultat[$id]['instruments'][(int)$nbInstruments[$id]]['annee_debut'] = $musicien['annee_debut'];
			$resultat[$id]['instruments'][(int)$nbInstruments[$id]]['id_pratique'] = intval($musicien['id_pratique']);
			$nbInstruments[$id] += 1;

		}
	}
	$resultat['nombre'] = $nbMusiciens;
	return $resultat;
}




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

$resultat = selectAllMusicians($conn);

sort($resultat);
echo json_encode($resultat);
header( http_response_code(200) );




?>
