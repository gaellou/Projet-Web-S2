<?php

function selectAllBands($conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';
		require_once '../data/commun.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT gr.id AS 'id_gr', gr.nom_groupe AS 'nom_gr', gr.id_genre AS 'id_genre',
			g.nom_genre AS 'nom_genre',
			mb.date_entree AS 'date_entree',
			m.id AS 'id_mu', m.nom_musicien AS 'nom_mu', m.prenom_musicien AS 'prenom_mu',
			i.id AS 'id_instrument', i.nom_instrument AS 'nom_instrument'
		FROM Groupe AS gr
		INNER JOIN Genre AS g ON g.id = gr.id_genre
		LEFT JOIN Membre AS mb ON mb.id_groupe = gr.id
		LEFT JOIN Pratique AS p ON p.id = mb.id_pratique
		LEFT JOIN Musicien AS m ON m.id = p.id_musicien
		LEFT JOIN Instrument AS i ON i.id = p.id_instrument
SQL
		);


	$req->execute();

	$nbGroupes = 0;
	while( ($groupe = $req->fetch()) !== false )
	{
		$id = $groupe['id_gr'];
		//première itération pour un groupe
		if( !isset($resultat[$id]['groupe']  ) )
		{
			//groupe en tant que tel
			$resultat[$id]['groupe']['id'] = $id;
			$resultat[$id]['groupe']['nom'] = $groupe['nom_gr'];

			//genre
			$resultat[$id]['genre']['id'] =  intval($groupe['id_genre']);
			$resultat[$id]['genre']['nom'] =  $groupe['nom_genre'];

			$resultat[$id]['membres'] = array();
			$nbMembres[$id] = 0;
			$nbGroupes++;
		}
		$augmenterNbMembres = false;


		//membres (musiciens)
		if( isset($groupe['id_mu'])
			&& !isElement2( intval($groupe['id_mu']), $resultat[$id]['membres'], 'musicien','id' ) ) 
		{
			$resultat[$id]['membres'][$nbMembres[$id]]['musicien']['id'] = intval($groupe['id_mu']);
			$resultat[$id]['membres'][$nbMembres[$id]]['musicien']['nom'] = $groupe['nom_mu'];
			$resultat[$id]['membres'][$nbMembres[$id]]['musicien']['prenom'] = $groupe['prenom_mu'];
			$augmenterNbMembres = true;
		}

		//instruments
		if( !isset($nbInstruments[$nbMembres[$id]]) )
		{
			$resultat[$id]['membres'][$nbMembres[$id]]['instruments'] = array();
			$nbInstruments[$nbMembres[$id]] = 0;
		}
		if( isset($groupe['id_instrument'])
			&& !isElement( intval($groupe['id_instrument']), $resultat[$id]['membres'][$nbMembres[$id]]['instruments'], 'id' ) )
		{
			$resultat[$id]['membres'][$nbMembres[$id]]['instruments'][$nbInstruments[$nbMembres[$id]]]['id'] = intval($groupe['id_instrument']);
			$resultat[$id]['membres'][$nbMembres[$id]]['instruments'][$nbInstruments[$nbMembres[$id]]]['nom'] = $groupe['nom_instrument'];
			$resultat[$id]['membres'][$nbMembres[$id]]['instruments'][$nbInstruments[$nbMembres[$id]]]['date_entree'] = $groupe['date_entree'];
			$nbInstruments[$nbMembres[$id]] += 1;

		}
		if( $augmenterNbMembres )
			$nbMembres[$id] += 1;
	}
	$resultat['nombre'] = $nbGroupes;
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

$resultat = selectAllBands($conn);

sort($resultat);
echo json_encode($resultat);
header( http_response_code(200) );




?>
