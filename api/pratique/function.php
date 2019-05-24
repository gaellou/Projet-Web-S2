<?php

function deletePlay($idPratique, $conn)
{
	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$suppr_Pratique = $conn->prepare(<<<SQL
		DELETE FROM `Pratique`
			WHERE `id` = {$idPratique}
SQL
	);

	$suppr_Membre = $conn->prepare(<<<SQL
		DELETE FROM `Membre`
			WHERE `id_pratique` = {$idPratique}
SQL
	);


	$suppr_Pratique->execute();
	$suppr_Membre->execute();
}

function deletePlaysFromMusician($idMusicien, $conn)
{
	/** Supprime les pratiques d'un musiciens
	** et supprime les membres correspondants
	**/
	require_once "../data/MyPDO.musiciens-groupes.include.php";

	$suppr_Pratique = $conn->prepare(<<<SQL
		DELETE FROM `Pratique`
			WHERE `id_musicien` = {$idMusicien}
SQL
	);

	/** Attention, pour une raison obscure,
	** on a besoin de nicher deux sous-requêtes
	** pour supprimer les membres
	**/
	$suppr_Membre = $conn->prepare(<<<SQL
		DELETE FROM Membre
		WHERE `id_pratique` IN 
			( SELECT pid FROM 
				( SELECT DISTINCT m2.`id_pratique` AS pid 
					FROM Membre AS m2 
						JOIN Pratique AS p ON p.`id` = m2.`id_pratique` 
							WHERE p.`id_musicien` = {$idMusicien}
				) AS m3
			) 
SQL
);

	$suppr_Membre->execute();

	$suppr_Pratique->execute();
}

function deletePlaysFromInstrument($idInstrument, $conn)
{
	/* On supprime les pratique d'un instrument, et les membres qui en jouent.
	* On aurait écrire une fonction plus générale avec les musiciens
	* Par souci de clarté, je réecris tout.
	*/
	require_once "../data/MyPDO.musiciens-groupes.include.php";



	$suppr_Pratique = $conn->prepare(<<<SQL
		DELETE FROM `Pratique`
			WHERE `id_instrument` = {$idInstrument}
SQL
	);

	$suppr_Instrument = $conn->prepare(<<<SQL
		DELETE FROM Instrument 
		WHERE `id` IN 
			( SELECT iid FROM 
				( SELECT DISTINCT i2.`id` AS iid
					FROM Instrument AS i2 
						JOIN Pratique AS p ON p.`id_instrument` = i2.`id` 
							WHERE p.`id_musicien` = {$idInstrument}
				) AS i3
			) 
SQL
);

	$suppr_Instrument->execute();

	$suppr_Pratique->execute();

	

}



?>