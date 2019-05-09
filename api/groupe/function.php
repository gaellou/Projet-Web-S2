<?php

function deleteBand($idGroupe, $conn)
{
	/* Supprime un groupe et :
	* - Supprime les membres (pas les musiciens)
	* - Supprime les concerts
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$suppr_Groupe = $conn->prepare(<<<SQL
		DELETE FROM `Groupe`
			WHERE `id` = {$idGroupe}
SQL
	);

	$suppr_Membre = $conn->prepare(<<<SQL
		DELETE FROM `Membre`
			WHERE `id_groupe` = {$idGroupe}
SQL
	);

	$suppr_Concert = $conn->prepare(<<<SQL
		DELETE FROM `Concert`
			WHERE `id_groupe` = {$idGroupe}
SQL
	);


	$suppr_Groupe->execute();
	$suppr_Membre->execute();
	$suppr_Concert->execute();
}

function createBand($groupe, $genre, $membres, $conn)
{
	/* Crée un groupe avec son nom, l'id de SON SEUL genre et :
	* - l'id des pratiques des membres, avec leur date d'entrée
	*
	*/

	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$crea_Groupe = $conn->prepare(<<<SQL
		INSERT INTO `Groupe` (`id`, `nom_groupe`, `id_genre`)
			VALUES (NULL, "{$groupe['nom']}", {$genre['id']})
SQL
	);

	$crea_Groupe->execute();
	$id  = intval( $conn->lastInsertId() );
	$groupe['id'] = $id;

	$ajout_Membre = $conn->prepare(<<<SQL
		INSERT INTO `Membre` (`id_groupe`, `id_pratique`, `date_entree`)
			VALUES ({$groupe['id']}, :id_pratique, :date_entree)
SQL
	);

	foreach ($membres as $membre) 
	{
		$ajout_Membre->execute( array(
			':id_pratique' => $membre['id'],
			':date_entree' => $membre['date_entree']
		) );
	}
	$reponse = array( 'groupe' => $groupe,
					'genre' => $genre,
					'membres' => $membres
				);
	return $reponse;
}

function selectBand($id, $conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';
	require_once '../data/commun.php';

	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT gr.id AS 'id_gr', gr.nom_groupe AS 'nom_gr', gr.id_genre AS 'id_genre',
			g.nom_genre AS 'nom_genre',
			mb.date_entree AS 'date_entree',
			m.id AS 'id_mu', m.nom_musicien AS 'nom_mu', m.prenom_musicien AS 'prenom_mu',
			i.id AS 'id_instrument', i.nom_instrument AS 'nom_instrument',
			p.id AS 'id_pratique' 
		FROM Groupe AS gr
		INNER JOIN Genre AS g ON g.id = gr.id_genre
		LEFT JOIN Membre AS mb ON mb.id_groupe = gr.id
		LEFT JOIN Pratique AS p ON p.id = mb.id_pratique
		LEFT JOIN Musicien AS m ON m.id = p.id_musicien
		LEFT JOIN Instrument AS i ON i.id = p.id_instrument
		WHERE gr.id = {$id}
SQL
		);


	$req->execute();


	$resultat['membres'] = array();
	$nbMembres = 0;

	$trouve= false;
	while( ($groupe = $req->fetch()) !== false )
	{
		//première itération
		if( !$trouve)
		{
			//groupe en tant que tel
			$resultat['groupe']['id'] = $id;
			$resultat['groupe']['nom'] = $groupe['nom_gr'];

			//genre
			$resultat['genre']['id'] =  intval($groupe['id_genre']);
			$resultat['genre']['nom'] =  $groupe['nom_genre'];
		}
		$trouve = true;
		$augmenterNbMembres = false;


		//membres (musiciens)
		if( isset($groupe['id_mu'])
			&& !isElement2( intval($groupe['id_mu']), $resultat['membres'], 'musicien','id' ) ) 
		{
			$resultat['membres'][$nbMembres]['musicien']['id'] = intval($groupe['id_mu']);
			$resultat['membres'][$nbMembres]['musicien']['nom'] = $groupe['nom_mu'];
			$resultat['membres'][$nbMembres]['musicien']['prenom'] = $groupe['prenom_mu'];
			$augmenterNbMembres = true;
		}

		//instruments
		if( !isset($nbInstruments[$nbMembres]) )
		{
			$resultat['membres'][$nbMembres]['instruments'] = array();
			$nbInstruments[$nbMembres] = 0;
		}
		if( isset($groupe['id_instrument'])
			&& !isElement( intval($groupe['id_instrument']), $resultat['membres'][$nbMembres]['instruments'], 'id' ) )
		{
			$resultat['membres'][$nbMembres]['instruments'][$nbInstruments[$nbMembres]]['id'] = intval($groupe['id_instrument']);
			$resultat['membres'][$nbMembres]['instruments'][$nbInstruments[$nbMembres]]['nom'] = $groupe['nom_instrument'];
			
			//pratique
			$resultat['membres'][$nbMembres]['pratiques'][$nbInstruments[$nbMembres]]['id'] = intval($groupe['id_pratique']);
			$resultat['membres'][$nbMembres]['pratiques'][$nbInstruments[$nbMembres]]['date_entree'] = $groupe['date_entree'];
			$nbInstruments[$nbMembres] += 1;

		}
		if( $augmenterNbMembres )
			$nbMembres++;
	}
	if( $trouve )
		return $resultat;
	else
		return false;
}

