<?php

function createMusician($musicien, $ville, $instruments, $genres, $conn)
{
	include_once "../data/MyPDO.musiciens-groupes.include.php";


	$req_Musicien_text = <<<SQL
	INSERT INTO `Musicien` (`id`, `nom_musicien`, `prenom_musicien`, `date_naissance`, `id_ville`)
		VALUES ( NULL, "{$musicien['nom']}", "{$musicien['prenom']}", "{$musicien['date_naissance']}", {$ville['id']} )
SQL;

	$req_Musicien = $conn->prepare($req_Musicien_text);

	$req_Musicien->execute();
	//On récupère l'identifiant
	$id  = intval( $conn->lastInsertId() );

	$musicien['id'] = $id;

	//On prépare les autres requêtes
	$req_Aime = $conn->prepare(<<<SQL
		INSERT INTO `Aime` (`id_musicien`, `id_genre`)
			VALUES ({$id}, ?)
SQL
	);

	$req_Pratique = $conn->prepare(<<<SQL
		INSERT INTO `Pratique` (`id_musicien`, `id_instrument`, `annee_debut`)
			VALUES ({$id}, :id, :annee_debut)
SQL
	);

	//On les exécutent, pour tous les éléments des vecteurs
	foreach ($genres as $genre) 
	{
		$req_Aime->bindValue(1, $genre['id']);
		$req_Aime->execute();
	}
	foreach ($instruments as $instrument) 
	{
		$req_Pratique->execute( array( ':id' => $instrument['id'], 
							':annee_debut'=> $instrument['annee_debut']) );
	}

	$reponse = array( 'musicien' => $musicien,
					'ville' => $ville,
					'instruments' => $instruments,
					'genres' => $genres
					);
	return $reponse;
}

function selectMusician($id, $conn)
{
	require_once '../data/MyPDO.musiciens-groupes.include.php';
	require_once '../data/commun.php';


	$conn = MyPDO::getInstance();

	$req = $conn->prepare(<<<SQL
		SELECT mu.id AS 'id_mu', mu.nom_musicien AS 'nom_mu', mu.prenom_musicien AS 'prenom_mu', mu.date_naissance AS 'date_naissance',
			 v.id AS 'id_ville', v.nom_ville AS 'nom_ville', 
			 g.id AS 'id_genre', g.nom_genre AS 'nom_genre', 
			 i.id AS 'id_instrument', i.nom_instrument AS 'nom_instrument',
			 p.id AS 'id_pratique', p.annee_debut AS 'annee_debut'
		FROM Musicien AS mu
		INNER JOIN Ville AS v ON v.id = mu.id_ville
		LEFT JOIN Aime AS a ON a.id_musicien = mu.id
		LEFT JOIN Genre AS g ON g.id = a.id_genre
		LEFT JOIN Pratique AS p ON p.id_musicien = mu.id
		LEFT JOIN Instrument AS i ON i.id = p.id_instrument
		WHERE mu.id = {$id}
SQL
		);


	$req->execute();


	$resultat['genres'] = array();
	$nbGenres = 0;
	$resultat['instruments'] = array();
	$nbInstruments = 0;

	$trouve= false;
	while( ($musicien = $req->fetch()) !== false )
	{
		$trouve = true;

		//musicien en tant que tel
		$resultat['musicien']['id'] = $id;
		$resultat['musicien']['nom'] = $musicien['nom_mu'];
		$resultat['musicien']['prenom'] = $musicien['prenom_mu'];
		$resultat['musicien']['date_naissance'] = $musicien['date_naissance'];

		//ville
		$resultat['ville']['id'] =  intval($musicien['id_ville']);
		$resultat['ville']['nom'] =  $musicien['nom_ville'];


		//genres

		if( isset($musicien['id_genre'])
			&& !isElement( intval($musicien['id_genre']), $resultat['genres'], 'id' ) )
		{
			$resultat['genres'][$nbGenres]['id'] = intval($musicien['id_genre']);
			$resultat['genres'][$nbGenres]['nom'] = $musicien['nom_genre'];
			$nbGenres += 1;

		}

		//instruments
		if( isset($musicien['id_instrument'])
			&& !isElement( intval($musicien['id_instrument']), $resultat['instruments'], 'id' ) )
		{
			$resultat['instruments'][$nbInstruments]['id'] = intval($musicien['id_instrument']);
			$resultat['instruments'][$nbInstruments]['nom'] = $musicien['nom_instrument'];
			$resultat['instruments'][$nbInstruments]['annee_debut'] = $musicien['annee_debut'];
			$resultat['instruments'][$nbInstruments]['id_pratique'] = intval($musicien['id_pratique']);
			$nbInstruments += 1;

		}
	}
	if( $trouve )
		return $resultat;
	else
		return false;

}

