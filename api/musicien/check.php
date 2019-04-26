<?php


function checkPost($musicien, $ville, $genres, $instruments, $conn)
{
	$valide = checkMusician_CREATE($musicien, $conn);
	$valide = $valide & checkTown($ville, $conn);
	if( isset($genres) )
		$valide = $valide & checkGenres($genres, $conn);
	if( isset($instruments) )
		$valide = $valide & checkInstruments($instruments, $conn);
	return $valide;
}

function checkPut($musicien, $ville, $genres, $instruments, $conn)
{
	$valide = checkMusician_UPDATE($musicien, $conn);
	var_dump($valide);
	if( isset($ville) )
		$valide = $valide & checkTown($ville, $conn);
	var_dump($valide);
	if( isset($genres) )
		$valide = $valide & checkGenres_UPDATE($genres, $conn);
	var_dump($valide);
	if( isset($instruments) )
		$valide = $valide & checkInstruments_UPDATE($instruments, $conn);
	return $valide;
}

function checkSearch($musicien, $dates, $ville, $genres, $instruments, $conn)
{
	$valide = checkMusician_UPDATE($musicien, $conn);
	if( isset($ville) )
		$valide = $valide & checkTown($ville, $conn);
	if( isset($genres) )
		$valide = $valide & checkGenres_UPDATE($genres, $conn);
	if( isset($instruments) )
		$valide = $valide & checkInstruments_SEARCH($instruments, $conn);
	if( isset($dates['apres']) )
		$valide = $valide & ( strtotime($dates['apres']) !== false );
	if( isset($dates['avant']) )
		$valide = $valide & ( strtotime($dates['avant']) !== false );
	return $valide;
}

function checkMusician_CREATE($musicien, $conn)
{
	$valide = is_string($musicien['nom']);
	$valide = $valide & is_string($musicien['prenom']);
	$valide = $valide & (strtotime($musicien['date_naissance']) !== false );
	return $valide;
}

function checkMusician_UPDATE($musicien, $conn)
{
	$valide = true;
	if( isset($musicien['nom']) )
		$valide = $valide & is_string($musicien['nom']);
	if( isset($musicien['prenom']) )
		$valide = $valide & is_string($musicien['prenom']);
	if( isset($musicien['date_naissance']) )
		$valide = $valide & (strtotime($musicien['date_naissance']) !== false );
	return $valide;
}

function checkMusician($musicien, $conn)
{
	require_once '../data/commun.php';
	$valide = checkID($musicien['id'], 'id', 'Musicien', $conn);
	$valide = $valide & is_string($musicien['nom']);
	$valide = $valide & is_string($musicien['prenom']);
	$valide = $valide & strtotime($musicien['date_naissance']);
	return $valide;
}



function checkGenres($genres, $conn)
{
	require_once '../data/commun.php';
	foreach( $genres as $genre )
	{
		if( !is_int($genre['id']) || !checkID($genre['id'],'id','Genre', $conn) )
		{
			return false;
		}
	}
	return true;
}

function checkInstruments($instruments, $conn)
{
	require_once '../data/commun.php';
	foreach( $instruments as $instrument )
	{
		if( !is_int($instrument['id']) ||
			!strtotime($instrument['annee_debut']) ||
			!checkID($instrument['id'],'id','Instrument', $conn) )
			return false;
	}
	return true;
}

function checkGenres_UPDATE($genres, $conn)
{
	require_once '../data/commun.php';
	foreach( $genres as $genre )
	{
		if( $genre['id'] === -1)
			return true;
		else if( !is_int($genre['id']) || !checkID($genre['id'], 'id', 'Genre', $conn) )
			return false;
	}
	return true;
}


function checkInstruments_UPDATE($instruments, $conn)
{
	require_once '../data/commun.php';
	foreach( $instruments as $instrument )
	{
		if( $instrument['id'] === -1)
			return true;
		if( !is_int($instrument['id']) ||
			!strtotime($instrument['annee_debut']) ||
			!checkID($instrument['id'],'id','Instrument', $conn) )
			return false;
	}
	return true;
}

function checkInstruments_SEARCH($instruments, $conn)
{
	require_once '../data/commun.php';
	foreach( $instruments as $instrument )
	{
		if( $instrument['id'] === -1)
			return true;
		if( !is_int($instrument['id']) ||
			!checkID($instrument['id'],'id','Instrument', $conn) )
			return false;
	}
	return true;
}

function checkTown($ville, $conn)
{
	require_once '../data/commun.php';
	$valide = is_int($ville['id']) & checkID($ville['id'],'id','Ville', $conn);
	return $valide;
}

?>