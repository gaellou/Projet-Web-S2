<?php


function checkPost($concert, $groupe, $salle, $conn)
{
	$valide = checkGig($concert);
	$valide &= checkBand($groupe, $conn);
	$valide &= checkVenue($salle, $conn);
	return $valide;
}

function checkPut($concert, $groupe, $salle, $conn)
{
	$valide = checkGig_UPDATE($concert);
	if( isset($groupe) )
		$valide &= checkBand($groupe, $conn);
	if( isset($salle) )
		$valide &= checkVenue($salle, $conn);
	return $valide;
}

function checkSearch($concert, $groupe, $salle, $conn)
{

$valide = checkGig_SEARCH($concert);
	if( isset($groupe) )
		$valide &= checkBand($groupe, $conn);
	if( isset($salle) )
		$valide &= checkVenue($salle, $conn);
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
		$valide &= (strtotime($concert['date_concert']) !== false );
	return $valide;
}

function checkGig_SEARCH($concert)
{
	$valide = true;
	if( isset($concert['date_avant']) )
		$valide &= (strtotime($concert['date_avant']) !== false );
	if( isset($concert['date_apres']) )
		$valide &= (strtotime($concert['date_apres']) !== false );
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