function updateMusician($ancien, $musicien, $ville, $instruments, $genres, $conn)
{
	/* LA VARIABLE $ancien contient les information du musicien
	* avant la mise-à-jour, pour comparer avec les nouvelles valeurs.*/
	$id = $ancien['musicien']['id'];

	/** MUSICIEN info de base, et VILLE **/
	if( isset($musicien) || isset($ville) )
	{
		$texte_nom = isset($musicien['nom']) ? "`nom_musicien` = '{$musicien['nom']}'" : "`nom_musicien` = '{$ancien['musicien']['nom']}'";
		$texte_prenom = "`prenom_musicien` = '{$musicien['prenom']}'";
		$texte_naissance =" `date_naissance` = '{$musicien['date_naissance']}'";
		$texte_ville = "`id_ville` = {$ville['id']}";

		$texte_total = $texte_nom;
		if( isset($musicien['prenom']) )
			$texte_total = $texte_total.', '.$texte_prenom;
		if( isset($musicien['date_naissance']) )
			$texte_total = $texte_total.', '.$texte_naissance;
		if( isset($ville['id']) )
			$texte_total = $texte_total.', '.$texte_ville;

		$req_total = <<<SQL
			UPDATE `Musicien`
				SET {$texte_total}
				WHERE`id` = {$id}
SQL
;
		$modif_Musicien = $conn->prepare($req_total);
		$modif_Musicien->execute();
	}
		
	/** **/


	/** GENRES (Aime) **/
	if( isset($genres) )
	{
		$ancien_idGenres = array_column($ancien['genres'], 'id');
		$nouveau_idGenres = array_column($genres, 'id');


		/*id à supprimer, ajouter :*/
		$supprGenres = array_diff( $ancien_idGenres, $nouveau_idGenres );
		$ajoutGenres = array_diff( $nouveau_idGenres, $ancien_idGenres );
		/*Si l'on doit supprimer tous sles genres*/
		if( in_array(-1, $nouveau_idGenres) )
		{
			$suppr_Aime = $conn->prepare(<<<SQL
				DELETE FROM `Aime`
					WHERE `id_musicien` = {$id}
SQL
);
			$suppr_Aime->execute();
		}
		else
		{
			$suppr_Aime = $conn->prepare(<<<SQL
					DELETE FROM `Aime`
						WHERE `id_musicien` = {$id}
						AND `id_genre` = ?
SQL
);
			$crea_Aime = $conn->prepare(<<<SQL
					INSERT INTO `Aime` (`id_musicien`,`id_genre`)
						VALUES ({$id}, ?)
SQL
);
			foreach($supprGenres as $idGenre)
			{
				$suppr_Aime->bindValue(1, $idGenre);
				$suppr_Aime->execute();
			}
			foreach($ajoutGenres as $idGenre)
			{
				$crea_Aime->bindValue(1, $idGenre);
				$crea_Aime->execute();
			}
		}

	}	
	/** **/

	/** INSTRUMENTS et date de début **/
	if( isset($instruments) )
	{
		$ancien_idInstruments = array_column($ancien['instruments'], 'id');
		$ancien_annees = array_combine( $ancien_idInstruments, array_column($ancien['instruments'], 'annee_debut') );
		$nouveau_idInstruments = array_column($instruments, 'id');
		$nouveau_annees = array_combine( $nouveau_idInstruments, array_column($instruments, 'annee_debut') );

		/*id à supprimer, ajouter :*/
		$supprInstruments = array_diff( $ancien_idInstruments, $nouveau_idInstruments );
		$ajoutInstruments = array_diff( $nouveau_idInstruments, $ancien_idInstruments );
		$modifDates = array_diff_assoc( $nouveau_annees , $ancien_annees );

		if( in_array(-1, $nouveau_idInstruments) )
		{
			$suppr_Pratique = $conn->prepare(<<<SQL
				DELETE FROM `Pratique`
					WHERE `id_musicien` = {$id}
SQL
);
			$suppr_Pratique->execute();
		}
		else
		{
			$suppr_Pratique = $conn->prepare(<<<SQL
				DELETE FROM `Pratique`
					WHERE `id_musicien` = {$id}
					AND `id_instrument` = ?
SQL
);
			$crea_Pratique = $conn->prepare(<<<SQL
				INSERT INTO `Pratique` (`id_musicien`,`id_instrument`, `annee_debut`)
					VALUES ({$id}, :id_instrument, :annee_debut)
SQL
);
			$modif_Pratique = $conn->prepare(<<<SQL
				UPDATE `Pratique`
					SET `annee_debut` = :annee_debut
					WHERE `id_musicien` = {$id}
					AND `id_instrument` = :id_instrument
SQL
);
			foreach($supprInstruments as $idInstrument)
			{
				$suppr_Pratique->bindValue(1, $idInstrument);
				$suppr_Pratique->execute();
			}
			foreach($ajoutInstruments as $idInstrument)
			{
				$crea_Pratique->execute( array(
					':id_instrument' => $idInstrument,
					':annee_debut' => $nouveau_annees[$idInstrument]
				) );
			}
			foreach ($modifDates as $indice => $annee_debut) 
			{
				$modif_Pratique->execute( array(
					':annee_debut' => $annee_debut,
					':id_instrument' => $indice
				) );
			}
		}
		
	}
	/** **/

	$resultat = array('musicien' => $musicien,
					'ville' => $ville,
					'genres' => $genres,
					'instruments' => $instruments
				);
	return $resultat;
}

