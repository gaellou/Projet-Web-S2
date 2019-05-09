<?php

function deleteGig($idConcert, $conn)
{
	/* Supprime un concert
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$suppr_Concert = $conn->prepare(<<<SQL
		DELETE FROM `Concert`
			WHERE `id` = {$idConcert}
SQL
	);

	$suppr_Concert->execute();
}

function createGig($concert, $groupe, $salle, $conn)
{
	/* Crée un concert, avec sa date, et :
	* - id du groupe et de la salle
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$crea_Concert = $conn->prepare(<<<SQL
		INSERT INTO `Concert` (`id`, `date_concert`, `id_groupe`, `id_salle`)
			VALUES (NULL, "{$concert['date_concert']}", {$groupe['id']}, {$salle['id']})
SQL
	);

	$crea_Concert->execute();

	$id  = intval( $conn->lastInsertId() );

	$concert['id'] = $id;

	$reponse = array( 'concert' => $concert,
					'groupe' => $groupe,
					'salle' => $salle
	);
	return($reponse);
}


function selectGig($id, $conn)
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
		WHERE co.id = {$id}
SQL
		);


	$req->execute();


	$resultat['concert'] = array();
	$resultat['concert']['id'] = $id;

	$trouve= false;
	while( ($concert = $req->fetch()) !== false )
	{
		$trouve = true;

		$resultat['concert']['date_concert'] = $concert['date_concert'];

		$resultat['groupe']['id'] = intval($concert['id_groupe']);
		$resultat['groupe']['nom'] = $concert['nom_groupe'];

		$resultat['salle']['id'] =  intval($concert['id_salle']);
		$resultat['salle']['nom'] =  $concert['nom_salle'];
	}
	if( $trouve )
		return $resultat;
	else
		return false;
}

function searchGig($concert, $groupe, $salle, $conn)
{
	$req_Concert_JOIN = "";
	$req_Concert_WHERE = "WHERE 1 ";
	$req_Concert_texte = "SELECT DISTINCT co.id AS 'id' FROM `Concert` AS `co`";


	//par date
	/* on cherche entre deux dates (avant une, après l'autre */
	if( isset($concert['date_apres']) )
	{
		$req_Apres_texte = "AND `date_concert` > DATE('{$concert['date_apres']}')";
		$req_Concert_WHERE = $req_Concert_WHERE.' '.$req_Apres_texte;
	}
	if( isset($concert['date_avant']) )
	{
		$req_Avant_texte = "AND `date_concert` < DATE('{$concert['date_avant']}')";
		$req_Concert_WHERE = $req_Concert_WHERE.' '.$req_Avant_texte;
	}
	//par groupe
	if( isset($groupe) )
	{
		$req_Groupe_texte = " AND `id_groupe` = {$groupe['id']}";
		$req_Concert_WHERE = $req_Concert_WHERE.$req_Groupe_texte;
	}
	//par salle
	if( isset($salle) )
	{
		$req_Salle_texte = " AND `id_salle` = {$salle['id']}";
		$req_Concert_WHERE = $req_Concert_WHERE.$req_Salle_texte;
	}

	//
	$req_Concert_texte = $req_Concert_texte.' '.$req_Concert_WHERE;

	$req_Concert = $conn->prepare($req_Concert_texte);
	$req_Concert->execute();

	$nbConcerts = 0;

	while( ($concert = $req_Concert->fetch()) !== false )
	{
		$resultat[$nbConcerts] = selectGig(intval($concert['id']), $conn);
		$nbConcerts++;
	}
	if( $nbConcerts === 0)
	{
		$resultat = array('message' => 'Pas de résultats !');
	}
	$resultat['nombre'] = $nbConcerts;

	return $resultat;
}

function updateGig($ancien, $concert, $groupe, $salle, $conn)
{
	/* LA VARIABLE $ancien contient les information du concert
	* avant la mise-à-jour, pour comparer avec les nouvelles valeurs.*/
	$id = $ancien['concert']['id'];

	/** Concert **/
	if( isset($concert) || isset($groupe) || isset($salle) )
	{
		$texte_date = isset($concert['date_concert']) ? "`date_concert` = '{$concert['date_concert']}'" : "`date_concert` = '{$ancien['concert']['date_concert']}'";
		$texte_groupe = isset($groupe['id']) ? "`id_groupe` = {$groupe['id']}" : NULL;
		$texte_salle = isset($salle['id']) ? "`id_salle` = {$salle['id']}" : NULL;

		$texte_total = $texte_date;
		if( isset($groupe) )
			$texte_total = $texte_total.', '.$texte_groupe;
		if( isset($salle) )
			$texte_total = $texte_total.', '.$texte_salle;

		$req_total = <<<SQL
			UPDATE `Concert`
				SET {$texte_total}
				WHERE`id` = {$id}
SQL
;
		$modif_Concert= $conn->prepare($req_total);
		$modif_Concert->execute();
	}
	/** **/

	$resultat = array('concert' => $concert,
					'groupe' => $groupe,
					'salle' => $salle,
				);
	return $resultat;

}


?>