function updateBand($ancien, $groupe, $genre, $membres, $conn)
{
	/* LA VARIABLE $ancien contient les information du groupe
	* avant la mise-à-jour, pour comparer avec les nouvelles valeurs.*/
	$id = $ancien['groupe']['id'];

	/** GROUPE info de base, et GENRE **/
	if( isset($groupe) || isset($genre) )
	{
		$texte_nom = isset($groupe['nom']) ? "`nom_groupe` = '{$groupe['nom']}'" : "`nom_groupe` = '{$ancien['groupe']['nom']}'";
		$texte_genre = isset($genre['id']) ? "`id_genre` = {$genre['id']}" : NULL;

		$texte_total = $texte_nom;
		if( isset($genre['id']) )
			$texte_total = $texte_total.', '.$texte_genre;

		$req_total = <<<SQL
			UPDATE `Groupe`
				SET {$texte_total}
				WHERE`id` = {$id}
SQL
;
		$modif_Groupe = $conn->prepare($req_total);
		$modif_Groupe->execute();
	}
		
	/** **/


	/** Membres **/
	if( isset($membres) )
	{
		$temp = mergeArray( array_column($ancien['membres'], 'pratiques') );
		$ancien_idMembres = array_column( $temp, 'id');
		$nouveau_idMembres = array_column($membres, 'id');

		/*dates d'entrées*/
		$ancien_dates = array_combine( $ancien_idMembres, array_column( $temp, 'date_entree') );
		$nouveau_dates = array_combine( $nouveau_idMembres, array_column($membres, 'date_entree') );


		/*id à supprimer, ajouter :*/
		$supprMembres = array_diff( $ancien_idMembres, $nouveau_idMembres );
		$ajoutMembres = array_diff( $nouveau_idMembres, $ancien_idMembres );
		/*dates à modifier*/
		$modifDates = array_diff_assoc( $nouveau_dates , $ancien_dates );

		
		/*Si l'on doit supprimer tous les membres*/
		if( in_array(-1, $nouveau_idMembres) )
		{
			$suppr_Membre = $conn->prepare(<<<SQL
				DELETE FROM `Membre`
					WHERE `id_groupe` = {$id}
SQL
);
			$suppr_Membre->execute();
		}
		else
		{
			$suppr_Membre = $conn->prepare(<<<SQL
					DELETE FROM `Membre`
						WHERE `id_groupe` = {$id}
						AND `id_pratique` = ?
SQL
);
			$crea_Membre = $conn->prepare(<<<SQL
					INSERT INTO `Membre` (`id_groupe`,`id_pratique`,`date_entree`)
						VALUES ({$id}, :id_pratique, :date_entree)
SQL
);
			$modif_Membre = $conn->prepare(<<<SQL
				UPDATE `Membre`
					SET `date_entree` = :date_entree
					WHERE `id_groupe` = {$id}
					AND `id_pratique` = :id_pratique
SQL
);
			foreach($supprMembres as $idMembre)
			{
				$suppr_Membre->bindValue(1, $idMembre);
				$suppr_Membre->execute();
			}
			foreach($ajoutMembres as $idMembre)
			{
				$crea_Membre->execute( array(
					':id_pratique' => $idMembre,
					':date_entree' => $nouveau_dates[$idMembre]
				) );
			}
			foreach ($modifDates as $indice => $date_entree) 
			{
				$modif_Membre->execute( array(
					':id_pratique' => $indice,
					':date_entree' => $date_entree
				) );
			}
		}

	}	
	/** **/

	$resultat = array('groupe' => $groupe,
					'genre' => $genre,
					'membres' => $membres,
				);
	return $resultat;

}

