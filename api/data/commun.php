<?php

function isElement($element, $vecteur, $champ)
{
	foreach( $vecteur as $valeur )
	{
		if( $valeur[$champ] === $element )
			return true;
	}
	return false;
}

function isElement2($element, $vecteur, $champ1, $champ2)
{
	foreach( $vecteur as $valeur )
	{
		if( $valeur[$champ1][$champ2] === $element )
			return true;
	}
	return false;
}

function castArrayInt($vecteur)
{
	foreach($vecteur as $indice => $chaine)
	{
		$vecteur[$indice] = intval($chaine);
	}
	return $vecteur;
}

function mergeArray($vecteur)
{
	$fusion = array();
	$compteur = 0;
	foreach ($vecteur as $indice => $vecteur2) 
	{
		foreach ($vecteur2 as $indice2 => $valeur) 
		{
			$fusion[$indice + $indice2] = $valeur;
		}
		
	}
	return $fusion;
}


function checkID($valeur, $champ, $table, $conn)
{
	require_once "../data/MyPDO.musiciens-groupes.include.php";

	if( is_string($valeur) )
		$valeur = "'".$valeur."'"; //pour correspondre à la syntaxe sql
	$req_texte = <<<SQL
	SELECT {$champ} FROM {$table}
		WHERE {$champ} = {$valeur}
SQL;

	$req_ID = $conn->prepare($req_texte);
	$req_ID->execute();

	$rep = $req_ID->fetch();

	if( $rep === false )
		return false;
	else
		return true;
}

?>