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

function castArrayInt($arr)
{
	foreach($arr as $index => $string)
	{
		$arr[$index] = intval($string);
	}
	return $arr;
}

function checkPost($musicien, $genres, $instruments)
{
	$check = checkMusician($musicien);
	$check = $check & checkInstruments($instruments);
	$check = $check & checkPlay($instruments);
	$check = $check & checkGenres($genres);
	return $check;
}

function checkMusician($musicien)
{
	$check = checkID($musicien['id'], 'id', 'Musicien');
	$check = $check & is_string($musicien['nom']);
	$check = $check & is_string($musicien['prenom']);
	$check = $check & strtotime($musicien['date_naissance']);
	return($check);
}



function checkGenres($genres)
{
	foreach( $genres as $genre )
	{
		if( !is_int($genre['id']) || !checkID($idGenre['id'],'id','Genre') )
		{
			return false;
		}
	}
	return true;
}

function checkPlay($instruments)
{
	foreach( $instruments as $instrument )
	{
		if( !strtotime($instrument['annee_debut']) )
			return false;
	}
	return true;
}


function checkInstruments($instruments)
{
	foreach( $instruments as $instrument )
	{
		if( !is_int($instrument['id']) || !checkID($instrument['id'],'id','Instrument') )
			return false;
	}
	return true;
}

function checkTown($town)
{
	return( is_int($town['id']) && checkID($town['id'],'id','Ville') );
}



function checkID($value, $field, $table)
{
	include_once "../data/MyPDO.musiciens-groupes.include.php";

	if( is_string($value) )
		$value = "'".$value."'"; //pour correspondre à la syntaxe sql

	$req_text = <<<SQL
	SELECT {$field} FROM {$table}
		WHERE {$field} = {$value}
SQL;

	$req_ID = MyPDO::getInstance()->prepare($req_text);
	$req_ID->execute();

	$resp = $req_ID->fetch();

	if( $resp === false )
		return false;
	else
		return true;
}

?>