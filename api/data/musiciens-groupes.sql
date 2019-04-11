-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Jeu 11 Avril 2019 à 13:46
-- Version du serveur :  5.7.25-0ubuntu0.18.10.2
-- Version de PHP :  7.2.15-0ubuntu0.18.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `musiciens-groupes`
--

-- --------------------------------------------------------

--
-- Structure de la table `Concert`
--

CREATE TABLE `Concert` (
  `id` int(11) NOT NULL,
  `date_concert` date NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `id_salle` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Concert`
--

INSERT INTO `Concert` (`id`, `date_concert`, `id_groupe`, `id_salle`) VALUES
(1, '2019-05-29', 1, 8),
(2, '2019-03-27', 6, 9),
(3, '2019-03-27', 7, 9),
(4, '2018-06-21', 8, 15);

-- --------------------------------------------------------

--
-- Structure de la table `Genre`
--

CREATE TABLE `Genre` (
  `id` int(11) NOT NULL,
  `nom_genre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Genre`
--

INSERT INTO `Genre` (`id`, `nom_genre`) VALUES
(1, 'Variété'),
(2, 'Jazz'),
(3, 'Blues'),
(4, 'Rock'),
(5, 'Metal'),
(6, 'Électro'),
(7, 'Expériemental'),
(8, 'Humour'),
(9, 'Chorale'),
(10, 'Classique');

-- --------------------------------------------------------

--
-- Structure de la table `Groupe`
--

CREATE TABLE `Groupe` (
  `id` int(11) NOT NULL,
  `nom_groupe` varchar(50) NOT NULL,
  `id_genre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Groupe`
--

INSERT INTO `Groupe` (`id`, `nom_groupe`, `id_genre`) VALUES
(1, 'Das Wunderbar Web Projekt', 7),
(6, 'Le cours de Chevreuil', 7),
(7, 'Le cours de Cherrier', 8),
(8, 'The Sylvain Cherrier Experience', 4);

-- --------------------------------------------------------

--
-- Structure de la table `Instrument`
--

CREATE TABLE `Instrument` (
  `id` int(11) NOT NULL,
  `nom_instrument` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Instrument`
--

INSERT INTO `Instrument` (`id`, `nom_instrument`) VALUES
(1, 'Voix'),
(2, 'Percussions'),
(3, 'Flûte'),
(4, 'Violon'),
(5, 'Guitare'),
(6, 'Piano'),
(7, 'Synthétiseur'),
(8, 'Trompette'),
(9, 'Saxophone'),
(10, 'Clarinette');

-- --------------------------------------------------------

--
-- Structure de la table `Membre`
--

CREATE TABLE `Membre` (
  `id_groupe` int(11) NOT NULL,
  `id_pratique` int(11) NOT NULL,
  `date_entree` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Membre`
--

INSERT INTO `Membre` (`id_groupe`, `id_pratique`, `date_entree`) VALUES
(1, 4, '2019-03-10'),
(1, 5, '2019-03-10'),
(1, 6, '2019-03-10'),
(1, 8, '2019-03-10'),
(6, 2, '2019-01-14'),
(7, 1, '2019-01-14'),
(8, 1, '2019-01-14'),
(8, 9, '2003-05-28');

-- --------------------------------------------------------

--
-- Structure de la table `Musicien`
--

CREATE TABLE `Musicien` (
  `id` int(11) NOT NULL,
  `nom_musicien` varchar(30) NOT NULL,
  `prenom_musicien` varchar(30) NOT NULL,
  `date_naissance` date NOT NULL,
  `id_ville` int(11) NOT NULL,
  `date_inscripton` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Musicien`
--

INSERT INTO `Musicien` (`id`, `nom_musicien`, `prenom_musicien`, `date_naissance`, `id_ville`, `date_inscripton`) VALUES
(1, 'Brami', 'Ruben', '1998-05-04', 1, '2019-03-23 09:48:51'),
(2, 'Labendzki', 'Pierre', '1998-09-13', 1, '2019-03-23 09:48:51'),
(3, 'Vallet', 'Gaëlle', '1998-07-20', 2, '2019-03-23 09:48:51'),
(4, 'Cherrier', 'Sylvain', '1966-06-06', 1, '2019-03-23 09:48:51'),
(5, 'Chevreuil', 'Antoine', '1960-02-02', 1, '2019-03-23 09:48:51'),
(6, 'Thiel', 'Pierre', '1997-04-01', 4, '2019-03-23 09:48:51'),
(7, 'Bouchuncoin', 'Satan', '0666-06-06', 2, '2019-04-10 20:26:37'),
(8, 'Bouchuncoin', 'Satan', '0666-06-06', 2, '2019-04-10 20:29:34'),
(9, 'Bouchuncoin', 'Satan', '0666-06-06', 2, '2019-04-10 20:31:01'),
(10, 'Bouchuncoin', 'Satan', '0666-06-06', 2, '2019-04-10 20:33:47'),
(11, 'Bouchuncoin', 'Satan', '0666-06-06', 2, '2019-04-10 20:36:14'),
(12, 'Bouchuncoin', 'Satan', '0666-06-06', 2, '2019-04-10 20:36:52'),
(13, 'Bouchuncoin', 'Satan', '0666-06-06', 2, '2019-04-10 20:37:18');

-- --------------------------------------------------------

--
-- Structure de la table `Pratique`
--

CREATE TABLE `Pratique` (
  `id` int(11) NOT NULL COMMENT 'Rajouté par rapport au MLD, pour lier à Membre et éviter l''association triple.',
  `id_musicien` int(11) NOT NULL COMMENT 'Unicité du couple (id_musicien, id_instrument) pour interdire les doublons.',
  `id_instrument` int(11) NOT NULL COMMENT 'Unicité du couple (id_musicien, id_instrument) pour interdire les doublons.',
  `annee_debut` year(4) NOT NULL COMMENT 'Année seulement.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Musicien <-> Instrumeent';

--
-- Contenu de la table `Pratique`
--

INSERT INTO `Pratique` (`id`, `id_musicien`, `id_instrument`, `annee_debut`) VALUES
(1, 4, 5, 1970),
(2, 5, 4, 1966),
(4, 6, 2, 2003),
(5, 2, 5, 2005),
(6, 3, 3, 2003),
(7, 3, 8, 2019),
(8, 1, 7, 2016),
(9, 5, 1, 1990),
(10, 13, 3, 0000),
(11, 13, 6, 1999);

-- --------------------------------------------------------

--
-- Structure de la table `Salle`
--

CREATE TABLE `Salle` (
  `id` int(11) NOT NULL,
  `nom_salle` varchar(30) NOT NULL,
  `capacite` int(11) NOT NULL,
  `id_ville` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Salle`
--

INSERT INTO `Salle` (`id`, `nom_salle`, `capacite`, `id_ville`) VALUES
(1, 'Ferme du Buisson', 300, 2),
(2, 'Maison des Étudiants', 70, 1),
(3, 'La Cigale', 1400, 15),
(4, 'Le Bataclan', 1500, 13),
(5, 'L\'Olympia', 1900, 14),
(6, 'La Philharmonie', 3000, 12),
(7, 'Le Zénith', 5830, 15),
(8, 'Le Mil\'s Pub', 40, 11),
(9, 'La 1B040', 47, 1);

-- --------------------------------------------------------

--
-- Structure de la table `Ville`
--

CREATE TABLE `Ville` (
  `id` int(11) NOT NULL,
  `nom_ville` varchar(30) NOT NULL,
  `code_postal` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Ville`
--

INSERT INTO `Ville` (`id`, `nom_ville`, `code_postal`) VALUES
(1, 'Champs-sur-Marne', 77420),
(2, 'Noisiel', 77186),
(3, 'Torcy', 77200),
(4, 'Noisy-le-Grand', 93160),
(5, 'Laval', 53000),
(11, 'Paris 12', 75012),
(12, 'Paris 19', 75019),
(13, 'Paris 11', 75011),
(14, 'Paris 9', 75009),
(15, 'Paris 18', 77018);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `Concert`
--
ALTER TABLE `Concert`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Genre`
--
ALTER TABLE `Genre`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Groupe`
--
ALTER TABLE `Groupe`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Instrument`
--
ALTER TABLE `Instrument`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Membre`
--
ALTER TABLE `Membre`
  ADD PRIMARY KEY (`id_groupe`,`id_pratique`);

--
-- Index pour la table `Musicien`
--
ALTER TABLE `Musicien`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Pratique`
--
ALTER TABLE `Pratique`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE_PRATIQUE` (`id_musicien`,`id_instrument`);

--
-- Index pour la table `Salle`
--
ALTER TABLE `Salle`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Ville`
--
ALTER TABLE `Ville`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `Concert`
--
ALTER TABLE `Concert`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `Genre`
--
ALTER TABLE `Genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `Groupe`
--
ALTER TABLE `Groupe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `Instrument`
--
ALTER TABLE `Instrument`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `Musicien`
--
ALTER TABLE `Musicien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT pour la table `Pratique`
--
ALTER TABLE `Pratique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Rajouté par rapport au MLD, pour lier à Membre et éviter l''association triple.', AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pour la table `Salle`
--
ALTER TABLE `Salle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `Ville`
--
ALTER TABLE `Ville`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
