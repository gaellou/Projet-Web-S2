<?php

function checkPost($ville)
{
	$valide = true;
	$valide = $valide & checkTown($ville);
	return $valide;
}

function checkPut($ville, $conn)
{
	$valide = true;
	$valide = $valide & checkTown_UPDATE($ville, $conn);
	return $valide;
}


function checkTown($ville)
{
	require_once '../data/commun.php';
	$valide = true;
	$valide = $valide & is_string($ville['nom']);
	$valide = $valide & is_int($ville['code_postal']);
	if( isset($ville['id']))
		$valide = $valide & is_int($ville['id']) && checkID($ville['id'], 'id', 'Ville', $conn);
	return $valide;
}

function checkTown_UPDATE($ville, $conn)
{
	$valide = true;
	if( isset($ville['nom']) )
		$valide = $valide & is_string($ville['nom']);
	if( isset($ville['code_postal']) )
		$valide = $valide & is_int($ville['code_postal']);
	return $valide;
}

?>