function deleteMusician($id, $conn)
{
	/** NÉCESSITE MySQL ( triggers ) **/
	include_once '../data/MyPDO.musiciens-groupes.include.php';
	require_once '../pratique/function.php';
	
	$suppr_Musicien = $conn->prepare(<<<SQL
			DELETE FROM `Musicien`
				WHERE `id` = {$id};
SQL
);
	$suppr_Aime = $conn->prepare(<<<SQL
			DELETE FROM `Aime`
				WHERE `id_musicien` = {$id};
SQL
);


	deletePlaysFromMusician($id, $conn);

	$suppr_Aime->execute();
	$suppr_Musicien->execute();
}

function searchMusician($musicien, $dates, $ville, $instruments, $genres, $conn)
{
	//On récupère les identifiants, éventuellement les afficher ensuite.
	$req_Musicien_JOIN = "";
	$req_Musicien_WHERE = "WHERE 1";
	$req_Musicien_texte = "SELECT DISTINCT mu.id AS 'id' FROM `Musicien` AS `mu`";




	//par nom
	if( isset($musicien['nom']) )
	{
		$req_Nom_texte = "AND `nom_musicien` LIKE '%".$musicien['nom']."%'";
		$req_Musicien_WHERE = $req_Musicien_WHERE.' '.$req_Nom_texte;
	}
	if( isset($musicien['prenom']) )
	{
		$req_Prenom_texte = "AND `prenom_musicien` LIKE '%".$musicien['prenom']."%'";
		$req_Musicien_WHERE = $req_Musicien_WHERE.' '.$req_Prenom_texte;
	}

	//par dates
	if( isset($dates['apres']) )
	{
		$req_Apres_texte = "AND `date_naissance` > DATE('{$dates['apres']}')";
		$req_Musicien_WHERE = $req_Musicien_WHERE.' '.$req_Apres_texte;
	}
	if( isset($dates['avant']) )
	{

		$req_Avant_texte = "AND `date_naissance` < DATE('{$dates['avant']}')";
		$req_Musicien_WHERE = $req_Musicien_WHERE.' '.$req_Avant_texte;
	}


	//par ville
	if( isset($ville) )
	{
		$req_Ville_JOIN = "INNER JOIN `Ville` AS v ON mu.id_ville = v.id";
		$req_Ville_texte = "AND `id_ville` = {$ville['id']}";
		$req_Musicien_JOIN = $req_Musicien_JOIN.' '.$req_Ville_JOIN;
		$req_Musicien_WHERE = $req_Musicien_WHERE.' '.$req_Ville_texte;
	}

	//par instruments
	if( isset($instruments) )
	{
		$req_Instrument_JOIN = array();
		$req_Instrument_WHERE = array();
		foreach($instruments as $indice => $instrument)
		{
			$req_Instrument_JOIN[$indice] = "INNER JOIN `Pratique` AS pr{$indice} ON pr{$indice}.id_musicien = .mu.id";

			$req_Instrument_WHERE[$indice] = "AND pr{$indice}.id_musicien = mu.id AND pr{$indice}.id_instrument = {$instrument['id']}";
		}
		
		$req_Musicien_JOIN = $req_Musicien_JOIN.' '.implode(' ',$req_Instrument_JOIN);
		$req_Musicien_WHERE = $req_Musicien_WHERE.' '.implode(' ',$req_Instrument_WHERE);
	}
	//par genres
	if( isset($genres) )
	{
		$req_Aime_JOIN = array();
		$req_Aime_WHERE = array();
		foreach ($genres as $indice => $genre) 
		{
			$req_Aime_JOIN[$indice] = "INNER JOIN `Aime` AS am{$indice} ON am{$indice}.id_musicien = mu.id";

			$req_Aime_WHERE[$indice] = "AND am{$indice}.id_musicien = mu.id AND am{$indice}.id_genre = {$genre['id']} ";
		}
		$req_Musicien_JOIN = $req_Musicien_JOIN.' '.implode(' ',$req_Aime_JOIN);
		$req_Musicien_WHERE = $req_Musicien_WHERE.' '.implode(' ',$req_Aime_WHERE);
	}

	
	$req_Musicien_texte = $req_Musicien_texte.' '.$req_Musicien_JOIN.' '.$req_Musicien_WHERE;
	
	$req_Musicien = $conn->prepare($req_Musicien_texte);
	$req_Musicien->execute();

	$nbMusiciens = 0;
	$resultat = array();

	while( ($musicien = $req_Musicien->fetch()) !== false )
	{
		/*
		$resultat[$nbMusiciens]['musicien']['id'] = intval($musicien['id']);
		$resultat[$nbMusiciens]['musicien']['nom'] = $musicien['nom'];
		$resultat[$nbMusiciens]['musicien']['prenom'] = $musicien['prenom'];
		$resultat[$nbMusiciens]['musicien']['date_naissance'] = $musicien['date_naissance'];*/
		$resultat[$nbMusiciens] = selectMusician(intval($musicien['id']), $conn);
		$nbMusiciens++;
	}
	if( $nbMusiciens === 0)
	{
		$resultat[$nbMusiciens] = array('message' => 'Pas de résultats !');
	}
	$resultat['nombre'] = $nbMusiciens;

	return $resultat;
}

?>