<?php


function checkPost($groupe, $genre, $membres, $conn)
{
	$valide = checkBand_CREATE($groupe, $conn);
	$valide &= checkGenre($genre, $conn);
	if( isset($membres) )
		$valide &= checkMembres($membres, $conn);
	return $valide;
}

function checkPut($groupe, $genre, $membres, $conn)
{
	$valide = checkBand_UPDATE($groupe, $conn);
	if( isset($genre) )
		$valide &= checkGenre($genre, $conn);
	if( isset($membres) )
		$valide &= checkMembres_UPDATE($membres, $conn);
	return $valide;
}


function checkSearch($groupe, $genre, $musiciens, $instruments, $conn)
{
	$valide = checkBand_UPDATE($groupe, $conn);
	if( isset($genre) )
		$valide &= checkGenre($genre, $conn);
	if( isset($musiciens) )
		$valide &= checkMusicians($musiciens, $conn);
	if( isset($instruments) )
		$valide &= checkInstruments($instruments, $conn);
	return $valide;
}



function checkBand_CREATE($groupe, $conn)
{
	$valide = is_string($groupe['nom']);
	return $valide;
}

function checkBand_UPDATE($groupe, $conn)
{
	$valide = true;
	if( isset($groupe['nom']) )
		$valide &= is_string($groupe['nom']);
	return $valide;
}



function checkGenre($genre, $conn)
{
	require_once '../data/commun.php';
	return  ( is_int($genre['id']) && checkID($genre['id'],'id','Genre', $conn) ) ;
}

function checkMembres($membres, $conn)
{
	require_once '../data/commun.php';
	foreach( $membres as $membre)
	{
		if( !strtotime($membre['date_entree']) || !checkID($membre['id'], 'id', 'Pratique', $conn) )
			return false;
	}
	return true;
}

function checkMembres_UPDATE($membres, $conn)
{
	require_once '../data/commun.php';
	foreach( $membres as $membre)
	{
		if( $membre['id'] === -1)
			return true;
		if( !strtotime($membre['date_entree']) || !checkID($membre['id'], 'id', 'Pratique', $conn) )
			return false;
	}
	return true;
}


function checkMusicians($musiciens, $conn)
{
	require_once '../data/commun.php';
	foreach( $musiciens as $musicien )
	{
		if( !checkID($musicien['id'],'id','Musicien', $conn) )
			return false;
	}
	return true;
}

function checkInstruments($instruments, $conn)
{
	require_once '../data/commun.php';
	foreach( $instruments as $instrument )
	{
		if( !checkID($instrument['id'],'id','Instrument', $conn) )
			return false;
	}
	return true;
}

?>