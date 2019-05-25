<?php

function checkPost($ville, $conn)
{
	$valide = checkTown($ville, $conn);
	return $valide;
}

function checkPut($ville)
{
	$valide = checkTown_UPDATE($ville);
	return $valide;
}

function checkSearch($ville)
{
	$valide = checkTown_SEARCH($ville);
	return $valide;
}


function checkTown($ville, $conn)
{
	require_once '../data/commun.php';
	$valide = true;
	$valide &= is_string($ville['nom']);
	$valide &= is_int($ville['code_postal']);
	if( isset($ville['id']))
		$valide &= is_int($ville['id']) && checkID($ville['id'], 'id', 'Ville', $conn);
	return $valide;
}

function checkTown_UPDATE($ville)
{
	$valide = true;
	if( isset($ville['nom']) )
		$valide &= is_string($ville['nom']);
	if( isset($ville['code_postal']) )
		$valide &= is_int($ville['code_postal']);
	return $valide;
}


function checkTown_SEARCH($ville)
{
	$valide = true;
	if( isset($ville['nom']) )
		$valide &= is_string($ville['nom']);
	if( isset($ville['code_min']) )
		$valide &= is_int($ville['code_min']);
	if( isset($ville['code_max']) )
		$valide &= is_int($ville['code_max']);
	return $valide;
}


?>