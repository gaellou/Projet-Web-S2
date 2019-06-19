
<?php

function selectAllGigs($conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT co.id AS 'id_concert', co.date_concert AS 'date_concert', co.id_groupe AS 'id_groupe',  co.id_salle AS 'id_salle',
			gr.nom_groupe AS 'nom_groupe',
			sa.nom_salle AS 'nom_salle',
			sa.capacite AS 'capacite_salle'
		FROM Concert AS co
		INNER JOIN Groupe AS gr ON gr.id = co.id_groupe
		INNER JOIN Salle AS sa ON sa.id = co.id_salle
		ORDER BY co.date_concert
SQL
		);


	$req->execute();


	$nbConcerts = 0;
	while( ($concert = $req->fetch()) !== false )
	{
		$resultat[$nbConcerts]['concert']['id'] = intval($concert['id_concert']);
		$resultat[$nbConcerts]['concert']['date_concert'] = $concert['date_concert'];

		$resultat[$nbConcerts]['groupe']['id'] = intval($concert['id_groupe']);
		$resultat[$nbConcerts]['groupe']['nom'] = $concert['nom_groupe'];

		$resultat[$nbConcerts]['salle']['id'] =  intval($concert['id_salle']);
		$resultat[$nbConcerts]['salle']['nom'] =  $concert['nom_salle'];
		$nbConcerts++;
	}
	$resultat['nombre'] = $nbConcerts;
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

require_once '../data/MyPDO.musiciens-groupes.include.php';

$conn = MyPDO::getInstance();


$resultat = selectAllGigs($conn);

sort($resultat);

echo json_encode($resultat);
header( http_response_code(200) );

?>