<?php

function deleteTown($idVille, $conn)
{
	/* Supprime une ville, et
	* -fixe à NULL la ville des musiciens qui y habite.
	* - supprime les salles
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";
	require_once '../salle/function.php';


	$suppr_Ville = $conn->prepare(<<<SQL
		DELETE FROM `Ville`
			WHERE `id` = {$idVille}
SQL
	);

	$modif_Musicien = $conn->prepare(<<<SQL
		UPDATE `Musicien`
			SET `id_ville` = NULL
			WHERE `id_ville` = {$idVille}
SQL
	);

	deleteVenueFromTown($idVille, $conn);

	$suppr_Ville->execute();
	$modif_Musicien->execute();
}

function createTown($ville, $conn)
{
	/* Créer une ville, avec son nom, son code postal.
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$crea_Ville= $conn->prepare(<<<SQL
		INSERT INTO `Ville` (`id`, `nom_ville`, `code_postal`)
			VALUES (NULL, "{$ville['nom']}", {$ville['code_postal']})
SQL
	);

	$crea_Ville->execute();

	$id  = intval( $conn->lastInsertId() );

	$ville['id'] = $id;

	$reponse = array('ville' => $ville
	);
	return($reponse);

}

function selectTown($id, $conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT v.id AS 'id_ville', v.nom_ville AS 'nom_ville', v.code_postal AS 'code_postal'
		FROM Ville AS v
		WHERE v.id = {$id}
SQL
		);


	$req->execute();


	$resultat['ville'] = array();
	$resultat['ville']['id'] = $id;

	$trouve= false;
	while( ($ville = $req->fetch()) !== false )
	{
		$trouve = true;

		$resultat['ville']['nom'] = $ville['nom_ville'];
		$resultat['ville']['code_postal'] = intval($ville['code_postal']);
	}
	if( $trouve )
		return $resultat;
	else
		return false;
}

function updateTown($ancien, $ville, $conn)
{
	/* LA VARIABLE $ancien contient les information de la ville
	* avant la mise-à-jour, pour comparer avec les nouvelles valeurs.*/
	$id = $ancien['ville']['id'];

	/** Salle **/
	if( isset($ville['nom']) || isset($ville['code_postal']) )
	{
		$texte_nom = isset($ville['nom']) ? "`nom_ville` = '{$ville['nom']}'" : "`nom_ville` = '{$ancien['ville']['nom']}'";
		$texte_code = isset($ville['code_postal']) ? "`code_postal` = '{$ville['code_postal']}'" : NULL;

		$texte_total = $texte_nom;
		if( isset($ville['code_postal']) )
			$texte_total = $texte_total.', '.$texte_code;

		$req_total = <<<SQL
			UPDATE `Ville`
				SET {$texte_total}
				WHERE`id` = {$id}
SQL
;
		$modif_Ville = $conn->prepare($req_total);
		$modif_Ville->execute();
	}
	/** **/

	$resultat = array('ville' => $ville
				);
	return $resultat;

}





?>