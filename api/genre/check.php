<?php

function checkPost($genre)
{
	$valide = checkGenre($genre);
	return $valide;
}

function checkPut($genre)
{
	$valide = checkGenre_UPDATE($genre);
	return $valide;
}

function checkSearch($genre)
{
	return checkPut($genre);
}

function checkGenre($genre)
{
	$valide = is_string($genre['nom']);
	return $valide;
}

function checkGenre_UPDATE($genre)
{
	$valide = true;
	if( isset($genre['name']) )
		$valide = is_string($genre['nom']);
	return $valide;
}

?>