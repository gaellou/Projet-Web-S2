<?php

function selectAllTowns($conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT v.id AS 'id_ville', v.nom_ville AS 'nom_ville', v.code_postal AS 'code_postal'
		FROM Ville AS v
		ORDER BY v.nom_ville
SQL
		);


	$req->execute();

	$nbVilles = 0;
	while( ($ville = $req->fetch()) !== false )
	{
		$resultat[$nbVilles]['ville']['id'] = intval($ville['id_ville']);

		$resultat[$nbVilles]['ville']['nom'] = $ville['nom_ville'];
		$resultat[$nbVilles]['ville']['code_postal'] = intval($ville['code_postal']);
		$nbVilles++;
	}
	$resultat['nombre'] = $nbVilles;
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

$resultat = selectAllTowns($conn);

echo json_encode($resultat);
header( http_response_code(200) );

?>