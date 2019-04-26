<?php

function deleteVenue($idSalle, $conn)
{
	/* Supprime une sallee, et
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

	$crea_table = $conn->prepare(<<<SQL
		CREATE TABLE IF NOT EXISTS `Concert_suppr` (`id` INT PRIMARY KEY NOT NULL)
SQL
	);

	$crea_declencheur = $conn->prepare(<<<SQL
		CREATE TRIGGER `decl_Salle`
			BEFORE DELETE ON `Salle`	FOR EACH ROW
					INSERT INTO `Concert_suppr` (`id`)
						VALUES (OLD.`id`)
SQL
	);

	$suppr_Salle = $conn->prepare(<<<SQL
		DELETE FROM `Salle`
			WHERE `id_ville` = {$idVille}
SQL
	);

	

	$selec_Concert = $conn->prepare(<<<SQL
		SELECT `id` FROM `Concert_suppr`
SQL
	);

	$suppr_Concert = $conn->prepare(<<<SQL
		DELETE FROM `Concert`
			WHERE `id` = ?
SQL
	);

	$suppr_declencheur_table = $conn->prepare(<<<SQL
		DROP TRIGGER IF EXISTS `decl_Salle`;
		DROP TABLE IF EXISTS `Concert_suppr`;
SQL
);

	$crea_table->execute();
	$crea_declencheur->execute();

	$suppr_Salle->execute();


	while( ($concert = $selec_Concert->fetch() ) !== false )
	{
		$suppr_Concert->bindValue(1, intval($concert['id']) );
		$suppr_Concert->execute();
	}
	$suppr_declencheur_table->execute();
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