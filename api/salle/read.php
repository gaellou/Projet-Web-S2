<?php

function selectAllVenues($conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT sa.id AS 'id_salle', sa.nom_salle AS 'nom_salle', sa.capacite AS 'capacite',
			sa.id_ville AS 'id_ville', v.nom_ville AS 'nom_ville', v.code_postal AS 'code_postal'
		FROM Salle AS sa
		INNER JOIN Ville AS v ON v.id = sa.id_ville
		ORDER BY sa.nom_salle
SQL
		);


	$req->execute();

	$nbSalles = 0;
	while( ($salle = $req->fetch()) !== false )
	{
		$resultat[$nbSalles]['salle']['id'] = $salle['id_salle'];

		$resultat[$nbSalles]['salle']['nom'] = $salle['nom_salle'];
		$resultat[$nbSalles]['salle']['capacite'] = intval($salle['capacite']);

		$resultat[$nbSalles]['ville']['id'] = $salle['id_ville'];
		$resultat[$nbSalles]['ville']['nom'] = $salle['nom_ville'];
		$resultat[$nbSalles]['ville']['code_postal'] = intval($salle['code_postal']);
		$nbSalles++;
	}
	$resultat['nombre'] = $nbSalles;
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

$resultat = selectAllVenues($conn);

echo json_encode($resultat);
header( http_response_code(200) );

?>