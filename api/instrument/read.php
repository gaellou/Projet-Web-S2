<?php

function selectAllInstruments($conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT i.id AS 'id_instrument', i.nom_instrument AS 'nom_instrument'
		FROM Instrument AS i
		ORDER BY i.nom_instrument
SQL
		);
	$req->execute();


	$nbGenres = 0;
	while( ($instrument = $req->fetch()) !== false )
	{
		$resultat[$nbInstruments]['instrument']['id'] = $instrument['id_instrument'];
		$resultat[$nbInstruments]['instrument']['nom'] = $instrument['nom_instrument'];
		$nbInstruments++;
	}
	$resultat['nombre'] = $nbInstruments;
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


$resultat = selectAllInstruments($conn);

echo json_encode($resultat);
header( http_response_code(200) );

?>