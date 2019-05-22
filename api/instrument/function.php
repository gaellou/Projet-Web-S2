<?php

function deleteInstrument($idInstrument, $conn)
{
	/* Supprime un instrument et :
	* - Supprime les occurrences du instrument dans les favoris des musiciens
	* - fixe à NULL l'instrument des musiciens à l'instrument.
	*/
	require_once "../data/MyPDO.musiciens-groupes.include.php";

	require_once "../pratique/function.php";

	$suppr_Instrument = $conn->prepare(<<<SQL
		DELETE FROM `Instrument`
			WHERE `id` = {$idInstrument}
SQL
	);

	$suppr_Instrument->execute();

	/* on supprime les pratique qui impliquent cet instrument */
	deletePlaysFromInstrument($idInstrument, $conn);
}

function createInstrument($instrument, $conn)
{
	/* Crée un instrument, avec son nom
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$crea_Instrument = $conn->prepare(<<<SQL
		INSERT INTO `Instrument` (`id`, `nom_instrument`)
			VALUES (NULL, "{$instrument['nom']}")
SQL
	);

	$crea_Instrument->execute();

	$id  = intval( $conn->lastInsertId() );

	$instrument['id'] = $id;

	$reponse = array( 'instrument' => $instrument
	);
	return($reponse);
}


function searchInstrument($instrument, $conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$req_Instrument_JOIN = "";
	$req_Instrument_WHERE = "WHERE 1 ";
	$req_Instrument_texte = "SELECT DISTINCT i.id AS 'id' FROM `Instrument` AS `i`";


	//par nom
	if( isset($instrument['nom']) )
	{
		$req_Nom_texte = "AND `nom_instrument` LIKE '%".$instrument['nom']."%'";
		$req_Instrument_WHERE = $req_Instrument_WHERE.' '.$req_Nom_texte;
	}

	$req_Instrument =$conn->prepare($req_Instrument_texte.' '.$req_Instrument_WHERE);
	$req_Instrument->execute();


	$nbInstruments = 0;

	while( ($instrument = $req_Instrument->fetch()) !== false )
	{
		$resultat[$nbInstruments] = selectInstrument($instrument['id'], $conn);
		$nbInstruments++;
	}
	if( $nbInstruments === 0)
	{
		$resultat = array('message' => 'Pas de résultats !');
	}
	$resultat['nombre'] = $nbInstruments;
	return $resultat;
}

function selectInstrument($id, $conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';

	$req = $conn->prepare(<<<SQL
		SELECT i.id AS 'id_instrument', i.nom_instrument AS 'nom_instrument'
		FROM Instrument AS i
		WHERE i.id = {$id}
SQL
		);
	$req->execute();


	$resultat['instrument'] = array();
	$resultat['instrument']['id'] = $id;

	$trouve= false;
	while( ($instrument = $req->fetch()) !== false )
	{
		$trouve = true;

		$resultat['instrument']['id'] = $instrument['id_instrument'];
		$resultat['instrument']['nom'] = $instrument['nom_instrument'];
	}
	if( $trouve )
		return $resultat;
	else
		return false;
}

function updateInstrument($ancien, $instrument, $conn)
{
	/* LA VARIABLE $ancien contient les information de l'instrument
	* avant la mise-à-jour, pour comparer avec les nouvelles valeurs.*/
	$id = $ancien['instrument']['id'];

	/** Instrument **/
	if( isset($instrument['nom']) )
	{
		$texte_nom = isset($instrument['nom']) ? "`nom_instrument` = '{$instrument['nom']}'" : "`nom_instrument` = '{$ancien['instrument']['nom']}'";

		$req_total = <<<SQL
			UPDATE `Instrument`
				SET {$texte_nom}
				WHERE`id` = {$id}
SQL
;
		$modif_Instrument= $conn->prepare($req_total);
		$modif_Instrument->execute();
	}
	/** **/

	$resultat = array('instrument' => $instrument
				);
	return $resultat;

}


?>