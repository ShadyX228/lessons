-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 23 2019 г., 11:58
-- Версия сервера: 10.4.8-MariaDB
-- Версия PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `project`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cell`
--

CREATE TABLE `cell` (
  `cell_id` int(11) NOT NULL,
  `cell_game_id` int(11) NOT NULL,
  `cell_resource_id` int(11) NOT NULL,
  `cell_x` int(11) NOT NULL,
  `cell_y` int(11) NOT NULL,
  `cell_shield` tinyint(1) NOT NULL,
  `player_id` int(11) NOT NULL,
  `cell_under_siege` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `cell`
--

INSERT INTO `cell` (`cell_id`, `cell_game_id`, `cell_resource_id`, `cell_x`, `cell_y`, `cell_shield`, `player_id`, `cell_under_siege`) VALUES
(558, 50, 5, 3, 0, 0, 46, 0),
(559, 50, 5, 0, 4, 0, 47, 0),
(560, 50, 5, 3, 8, 0, 48, 0),
(561, 50, 5, 8, 6, 0, 49, 0),
(562, 50, 5, 8, 2, 0, 50, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `game`
--

CREATE TABLE `game` (
  `game_id` int(11) NOT NULL,
  `game_status` int(1) NOT NULL,
  `game_time_begin` datetime DEFAULT NULL,
  `game_last_step_time` datetime DEFAULT NULL,
  `game_step` int(11) DEFAULT NULL,
  `game_players` int(11) DEFAULT NULL,
  `game_field_is_painted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `game`
--

INSERT INTO `game` (`game_id`, `game_status`, `game_time_begin`, `game_last_step_time`, `game_step`, `game_players`, `game_field_is_painted`) VALUES
(50, 1, NULL, NULL, 1, 5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `player`
--

CREATE TABLE `player` (
  `player_id` int(11) NOT NULL,
  `player_name` varchar(32) NOT NULL,
  `player_game_id` int(11) DEFAULT NULL,
  `player_color` varchar(7) DEFAULT NULL,
  `player_steps` int(3) DEFAULT NULL,
  `player_pass` varchar(16) NOT NULL,
  `player_ready` int(11) NOT NULL DEFAULT 0,
  `player_wins` int(11) NOT NULL DEFAULT 0,
  `player_falls` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `player`
--

INSERT INTO `player` (`player_id`, `player_name`, `player_game_id`, `player_color`, `player_steps`, `player_pass`, `player_ready`, `player_wins`, `player_falls`) VALUES
(46, 'habib', 50, '#177347', NULL, '148822', 1, 0, 0),
(47, 'tuger', 50, '#52c3f5', NULL, '148822', 1, 0, 0),
(48, 'connor', 50, '#7c01ee', NULL, '148822', 0, 0, 0),
(49, 'mcgregor', 50, '#caae7e', NULL, '148822', 0, 0, 0),
(50, 'epta', 50, '#824516', NULL, '148822', 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `resources`
--

CREATE TABLE `resources` (
  `resource_id` int(11) NOT NULL,
  `resource_name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `resources`
--

INSERT INTO `resources` (`resource_id`, `resource_name`) VALUES
(1, 'Mountain'),
(2, 'Lain'),
(3, 'Forest'),
(4, 'Lake'),
(5, 'NULL');

-- --------------------------------------------------------

--
-- Структура таблицы `unit`
--

CREATE TABLE `unit` (
  `unit_id` int(11) NOT NULL,
  `unit_player_id` int(11) NOT NULL,
  `unit_cell_id` int(11) NOT NULL,
  `unit_steps` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `unit`
--

INSERT INTO `unit` (`unit_id`, `unit_player_id`, `unit_cell_id`, `unit_steps`) VALUES
(274, 47, 559, 5),
(275, 47, 559, 5),
(276, 47, 559, 5),
(277, 46, 558, 5),
(278, 46, 558, 5),
(279, 46, 558, 5);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cell`
--
ALTER TABLE `cell`
  ADD PRIMARY KEY (`cell_id`),
  ADD KEY `cell_game_id` (`cell_game_id`),
  ADD KEY `cell_resource_id` (`cell_resource_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Индексы таблицы `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`game_id`);

--
-- Индексы таблицы `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `player_game_id` (`player_game_id`);

--
-- Индексы таблицы `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`);

--
-- Индексы таблицы `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `unit_player_id` (`unit_player_id`),
  ADD KEY `unit_cell_id` (`unit_cell_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cell`
--
ALTER TABLE `cell`
  MODIFY `cell_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=563;

--
-- AUTO_INCREMENT для таблицы `game`
--
ALTER TABLE `game`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT для таблицы `player`
--
ALTER TABLE `player`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT для таблицы `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `unit`
--
ALTER TABLE `unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cell`
--
ALTER TABLE `cell`
  ADD CONSTRAINT `cell_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`player_id`),
  ADD CONSTRAINT `cell_ibfk_2` FOREIGN KEY (`cell_game_id`) REFERENCES `game` (`game_id`),
  ADD CONSTRAINT `cell_ibfk_3` FOREIGN KEY (`cell_resource_id`) REFERENCES `resources` (`resource_id`);

--
-- Ограничения внешнего ключа таблицы `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `player_ibfk_1` FOREIGN KEY (`player_game_id`) REFERENCES `game` (`game_id`);

--
-- Ограничения внешнего ключа таблицы `unit`
--
ALTER TABLE `unit`
  ADD CONSTRAINT `unit_ibfk_1` FOREIGN KEY (`unit_player_id`) REFERENCES `player` (`player_id`),
  ADD CONSTRAINT `unit_ibfk_2` FOREIGN KEY (`unit_cell_id`) REFERENCES `cell` (`cell_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
