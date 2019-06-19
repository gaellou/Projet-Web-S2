<?php

function selectAllGenres($conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT g.id AS 'id_genre', g.nom_genre AS 'nom_genre'
		FROM Genre AS g
		ORDER BY g.nom_genre
SQL
		);
	$req->execute();


	$nbGenres = 0;
	while( ($genre = $req->fetch()) !== false )
	{
		$resultat[$nbGenres]['genre']['id'] = intval($genre['id_genre']);
		$resultat[$nbGenres]['genre']['nom'] = $genre['nom_genre'];
		$nbGenres++;
	}
	$resultat['nombre'] = $nbGenres;
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


$resultat = selectAllGenres($conn);


echo json_encode($resultat);
header( http_response_code(200) );

?>