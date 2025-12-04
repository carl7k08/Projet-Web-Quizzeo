-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 04 déc. 2025 à 13:21
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `quizzeo_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `answers`
--

DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `answer_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `is_correct`) VALUES
(1, 1, 'a', 0),
(2, 1, 'b', 0),
(3, 1, 'c', 1),
(4, 1, 'd', 0),
(5, 2, 'a', 1),
(6, 2, 'b', 0),
(7, 2, 'c', 0),
(8, 2, 'd', 0),
(9, 3, 'a', 1),
(10, 3, 'b', 0),
(11, 3, 'c', 0),
(12, 3, 'd', 0);

-- --------------------------------------------------------

--
-- Structure de la table `attempts`
--

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE IF NOT EXISTS `attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `score` int DEFAULT '0',
  `total_points` int DEFAULT '0',
  `finished_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quiz_id` int NOT NULL,
  `question_text` text NOT NULL,
  `type` enum('qcm','libre') NOT NULL,
  `points` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `type`, `points`, `created_at`) VALUES
(1, 5, 'test', 'qcm', 1, '2025-12-03 14:37:02'),
(2, 6, 'test', 'qcm', 1, '2025-12-03 14:38:44'),
(3, 7, 'el,fbhm', 'qcm', 1, '2025-12-04 12:25:48');

-- --------------------------------------------------------

--
-- Structure de la table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `status` enum('en_cours','lance','termine') DEFAULT 'en_cours',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `quizzes`
--

INSERT INTO `quizzes` (`id`, `user_id`, `titre`, `description`, `status`, `created_at`) VALUES
(1, 4, 'dmoigghea', '', 'en_cours', '2025-12-03 13:34:22'),
(2, 4, 'ed:,:bfqe ', ',elkbfmoeagfep', 'en_cours', '2025-12-03 13:37:03'),
(3, 4, 'sfvsf', ' &quot;', 'en_cours', '2025-12-03 13:40:29'),
(4, 4, 'sfvsf', ' &quot;', 'en_cours', '2025-12-03 13:42:26'),
(5, 4, 'Examen', 'bonjour', 'lance', '2025-12-03 14:36:31'),
(6, 4, 'qsdgfea', ' &quot;', 'en_cours', '2025-12-03 14:38:27'),
(7, 4, 'dqvsd', 'dsvqsc', 'en_cours', '2025-12-04 12:23:03');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','ecole','entreprise','utilisateur') NOT NULL DEFAULT 'utilisateur',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES
(2, 'JACOB', 'jacobcarlos744@gmail.com', '$2y$10$j2MZf5Duta9VzV6/YEAky.r16zHz7KDfBV3iCapody.epNuKO6NKy', 'admin', 1, '2025-12-02 19:29:16'),
(3, 'Usain Bolt', 'c.jacob@ecole-ipssi.net', '$2y$10$3CFxdLpIpuLZU/PrvX.WFuMOAI8G5xsCHBgdSFtOKKIwy/zBY1MBa', 'utilisateur', 1, '2025-12-02 19:31:25'),
(4, 'appel contre appel', 'comptepoubelle640@gmail.com', '$2y$10$/.Siao48u4URwszXMB7UPOAJmXBnL/DozSEYfLrIkht4chGJ4BIMm', 'ecole', 1, '2025-12-02 19:46:39'),
(5, 'Kamui', 'kamuitv@gmail.com', '$2y$10$7u1ffQkwiyJhSg67AWzqFOxK3tSoEOX0oHDE3hgxO.VEssuNpm6Im', 'utilisateur', 1, '2025-12-03 14:13:21'),
(6, 'Administrateur', 'admin@quizzeo.com', '$2y$10$O0GCx00eqQ7syvRRp.77.OAaIN8v4gcvERMPePTMBWfFn2rKP4eyi', 'admin', 1, '2025-12-03 14:25:45');

-- --------------------------------------------------------

--
-- Structure de la table `user_answers`
--

DROP TABLE IF EXISTS `user_answers`;
CREATE TABLE IF NOT EXISTS `user_answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `attempt_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer_text` text,
  PRIMARY KEY (`id`),
  KEY `attempt_id` (`attempt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `attempts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
