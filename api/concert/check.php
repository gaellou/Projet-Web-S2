<?php


function checkPost($concert, $groupe, $salle, $conn)
{
	$valide = checkGig($concert);
	$valide = $valide & checkBand($groupe, $conn);
	$valide = $valide & checkVenue($salle, $conn);
	return $valide;
}

function checkPut($concert, $groupe, $salle, $conn)
{
	$valide = checkGig_UPDATE($concert);
	if( isset($groupe) )
		$valide = $valide & checkBand($groupe, $conn);
	if( isset($salle) )
		$valide = $valide & checkVenue($salle, $conn);
	return $valide;
}
function checkGig($concert)
{
	$valide = (strtotime($concert['date_concert']) !== false );
	return $valide;
}

function checkGig_UPDATE($concert)
{
	$valide = true;
	if( isset($concert['date_concert']) )
		$valide = $valide & (strtotime($concert['date_concert']) !== false );
	return $valide;
}

function checkBand($groupe, $conn)
{
	require_once '../data/commun.php';
	$valide =  is_int($groupe['id']) && checkID($groupe['id'], 'id', 'Groupe', $conn);
	return $valide;
}

function checkVenue($salle, $conn)
{
	require_once '../data/commun.php';
	$valide = is_int($salle['id']) && checkID($salle['id'], 'id', 'Salle', $conn);
	return $valide;
}

?>