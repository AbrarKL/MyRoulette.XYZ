-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 05, 2018 at 12:29 AM
-- Server version: 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myroulette`
--

-- --------------------------------------------------------

--
-- Table structure for table `bets`
--

CREATE TABLE `bets` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `user` text NOT NULL,
  `amount` double NOT NULL,
  `colour` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `market`
--

CREATE TABLE `market` (
  `id` int(11) NOT NULL,
  `itemid` text NOT NULL,
  `name` text NOT NULL,
  `color` text NOT NULL,
  `img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `market`
--

INSERT INTO `market` (`id`, `itemid`, `name`, `color`, `img`) VALUES
(1, '13403364482', 'UMP-45%20%7C%20Primal%20Saber%20%28Minimal%20Wear%29', 'd32ce6', '-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KU0Zwwo4NUX4oFJZEHLbXH5ApeO4YmlhxYQknCRvCo04DEVlxkKgpoo7e1f1Jf0Ob3ZDBSuImJhJKCmvb4ILrTk3lu5Mx2gv2Po9v3jVLt-hJoYG7wINKTdwI7YF6G_FTtxeznjZG9vc_LzHU3uCAm7GGdwUIwVIf-Gg'),
(2, '12968892041', '%E2%98%85%20Butterfly%20Knife%20%7C%20Fade%20%28Factory%20New%29', 'caab05', '-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KU0Zwwo4NUX4oFJZEHLbXH5ApeO4YmlhxYQknCRvCo04DEVlxkKgpovbSsLQJf0ebcZThQ6tCvq4GKqPH1N77ummJW4NE_iLjA99nzigexr0NkYmH2dYSTdAU9ZQrW_lm2kO3pgcTuv8vLy3I1sj5iuyin5z3u1g'),
(3, '12121521451', 'AWP%20%7C%20Dragon%20Lore%20%28Factory%20New%29', 'eb4b4b', '-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KU0Zwwo4NUX4oFJZEHLbXH5ApeO4YmlhxYQknCRvCo04DEVlxkKgpot621FAR17P7NdTRH-t26q4SZlvD7PYTQgXtu5Mx2gv2P9o6migzl_Us5ZmCmLYDDJgU9NA6B81S5yezvg8e-7cycnXJgvHZx5WGdwUJqz1Tl4g'),
(33, '14134538079', 'Sealed Graffiti | Mr. Teeth (Blood Red)\n', '000000', 'IzMF03bi9WpSBq-S-ekoE33L-iLqGFHVaU25ZzQNQcXdB2ozio1RrlIWFK3UfvMYB8UsvjiMXojflsZalyxSh31CIyHz2GZ-KuFpPsrTzBG0pO-CI3z2eCfdYXfYSwlsSbJeMTrbqzOts7mSQznOSOt5RgFSKKYG82BBacuLOhJshtYVu2u_0UdyEhk6f9BKZAarxm1OM-xxzHUW78t9lMs'),
(34, '14121815579', 'UMP-45 | Urban DDPAT (Battle-Scarred)', '000000', 'fWFc82js0fmoRAP-qOIPu5THSWqfSmTELLqcUywGkijVjZYMUrsm1j-9xgEObwgfEh_nvjlWhNzZCveCDfIBj98xqodQ2CZknz5uOfPhZQhvazvGBbBfSMov5grTASIz_t4tUYK3r-9eKAzqs9fPZeUrNI1ITMfQWf6BMlv97xk706lUfJyA8Svr2zOpZDn8kbeMQg'),
(35, '14121802718', 'MP9 | Sand Dashed (Field-Tested)\r\n', '000000', 'fWFc82js0fmoRAP-qOIPu5THSWqfSmTELLqcUywGkijVjZYMUrsm1j-9xgEObwgfEh_nvjlWhNzZCveCDfIBj98xqodQ2CZknz52JLqKIydYZgXSBJ9NVPoo4Df_CCk218pmUN6j-vUEKlq-tIbGYOJ5MopOGMiGC_fTNQ6vu0g_hKRZfJzbpC_tiCvqOzsCRVO1reco0gfx'),
(36, '14121790482', 'Nova | Polar Mesh (Field-Tested)\r\n', '000000', 'fWFc82js0fmoRAP-qOIPu5THSWqfSmTELLqcUywGkijVjZYMUrsm1j-9xgEObwgfEh_nvjlWhNzZCveCDfIBj98xqodQ2CZknz51O_W0DyR3TQnHEqhhXec54AHvNiQ95tNxVcSiyLYDLVWq6ZyVZrMqMYoZHsTWDKWPMF-r6xltgKdYfpWI8irmiXi_OmwKU0G-_m1WhqbZ7RhXqFS2'),
(37, '14121768911', 'P90 | Sand Spray (Field-Tested)', '000000', 'fWFc82js0fmoRAP-qOIPu5THSWqfSmTELLqcUywGkijVjZYMUrsm1j-9xgEObwgfEh_nvjlWhNzZCveCDfIBj98xqodQ2CZknz5rbbOKIydYYRTQALlhT_Q08DfhDCM7_cotVtLnpr4FeAzusovHYrYtMY0dG5KDXf7VYwqruE07h6ZYJ5yApCjqjDOpZDk_t66DLQ'),
(38, '14121746484', 'Gamma 2 Case', '000000', '-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KU0Zwwo4NUX4oFJZEHLbXU5A1PIYQNqhpOSV-fRPasw8rsVFx5KAVo5PSkKV4xhfGfKTgVvIXlxNPSwaOmMLiGwzgJvJMniO-Zoo_z2wXg-EVvfSmtc78HsNoy=');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `steamid` text NOT NULL,
  `tradeofferid` text NOT NULL,
  `item` text NOT NULL,
  `value` double NOT NULL,
  `type` text NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rolls`
--

CREATE TABLE `rolls` (
  `id` int(11) NOT NULL,
  `roll` int(11) NOT NULL,
  `colour` text NOT NULL,
  `time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rolls`
--

INSERT INTO `rolls` (`id`, `roll`, `colour`, `time`) VALUES
(1, 6, 'red', 0),
(2, 5, 'red', 0),
(3, 4, 'red', 0),
(4, 7, 'red', 0),
(5, 8, 'black', 0),
(6, 9, 'black', 0),
(7, 10, 'black', 0),
(8, 11, 'black', 0),
(9, 12, 'black', 0),
(10, 13, 'black', 0);

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE `site` (
  `id` int(11) NOT NULL,
  `totalIn` double NOT NULL,
  `totalOut` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`id`, `totalIn`, `totalOut`) VALUES
(1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `support`
--

CREATE TABLE `support` (
  `id` int(11) NOT NULL,
  `steamid` text NOT NULL,
  `name` text NOT NULL,
  `subject` text NOT NULL,
  `description` text NOT NULL,
  `response` text NOT NULL,
  `resolved` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `identifier` text NOT NULL,
  `steamid` text NOT NULL,
  `nickname` text NOT NULL,
  `rank` text NOT NULL,
  `avatar` text NOT NULL,
  `balance` double NOT NULL,
  `referralCode` text NOT NULL,
  `referredBy` text NOT NULL,
  `lifeTimeEarnings` double NOT NULL,
  `availableEarnings` double NOT NULL,
  `redeemedCode` int(11) NOT NULL,
  `tlink` text NOT NULL,
  `muted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bets`
--
ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `market`
--
ALTER TABLE `market`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rolls`
--
ALTER TABLE `rolls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bets`
--
ALTER TABLE `bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `market`
--
ALTER TABLE `market`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rolls`
--
ALTER TABLE `rolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `site`
--
ALTER TABLE `site`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `support`
--
ALTER TABLE `support`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
