-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 27 avr. 2026 à 12:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `maslaki`
--

-- --------------------------------------------------------

--
-- Structure de la table `ai_recommendations`
--

CREATE TABLE `ai_recommendations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `result` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `deadlines`
--

CREATE TABLE `deadlines` (
  `id` int(11) NOT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `deadline_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `institutions`
--

CREATE TABLE `institutions` (
  `id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `min_average` float DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `requirements` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `city`, `type`, `min_average`, `description`, `created_at`, `requirements`) VALUES
(1, 'ENSA Casablanca', 'Casablanca', 'Engineering', 12, 'école d\'ingénieur publique', '2026-04-24 09:14:50', 'Bac Sciences Math / Physique + concours'),
(2, 'ENCG Casablanca', 'Casablanca', 'Business', 11, 'école de commerce publique', '2026-04-24 09:14:50', 'Bac Eco / Math + sélection dossier'),
(3, 'FST Settat', 'Settat', 'Science', 10, 'faculté des sciences et techniques', '2026-04-24 09:14:50', 'Bac Sciences + moyenne >= 10'),
(4, 'EST Casablanca', 'Casablanca', 'Technical', 10, 'école supérieure de technologie', '2026-04-24 09:14:50', 'Bac Sciences / Tech'),
(5, 'ISCAE Casablanca', 'Casablanca', 'Business', 14, 'institut supérieur de commerce', '2026-04-24 09:14:50', 'Bac mention + concours écrit + oral'),
(6, 'ENSA Marrakech', 'Marrakech', 'Engineering', 12, 'école nationale des sciences appliquées', '2026-04-24 09:14:50', 'Bac Sciences + concours'),
(7, 'ENCG Agadir', 'Agadir', 'Business', 11, 'école nationale de commerce', '2026-04-24 09:14:50', 'Bac Eco / Math + dossier'),
(8, 'FST Tanger', 'Tanger', 'Science', 10, 'faculté scientifique', '2026-04-24 09:14:50', 'Bac Sciences + 10+'),
(9, 'EST Fes', 'Fes', 'Technical', 10, 'école technologie', '2026-04-24 09:14:50', 'Bac Tech / Sciences'),
(10, 'EMI Rabat', 'Rabat', 'Engineering', 15, 'école Mohammadia', '2026-04-24 09:14:50', 'Bac Sciences Math + très haut niveau + concours'),
(11, 'ENSA Fes', 'Fes', 'Engineering', 12, 'école ingénieur', '2026-04-24 09:14:50', 'Bac Sciences + concours'),
(12, 'ENSA Tanger', 'Tanger', 'Engineering', 12, 'école ingénieur', '2026-04-24 09:14:50', 'Bac Sciences + concours'),
(13, 'ENCG Settat', 'Settat', 'Business', 11, 'école commerce', '2026-04-24 09:14:50', 'Bac Eco / Math'),
(14, 'ENCG Marrakech', 'Marrakech', 'Business', 11, 'école gestion', '2026-04-24 09:14:50', 'Bac Eco / Math'),
(15, 'FST Mohammedia', 'Mohammedia', 'Science', 10, 'faculté technique', '2026-04-24 09:14:50', 'Bac Sciences'),
(16, 'FST Beni Mellal', 'Beni Mellal', 'Science', 10, 'faculté régionale', '2026-04-24 09:14:50', 'Bac Sciences'),
(17, 'EST Agadir', 'Agadir', 'Technical', 10, 'école technique', '2026-04-24 09:14:50', 'Bac Tech'),
(18, 'EST Oujda', 'Oujda', 'Technical', 10, 'école technique', '2026-04-24 09:14:50', 'Bac Tech'),
(19, 'FS Casablanca', 'Casablanca', 'Science', 10, 'faculté sciences', '2026-04-24 09:14:50', 'Bac Sciences'),
(20, 'FS Rabat', 'Rabat', 'Science', 10, 'faculté sciences', '2026-04-24 09:14:50', 'Bac Sciences'),
(21, 'CPGE Casablanca', 'Casablanca', 'Preparatory', 14, 'classes préparatoires', '2026-04-24 09:14:50', 'Bac mention Bien ou Très Bien'),
(22, 'CPGE Rabat', 'Rabat', 'Preparatory', 14, 'prépa scientifique', '2026-04-24 09:14:50', 'Bac mention Bien + excellent niveau'),
(23, 'UIR Rabat', 'Rabat', 'Private', 12, 'université privée', '2026-04-24 09:14:50', 'Bac + dossier + entretien'),
(24, 'EMSI Casablanca', 'Casablanca', 'Private', 10, 'école ingénieur privée', '2026-04-24 09:14:50', 'Bac Sciences / Tech'),
(25, 'SUPMTI Rabat', 'Rabat', 'Private', 10, 'école IT', '2026-04-24 09:14:50', 'Bac + dossier'),
(26, 'IGA Casablanca', 'Casablanca', 'Private', 10, 'école privée', '2026-04-24 09:14:50', 'Bac + entretien'),
(27, 'HEM Casablanca', 'Casablanca', 'Private', 12, 'école management', '2026-04-24 09:14:50', 'Bac + sélection'),
(28, 'ISGA Marrakech', 'Marrakech', 'Private', 10, 'école privée', '2026-04-24 09:14:50', 'Bac + dossier'),
(29, 'ENS Rabat', 'Rabat', 'Education', 11, 'école normale', '2026-04-24 09:14:50', 'Bac + concours'),
(30, 'ENSET Mohammedia', 'Mohammedia', 'Education', 11, 'école technique', '2026-04-24 09:14:50', 'Bac + concours'),
(31, 'ENSA Oujda', 'Oujda', 'Engineering', 12, 'école ingénieur', '2026-04-24 09:14:50', 'Bac Sciences + concours'),
(32, 'ENSA Kenitra', 'Kenitra', 'Engineering', 12, 'école ingénieur', '2026-04-24 09:14:50', 'Bac Sciences + concours'),
(33, 'ENCG Oujda', 'Oujda', 'Business', 11, 'école commerce', '2026-04-24 09:14:50', 'Bac Eco / Math'),
(34, 'ENCG Kenitra', 'Kenitra', 'Business', 11, 'école gestion', '2026-04-24 09:14:50', 'Bac Eco / Math'),
(35, 'FST Errachidia', 'Errachidia', 'Science', 10, 'faculté sciences', '2026-04-24 09:14:50', 'Bac Sciences'),
(36, 'FST Al Hoceima', 'Al Hoceima', 'Science', 10, 'faculté sciences', '2026-04-24 09:14:50', 'Bac Sciences'),
(37, 'EST Kenitra', 'Kenitra', 'Technical', 10, 'école technique', '2026-04-24 09:14:50', 'Bac Tech'),
(38, 'EST Laayoune', 'Laayoune', 'Technical', 10, 'école technique', '2026-04-24 09:14:50', 'Bac Tech'),
(39, 'FS Meknes', 'Meknes', 'Science', 10, 'faculté sciences', '2026-04-24 09:14:50', 'Bac Sciences'),
(40, 'FS Oujda', 'Oujda', 'Science', 10, 'faculté sciences', '2026-04-24 09:14:50', 'Bac Sciences'),
(41, 'CPGE Marrakech', 'Marrakech', 'Preparatory', 14, 'prépa', '2026-04-24 09:14:50', 'Bac mention Bien'),
(42, 'CPGE Fes', 'Fes', 'Preparatory', 14, 'prépa', '2026-04-24 09:14:50', 'Bac mention Bien'),
(43, 'Université Cadi Ayyad', 'Marrakech', 'University', 10, 'université publique', '2026-04-24 09:14:50', 'Bac + inscription'),
(44, 'Université Ibn Tofail', 'Kenitra', 'University', 10, 'université publique', '2026-04-24 09:14:50', 'Bac + inscription'),
(45, 'Université Abdelmalek Essaadi', 'Tanger', 'University', 10, 'université publique', '2026-04-24 09:14:50', 'Bac + inscription'),
(46, 'SUPINFO Casablanca', 'Casablanca', 'Private', 10, 'école IT', '2026-04-24 09:14:50', 'Bac + dossier'),
(47, 'EIGSI Casablanca', 'Casablanca', 'Private', 12, 'école ingénieur privée', '2026-04-24 09:14:50', 'Bac Sciences'),
(48, 'HECI Casablanca', 'Casablanca', 'Private', 10, 'école commerce', '2026-04-24 09:14:50', 'Bac'),
(49, 'ESCA Ecole de Management', 'Casablanca', 'Private', 13, 'école management', '2026-04-24 09:14:50', 'Bac + concours');

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `saved_schools`
--

CREATE TABLE `saved_schools` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `saved_schools`
--

INSERT INTO `saved_schools` (`id`, `student_id`, `institution_id`, `created_at`) VALUES
(1, 2, 1, '2026-04-24 09:43:32');

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `bac_branch` varchar(50) DEFAULT NULL,
  `average` float DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `password`, `bac_branch`, `average`, `city`, `created_at`) VALUES
(2, 'Safa Eh', 'ehsafaa7@gmail.com', '$2y$10$4zxaFDvq6GD5jhY1hHazB.3rGadltTVNg70YPO/EvyufsUG8Q9Aii', 'SVT', 15.64, 'Tanger', '2026-04-24 09:29:17');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `deadlines`
--
ALTER TABLE `deadlines`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `saved_schools`
--
ALTER TABLE `saved_schools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `institution_id` (`institution_id`);

--
-- Index pour la table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `deadlines`
--
ALTER TABLE `deadlines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `saved_schools`
--
ALTER TABLE `saved_schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD CONSTRAINT `ai_recommendations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Contraintes pour la table `saved_schools`
--
ALTER TABLE `saved_schools`
  ADD CONSTRAINT `saved_schools_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `saved_schools_ibfk_2` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
