<?php

function deleteVenue($idSalle, $conn)
{
	/* Supprime une salle, et
	* - supprime les concerts de la salle
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$suppr_Salle = $conn->prepare(<<<SQL
		DELETE FROM `Salle`
			WHERE `id` = {$idSalle}
SQL
	);

	$suppr_Concert = $conn->prepare(<<<SQL
		DELETE FROM `Concert`
			WHERE `id_salle` = {$idSalle}
SQL
	);

	$suppr_Salle->execute();
	$suppr_Concert->execute();
}

function deleteVenueFromTown($idVille, $conn)
{
	/* Supprime une salle à partir de la ville, et
	* - supprime les concerts de la salle
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";


	$suppr_Salle = $conn->prepare(<<<SQL
		DELETE FROM `Salle`
			WHERE `id_ville` = {$idVille}
SQL
	);

	$suppr_Concert = $conn->prepare(<<<SQL
		DELETE FROM Concert
		WHERE `id` IN 
			( SELECT cid FROM 
				( SELECT DISTINCT c2.`id` AS cid
					FROM Concert AS c2
						JOIN Salle AS s ON c2.`id_salle` = s.`id` 
						JOIN Ville AS v ON s.`id_ville` = v.`id` 
							WHERE v.`id` = {$idVille}
				) AS c3
			) 
SQL
	);

	$suppr_Concert->execute();

	$suppr_Salle->execute();


	
}

function createVenue($salle, $ville, $conn)
{
	/* Crée une salle, avec son nom, sa capacité, et :
	* - id de la ville
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$crea_Salle = $conn->prepare(<<<SQL
		INSERT INTO `Salle` (`id`, `nom_salle`, `capacite`, `id_ville`)
			VALUES (NULL, "{$salle['nom']}", {$salle['capacite']}, {$ville['id']})
SQL
	);

	$crea_Salle->execute();

	$id  = intval( $conn->lastInsertId() );

	$salle['id'] = $id;

	$reponse = array('salle' => $salle,
					'ville' => $ville
	);
	return($reponse);
}

function selectVenue($id, $conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT sa.id AS 'id_salle', sa.nom_salle AS 'nom_salle', sa.capacite AS 'capacite',
			sa.id_ville AS 'id_ville', v.nom_ville AS 'nom_ville', v.code_postal AS 'code_postal'
		FROM Salle AS sa
		INNER JOIN Ville AS v ON v.id = sa.id_ville
		WHERE sa.id = {$id}
SQL
		);


	$req->execute();


	$resultat['salle'] = array();
	$resultat['salle']['id'] = $id;

	$trouve= false;
	while( ($salle = $req->fetch()) !== false )
	{
		$trouve = true;

		$resultat['salle']['nom'] = $salle['nom_salle'];
		$resultat['salle']['capacite'] = intval($salle['capacite']);

		$resultat['ville']['id'] = $salle['id_ville'];
		$resultat['ville']['nom'] = $salle['nom_ville'];
		$resultat['ville']['code_postal'] = intval($salle['code_postal']);
	}
	if( $trouve )
		return $resultat;
	else
		return false;
}

function searchVenue($salle, $ville, $conn)
{
	$req_Salle_JOIN = "";
	$req_Salle_WHERE = "WHERE 1 ";
	$req_Salle_texte = "SELECT DISTINCT sa.id AS 'id' FROM `Salle` AS `sa`";


	//par nom
	if( isset($salle['nom']) )
	{
		$req_Nom_texte = "AND `nom_salle` LIKE '%".$salle['nom']."%'";
		$req_Salle_WHERE = $req_Salle_WHERE.' '.$req_Nom_texte;
	}
	//par capacité
	if( isset($salle['capacite_moins']) )
	{
		$req_Moins_texte = "AND `capacite` < {$salle['capacite_moins']}";
		$req_Salle_WHERE = $req_Salle_WHERE.' '.$req_Moins_texte;
	}
	if( isset($salle['capacite_plus']) )
	{
		$req_Plus_texte = "AND `capacite` > {$salle['capacite_plus']}";
		$req_Salle_WHERE = $req_Salle_WHERE.' '.$req_Plus_texte;
	}
	//par ville
	if( isset($groupe) )
	{
		$req_Ville_texte = " AND `id_ville` = {$ville['id']}";
		$req_Salle_WHERE = $req_Salle_WHERE.$req_Ville_texte;
	}
	//
	$req_Salle_texte = $req_Salle_texte.' '.$req_Salle_WHERE;

	$req_Salle = $conn->prepare($req_Salle_texte);
	$req_Salle->execute();

	$nbSalles = 0;
	$resultat = array();

	while( ($salle = $req_Salle->fetch()) !== false )
	{
		$resultat[$nbSalles] = selectVenue(intval($salle['id']), $conn);
		$nbSalles++;
	}
	if( $nbSalles === 0)
	{
		$resultat = array('message' => 'Pas de résultats !');
	}
	$resultat['nombre'] = $nbSalles;

	return $resultat;
}

function updateVenue($ancien, $salle, $ville, $conn)
{
	/* LA VARIABLE $ancien contient les information de la salle
	* avant la mise-à-jour, pour comparer avec les nouvelles valeurs.*/
	$id = $ancien['salle']['id'];

	/** Salle **/
	if( isset($salle) || isset($ville) )
	{
		$texte_nom = isset($salle['nom']) ? "`nom_salle` = '{$salle['nom']}'" : "`nom_salle` = '{$ancien['salle']['nom']}'";
		$texte_capacite = isset($salle['capacite']) ? "`capacite` = '{$salle['capacite']}'" : NULL;
		$texte_ville = isset($ville['id']) ? "`id_ville` = '{$ville['id']}'" : NULL;

		$texte_total = $texte_nom;
		if( isset($salle['capacite']) )
			$texte_total = $texte_total.', '.$texte_capacite;
		if( isset($ville['id']) )
			$texte_total = $texte_total.', '.$texte_ville;

		$req_total = <<<SQL
			UPDATE `Salle`
				SET {$texte_total}
				WHERE`id` = {$id}
SQL
;
		$modif_Salle= $conn->prepare($req_total);
		$modif_Salle->execute();
	}
	/** **/

	$resultat = array('concert' => $salle,
					'ville' => $ville
				);
	return $resultat;

}



?>