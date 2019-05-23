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
	require_once "../data/MyPDO.musiciens-groupes.include.php";


	$crea_declencheur = $conn->prepare(<<<SQL
		CREATE TRIGGER `decl_Pratique`
			BEFORE DELETE ON `Pratique`	FOR EACH ROW
					DELETE FROM `Membre`
						WHERE `id_pratique` = OLD.`id`
SQL
	);

	$suppr_Pratique = $conn->prepare(<<<SQL
		DELETE FROM `Pratique`
			WHERE `id_musicien` = {$idMusicien}
SQL
	);



	$suppr_Membre = $conn->prepare(<<<SQL
		DELETE FROM `Membre`
			WHERE `id_pratique` = ?
SQL
	);

	$suppr_declencheur= $conn->prepare(<<<SQL
		DROP TRIGGER IF EXISTS `decl_Pratique`;
SQL
);

	$crea_declencheur->execute();

	$suppr_Pratique->execute();

	$suppr_declencheur->execute();
}

function deletePlaysFromInstrument($idInstrument, $conn)
{
	/* On supprime les pratique d'un instrument, et les membres qui en jouent.
	* On aurait écrire un efonction plus générale avec les musiciens
	* Par souci de clarté, je réecris tout.
	*/
	require_once "../data/MyPDO.musiciens-groupes.include.php";


	$crea_declencheur = $conn->prepare(<<<SQL
		CREATE TRIGGER `decl_Pratique`
			BEFORE DELETE ON `Pratique`	FOR EACH ROW
					dELETE FROM `Membre`
						WHERE `id_pratique` = OLD.`id`
SQL
	);

	$suppr_Pratique = $conn->prepare(<<<SQL
		DELETE FROM `Pratique`
			WHERE `id_instrument` = {$idInstrument}
SQL
	);


	$suppr_declencheur = $conn->prepare(<<<SQL
		DROP TRIGGER IF EXISTS `decl_Pratique`;
SQL
);

	$crea_declencheur->execute();

	$suppr_Pratique->execute();

	$suppr_declencheur->execute();


}



?>