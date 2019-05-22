<?php
// TO DO : à modifier si jamais
require_once 'MyPDO.class.php';

// TO DO : à modifier
// host=votre serveur (localhost si travail en local)
// n'oubliez pas d'ajouter le port (sur mac notamment, ex: localhost:8080)
MyPDO::setConfiguration('mysql:host= sqletud.u-pem.fr;dbname=pthiel_db;charset=utf8', 'pthiel', 'root');


/*
EXEMPLE DE CONFIGURATION ET D'UTILISATION
// dans le fichier MyPDO.my_db.include.php : 

MyPDO::setConfiguration('mysql:host=nom_de_l_hote;dbname=nom_de_la_base;charset=utf8', 'login', 'mdp');

// dans un fichier qui utilise la bdd :
$stmt = MyPDO::getInstance()->prepare(<<<SQL
	SELECT *
	FROM Countries
	ORDER BY code
SQL
);

$stmt->execute();

while (($row = $stmt->fetch()) !== false) {
	echo "<div>{$row['name']}</div>"
}

*/