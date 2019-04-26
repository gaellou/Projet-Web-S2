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

	$crea_table = $conn->prepare(<<<SQL
		CREATE TABLE IF NOT EXISTS `Pratique_suppr` (`id` INT PRIMARY KEY NOT NULL)
SQL
	);

	$crea_declencheur = $conn->prepare(<<<SQL
		CREATE TRIGGER `decl_Pratique`
			BEFORE DELETE ON `Pratique`	FOR EACH ROW
					INSERT INTO `Pratique_suppr` (`id`)
						VALUES (OLD.`id`)
SQL
	);

	$suppr_Pratique = $conn->prepare(<<<SQL
		DELETE FROM `Pratique`
			WHERE `id_musicien` = {$idMusicien}
SQL
	);

	

	$selec_Pratique = $conn->prepare(<<<SQL
		SELECT `id` FROM `Pratique_suppr`
SQL
	);

	$suppr_Membre = $conn->prepare(<<<SQL
		DELETE FROM `Membre`
			WHERE `id_pratique` = ?
SQL
	);

	$suppr_declencheur_table = $conn->prepare(<<<SQL
		DROP TRIGGER IF EXISTS `decl_Pratique`;
		DROP TABLE IF EXISTS `Pratique_suppr`;
SQL
);

	$crea_table->execute();
	$crea_declencheur->execute();

	$suppr_Pratique->execute();


	while( ($pratique = $selec_Pratique->fetch() ) !== false )
	{
		$suppr_Membre->bindValue(1, intval($pratique['id']) );
		$suppr_Membre->execute();
	}
	$suppr_declencheur_table->execute();


}



?>