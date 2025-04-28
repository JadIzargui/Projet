-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 28 avr. 2025 à 19:21
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ringosso`
--
CREATE DATABASE IF NOT EXISTS `ringosso` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ringosso`;

-- --------------------------------------------------------

-- Ajout des contraintes de clé étrangère et corrections

-- Structure de la table `ads`
DROP TABLE IF EXISTS `ads`;
CREATE TABLE `ads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_category` (`category`),
  CONSTRAINT `fk_ads_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `conversations`
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ad_id` INT(11) NOT NULL,
  `user1_id` INT(11) NOT NULL,
  `user2_id` INT(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user1` (`user1_id`),
  KEY `idx_user2` (`user2_id`),
  KEY `idx_ad_id` (`ad_id`),
  CONSTRAINT `fk_conversations_ad` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conversations_user1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_conversations_user2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `messages`
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` INT(11) NOT NULL,
  `sender_id` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_conversation` (`conversation_id`),
  CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suppression des données existantes avant insertion
TRUNCATE TABLE `ads`;
TRUNCATE TABLE `conversations`;
TRUNCATE TABLE `messages`;

-- Données de la table `ads`
INSERT INTO `ads` (`id`, `user_id`, `title`, `description`, `price`, `category`, `location`, `image_path`, `created_at`) VALUES
(3, 2, 'iPhone 13 Pro', 'État parfait', 350.00, 'Autres', 'Alger', 'uploads/67fd5d29b4a91.png', '2025-04-14 21:08:25'),
(4, 1, 'Peugeot 207 Rouge', '12200 km', 12025.00, 'Véhicules', 'Paris', 'uploads/67fe2bec30cc6.png', '2025-04-15 11:50:36'),
(5, 5, 'MacBook Pro', 'État parfait', 850.00, 'Matériel Professionnel', 'Toulouse', 'uploads/67fe5aa10fa07.png', '2025-04-15 15:09:53'),
(9, 5, 'T-Max', 'Avec un kit Maxtonn', 12500.00, 'Véhicules', 'Alger', 'uploads/67fe5bb274b23.png', '2025-04-15 15:14:26'),
(10, 2, 'Casque Razer', 'Blackshark V2 neuf', 65.00, 'Matériel Professionnel', 'Paris', 'uploads/67fe604f7c277.png', '2025-04-15 15:34:07'),
(11, 1, 'Siège pour bébé', 'Compatible avec tout type de voiture', 35.00, 'Autres', 'Lyon', 'uploads/67fe60ce6d363.png', '2025-04-15 15:36:14');

-- Données de la table `conversations`
INSERT INTO `conversations` (`id`, `ad_id`, `user1_id`, `user2_id`, `created_at`) VALUES
(2, 3, 1, 2, '2025-04-14 21:08:53'),
(3, 3, 5, 2, '2025-04-15 15:07:53'),
(4, 4, 5, 1, '2025-04-15 15:07:56'),
(5, 10, 5, 2, '2025-04-15 15:39:04'),
(6, 11, 5, 1, '2025-04-15 16:48:08');

-- Données de la table `messages`
INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `created_at`) VALUES
(3, 2, 1, 'Salut !', '2025-04-14 21:09:00'),
(4, 2, 1, 'Toujours disponible ?', '2025-04-14 21:09:05'),
(5, 2, 1, 'Dites-moi plus.', '2025-04-14 21:09:12'),
(6, 3, 5, 'Toujours dispo ?', '2025-04-15 15:14:53'),
(7, 3, 2, 'Oui.', '2025-04-15 15:30:45'),
(8, 2, 2, 'Merci de votre réponse.', '2025-04-15 15:30:56'),
(9, 5, 5, 'Toujours dispo ?', '2025-04-15 15:39:12'),
(10, 5, 2, 'Oui.', '2025-04-15 15:40:08'),
(11, 6, 5, 'Toujours dispo ?', '2025-04-15 16:48:13');

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conversations`
--

INSERT INTO `conversations` (`id`, `ad_id`, `user1_id`, `user2_id`, `created_at`) VALUES
(2, 3, 1, 2, '2025-04-14 21:08:53'),
(3, 3, 5, 2, '2025-04-15 15:07:53'),
(4, 4, 5, 1, '2025-04-15 15:07:56'),
(5, 10, 5, 2, '2025-04-15 15:39:04'),
(6, 11, 5, 1, '2025-04-15 16:48:08');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `created_at`) VALUES
(3, 2, 1, 'jjgufffèfèfèèèfèfèfèf', '2025-04-14 21:09:00'),
(4, 2, 1, 'nknhiig', '2025-04-14 21:09:05'),
(5, 2, 1, '65414', '2025-04-14 21:09:12'),
(6, 3, 5, 'toujours dispo', '2025-04-15 15:14:53'),
(7, 3, 2, 'oui', '2025-04-15 15:30:45'),
(8, 2, 2, 'ewwwww parle bien', '2025-04-15 15:30:56'),
(9, 5, 5, 'toujours dispo', '2025-04-15 15:39:12'),
(10, 5, 2, 'oui', '2025-04-15 15:40:08'),
(11, 6, 5, 'toujours dispo', '2025-04-15 16:48:13'),
(12, 5, 5, 'cvvvv', '2025-04-18 14:48:52'),
(13, 4, 5, 'oui', '2025-04-27 20:29:37'),
(14, 5, 5, 'test', '2025-04-28 19:03:22');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `conversation_id`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 2, 'Un acheteur a contacté votre annonce: iphone 13pro', 0, '2025-04-14 21:08:53'),
(2, 2, 2, 'Nouveau message de salim', 0, '2025-04-14 21:09:00'),
(3, 2, 2, 'Nouveau message de salim', 0, '2025-04-14 21:09:05'),
(4, 2, 2, 'Nouveau message de salim', 0, '2025-04-14 21:09:12'),
(5, 2, 3, 'Un acheteur a contacté votre annonce: iphone 13pro', 0, '2025-04-15 15:07:53'),
(6, 1, 4, 'Un acheteur a contacté votre annonce: peugeot 207 rouge', 0, '2025-04-15 15:07:56'),
(7, 2, 3, 'Nouveau message de jad', 0, '2025-04-15 15:14:53'),
(8, 5, 3, 'Nouveau message de pbhd', 0, '2025-04-15 15:30:45'),
(9, 1, 2, 'Nouveau message de pbhd', 0, '2025-04-15 15:30:56'),
(10, 2, 5, 'Un acheteur a contacté votre annonce: casque razer', 0, '2025-04-15 15:39:04'),
(11, 2, 5, 'Nouveau message de jad', 0, '2025-04-15 15:39:12'),
(12, 5, 5, 'Nouveau message de pbhd', 0, '2025-04-15 15:40:08'),
(13, 1, 6, 'Un acheteur a contacté votre annonce: siège pour bebe', 0, '2025-04-15 16:48:08'),
(14, 1, 6, 'Nouveau message de jad', 0, '2025-04-15 16:48:13'),
(15, 2, 5, 'Nouveau message de jad', 0, '2025-04-18 14:48:52'),
(16, 1, 4, 'Nouveau message de jad', 0, '2025-04-27 20:29:37'),
(17, 2, 5, 'Nouveau message de jad', 0, '2025-04-28 19:03:22');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `access` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `phone`, `sexe`, `password`, `access`, `created_at`) VALUES
(1, 'salim', 'salim idiri', 'salimeidiri@gmail.com', '0758194035', 'homme', '$2y$10$uA3BIYo9v3o5517whF95FOUs9k8tgnhH3l2i5ecWR7v8DYbp5QasO', 0, '2025-04-14 20:55:25'),
(2, 'riad', 'mahiout', 'riadmahsqdd@gmail.com', '0605541620', 'homme', '$2y$10$ALPqh0smvY/w0oUXs/yKu.Obt9TQEfDfl/EPRUohNngSRxJj8FEyy', 0, '2025-04-14 20:56:23'),
(4, 'foznfjzf', 'njzrfr', 'riadmahsq6d@gmail.com', '0605541620', 'homme', '$2y$10$01yf4LGEwtwXxy5RRk1x/OZdmrepLjL6c1XFPEmPqPJHqC/W/5wzy', 0, '2025-04-15 08:48:45'),
(5, 'jad', 'jadd', 'jad@gmail.com', '0758194035', 'homme', '$2y$10$RXOQD3cp4.rBb2TdqCfFdORKqg15TPqgI.dWFLIGooplGjr8Fo5pW', 0, '2025-04-15 15:06:33');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_category` (`category`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user1` (`user1_id`),
  ADD KEY `idx_user2` (`user2_id`),
  ADD KEY `idx_ad_id` (`ad_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation` (`conversation_id`),
  ADD KEY `idx_sender` (`sender_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_conversation` (`conversation_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conversations_ad_id` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