function searchBand($groupe, $genre, $musiciens, $instruments, $conn)
{

	//On récupère les identifiants, éventuellement les afficher ensuite.
	$req_Groupe_JOIN = "";
	$req_Groupe_WHERE = "WHERE 1 ";
	$req_Groupe_texte = "SELECT DISTINCT gr.id AS 'id' FROM `Groupe` AS `gr`";



	//par nom
	if( isset($groupe['nom']) )
	{
		$req_Nom_texte = "AND `nom_groupe` LIKE '%".$groupe['nom']."%' ";
		$req_Groupe_WHERE = $req_Groupe_WHERE.$req_Nom_texte;
	}

	//par genre
	if( isset($genre) )
	{
		$req_Genre_texte = " AND `id_genre` = {$genre['id']}";
		$req_Groupe_WHERE = $req_Groupe_WHERE.$req_Genre_texte;
	}

	//par pratiques
	/*if( isset($membres) )
	{
		$req_Membre_JOIN = array();
		$req_Membre_WHERE = array();
		foreach ($membres as $indice => $idMembre) 
		{
			# code...
			$req_Membre_JOIN[$indice] = "INNER JOIN `Membre` AS mb{$indice} ON mb{$indice}.id_groupe = gr.id ";

			$req_Membre_WHERE[$indice] = "AND mb{$indice}.id_groupe = gr.id AND mb{$indice}.id_membre = {$idMembre} ";
		}
		$req_Groupe_JOIN = $req_Groupe_JOIN.implode(' ',$req_Membre_JOIN);
		$req_Groupe_WHERE = $req_Groupe_WHERE.implode(' ',$req_Membre_WHERE);
	}*/

	//par musiciens
	if( isset($musiciens) )
	{
		$req_Musicien_JOIN = array();
		$req_Musicien_WHERE = array();
		foreach ($musiciens as $indice => $musicien) 
		{
			$req_Musicien_JOIN[$indice] = "INNER JOIN `Membre` AS mbm{$indice} ON mbm{$indice}.id_groupe= gr.id
			INNER JOIN `Pratique` AS prm{$indice} ON prm{$indice}.id = mbm{$indice}.id_pratique";

			$req_Musicien_WHERE[$indice] = "AND mbm{$indice}.id_groupe = gr.id AND prm{$indice}.id_musicien = {$musicien['id']} ";
		}
		$req_Groupe_JOIN = $req_Groupe_JOIN.' '.implode(' ',$req_Musicien_JOIN);
		$req_Groupe_WHERE = $req_Groupe_WHERE.' '.implode(' ',$req_Musicien_WHERE);
	}

	//par instruments
	if( isset($instruments) )
	{
		$req_Instrument_JOIN = array();
		$req_Instrument_WHERE = array();
		foreach($instruments as $indice => $instrument)
		{
			$req_Instrument_JOIN[$indice] = "INNER JOIN `Membre` AS mbi{$indice} ON mbi{$indice}.id_groupe= gr.id INNER JOIN `Pratique` AS pri{$indice} ON pri{$indice}.id = mbi{$indice}.id_pratique";

			$req_Instrument_WHERE[$indice] = "AND mbi{$indice}.id_groupe = gr.id AND pri{$indice}.id_instrument = {$instrument['id']}";
		}
		
		$req_Groupe_JOIN = $req_Groupe_JOIN.' '.implode(' ',$req_Instrument_JOIN);
		$req_Groupe_WHERE = $req_Groupe_WHERE.' '.implode(' ',$req_Instrument_WHERE);
	}
	///
	$req_Groupe_texte = $req_Groupe_texte.' '.$req_Groupe_JOIN.' '.$req_Groupe_WHERE;

	$req_Groupe = $conn->prepare($req_Groupe_texte);
	$req_Groupe->execute();

	$nbGroupes = 0;
	$resultat = array();

	while( ($groupe = $req_Groupe->fetch()) !== false )
	{
		$resultat[$nbGroupes] = selectBand(intval($groupe['id']), $conn);
		$nbGroupes++;
	}
	if( $nbGroupes === 0)
	{
		$resultat[$nbGroupes] = array('message' => 'Pas de résultats !');
	}
	$resultat['nombre'] = $nbGroupes;

	return $resultat;
}



?>