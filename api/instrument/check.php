<?php

function checkPost($instrument)
{
	$valide = checkInstrument($instrument);
	return $valide;
}

function checkPut($instrument)
{
	$valide = checkInstrument_UPDATE($instrument);
	return $valide;
}

function checkSearch($instrument)
{
	return checkPut($instrument);
}

function checkInstrument($instrument)
{
	$valide = is_string($instrument['nom']);
	return $valide;
}

function checkInstrument_UPDATE($instrument)
{
	$valide = true;
	if( isset($instrument['name']) )
		$valide = is_string($instrument['nom']);
	return $valide;
}

?>