<?php


function checkPost($salle, $ville, $conn)
{
	$valide = true;
	$valide = $valide & checkVenue($salle, $conn);
	$valide = $valide & checkTown($ville, $conn);
	return $valide;
}

function checkPut($salle, $ville, $conn)
{
	$valide = checkVenue_UPDATE($salle, $conn);
	if( isset($ville) )
		$valide = $valide & checkTown($ville, $conn);
	return $valide;
}

function checkVenue($salle, $conn)
{
	$valide = true;
	$valide = $valide & is_int($salle['capacite']);
	$valide = $valide & is_string($salle['nom']);
	return $valide;
}

function checkVenue_UPDATE($salle, $conn)
{
	require_once '../data/commun.php';
	$valide = is_int($salle['id']) && checkID($salle['id'], 'id', 'Salle', $conn);
	if( isset($salle['capacite']) )
		$valide = $valide & is_int($salle['capacite']);
	if( isset($salle['nom']) )
		$valide = $valide & is_string($salle['nom']);
	return $valide;
}

function checkTown($ville, $conn)
{
	require_once '../data/commun.php';
	$valide = true;
	if( isset($ville['id']))
		$valide = $valide & is_int($ville['id']) && checkID($ville['id'], 'id', 'Ville', $conn);
	return $valide;
}

?>