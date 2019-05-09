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

function searchTown($ville, $conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$req_Ville_JOIN = "";
	$req_Ville_WHERE = "WHERE 1 ";
	$req_Ville_texte = "SELECT DISTINCT v.id AS 'id' FROM `Ville` AS `v`";


	//par nom
	if( isset($ville['nom']) )
	{
		$req_Nom_texte = "AND `nom_ville` LIKE '%".$ville['nom']."%'";
		$req_Ville_WHERE = $req_Ville_WHERE.' '.$req_Nom_texte;
	}
	//par code postal, entre deux valeurs
	if( isset($ville['code_moins']) )
	{
		$req_Moins_texte = "AND `code_postal` < {$ville['code_moins']}";
		$req_Ville_WHERE = $req_Ville_WHERE.' '.$req_Moins_texte;
	}
	if( isset($ville['code_plus']) )
	{
		$req_Plus_texte = "AND `code_postal` > {$ville['code_plus']}";
		$req_Ville_WHERE = $req_Ville_WHERE.' '.$req_Plus_texte;
	}

	var_dump($req_Ville_WHERE);

	$req_Ville =$conn->prepare($req_Ville_texte.' '.$req_Ville_WHERE);
	$req_Ville->execute();


	$nbVilles = 0;

	while( ($ville = $req_Ville->fetch()) !== false )
	{
		$resultat[$nbVilles] = selectTown($ville['id'], $conn);
		$nbVilles++;
	}
	if( $nbVilles === 0)
	{
		$resultat = array('message' => 'Pas de résultats !');
	}
	$resultat['nombre'] = $nbVilles;
	return $resultat;
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