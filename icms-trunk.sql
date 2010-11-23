-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 13 2010 г., 20:29
-- Версия сервера: 5.1.33
-- Версия PHP: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `icms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cms_actions`
--

DROP TABLE IF EXISTS `cms_actions`;
CREATE TABLE `cms_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_tracked` tinyint(4) NOT NULL,
  `is_visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`is_visible`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_actions`
--

INSERT INTO `cms_actions` (`id`, `component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
(2, 'comments', 'add_comment', 'Добавление комментария', 'добавляет %s| на странице %s', 1, 1),
(7, 'photos', 'add_photo', 'Добавление фото', 'добавляет фото %s| в альбом %s', 1, 1),
(8, 'content', 'add_article', 'Добавление статьи с сайта', 'добавляет статью %s| в раздел %s', 1, 1),
(9, 'blogs', 'add_blog', 'Создание блога пользователем', 'создает блог %s|', 1, 1),
(10, 'blogs', 'add_post', 'Добавление поста в блог', 'пишет пост %s| в блоге %s', 1, 1),
(11, 'users', 'set_status', 'Изменение статуса пользователя', '', 1, 1),
(12, 'board', 'add_board', 'Добавление объявления', 'добавляет объявление %s| в рубрику %s', 1, 1),
(13, 'catalog', 'add_catalog', 'Добавление записи в каталог', 'добавляет запись %s| в рубрику каталога %s', 1, 1),
(14, 'clubs', 'add_club', 'Создание клуба', 'создает клуб %s|', 1, 1),
(15, 'clubs', 'add_club_user', 'Вступление в клуб', 'вступает в клуб %s|', 1, 1),
(16, 'faq', 'add_quest', 'Вопрос', 'задает %s| в категории %s', 1, 1),
(17, 'forum', 'add_fpost', 'Добавление поста в форуме', 'добавляет %s| в теме %s', 1, 1),
(18, 'forum', 'add_thread', 'Добавление темы на форуме', 'создает тему %s| на форуме %s', 1, 1),
(19, 'users', 'add_avatar', 'Загрузка или смена аватара пользователем', 'изменяет аватар|', 1, 1),
(20, 'users', 'add_friend', 'Добавление друга', 'и %s стали друзьями|', 1, 1),
(21, 'users', 'add_award', 'Получение награды пользователем', 'получает награду %s|', 1, 1),
(22, 'users', 'add_file', 'Загрузка файла пользователем', 'загружает файл %s|', 1, 1),
(23, 'users', 'add_wall', 'Добавление записи на стену', 'добавляет запись на стене пользователя %s|', 1, 1),
(24, 'clubs', 'add_wall_club', 'Добавление записи на стену', 'добавляет запись на стене клуба %s|', 1, 1),
(25, 'clubs', 'add_post_club', 'Добавление поста в блог клуба', 'пишет пост %s| в клубе %s', 1, 1),
(26, 'users', 'add_user_photo', 'Добавление фото в личный альбом', 'добавляет фото %s| в альбом %s', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_actions_log`
--

DROP TABLE IF EXISTS `cms_actions_log`;
CREATE TABLE `cms_actions_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `object` varchar(100) NOT NULL,
  `object_url` varchar(255) NOT NULL,
  `object_id` int(11) NOT NULL,
  `target` varchar(100) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `target_id` int(11) NOT NULL,
  `description` varchar(650) DEFAULT NULL,
  `is_friends_only` tinyint(4) NOT NULL DEFAULT '0',
  `is_users_only` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`,`user_id`),
  KEY `object_id` (`object_id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_actions_log`
--

INSERT INTO `cms_actions_log` (`id`, `action_id`, `pubdate`, `user_id`, `object`, `object_url`, `object_id`, `target`, `target_url`, `target_id`, `description`, `is_friends_only`, `is_users_only`) VALUES
(1, 11, '2010-10-28 15:44:45', 3, '', '', 0, '', '', 0, 'We are all made of stars (c) Moby', 0, 0),
(2, 20, '2010-10-28 15:45:14', 7, 'Федор', 'http://icms/users/fedor', 9, '', '', 0, '', 0, 0),
(3, 7, '2010-10-28 15:49:11', 7, 'Firefox Wallpaper', '/photos/photo20.html', 20, 'Общий альбом', '/photos/1', 0, '<a href="/photos/photo20.html" class="act_photo">\n										<img border="0" src="/images/photos/small/0df82451e5d0deaa72a904b01a2879a1.jpg" />\n									  </a>', 0, 0),
(4, 8, '2010-10-28 15:52:34', 8, 'Линейно зависимый постулат: предпосылки и развитие', '/stati/astronomija/lineino-zavisimyi-postulat-predposylki-i-razvitie-40.html', 40, 'Астрономия', '/stati/astronomija', 11, '', 0, 0),
(7, 12, '2010-10-28 15:57:22', 2, 'Сдам 2-х комнатную квартиру', '/board/read6.html', 6, 'Недвижимость', '/board/8', 8, '', 0, 0),
(8, 23, '2010-10-28 16:12:16', 2, 'Федор', 'http://icms/users/fedor', 7, '', '', 0, 'так рад тебя здесь видеть!', 0, 0),
(9, 19, '2010-10-28 16:13:32', 2, '', '', 2, '', '', 0, '<a href="http://icms/users/vasya" class="act_usr_ava">\r\n                                               <img border="0" src="/images/users/avatars/small/165e5d6b2786dc6d0a538146de38b480.jpg">\r\n                                            </a>', 0, 0),
(10, 24, '2010-11-07 16:43:09', 1, 'Клуб любителей InstantCMS', '/clubs/14', 8, '', '', 0, 'Клуб любителей InstantCMS', 0, 0),
(11, 17, '2010-11-07 16:56:46', 1, 'пост', '/forum/thread13.html#41', 41, 'Тестовая тема форума', '/forum/thread13.html', 13, 'qweqqweqweqwe', 0, 0),
(12, 17, '2010-11-07 16:57:23', 8, 'пост', '/forum/thread13.html#42', 42, 'Тестовая тема форума', '/forum/thread13.html', 13, 'Согласен со всем что ты тут наговорил  :joke:', 0, 0),
(13, 2, '2010-11-07 19:01:32', 2, 'комментарий', '/stati/astronomija/neprelozhnyi-parametr.html#c12', 12, 'Непреложный параметр', '/stati/astronomija/neprelozhnyi-parametr.html', 0, 'Южный Треугольник, тем не менее, уже 4,5 млрд лет расстояние нашей планеты от Солнца практически не ', 0, 0),
(14, 23, '2010-11-09 17:24:05', 1, 'Василий', 'http://icms/users/vasya', 9, '', '', 0, 'Самый длинный статус из всех', 0, 0),
(15, 20, '2010-11-09 23:42:55', 10, 'Администратор', 'http://icms/users/admin', 10, '', '', 0, '', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_banlist`
--

DROP TABLE IF EXISTS `cms_banlist`;
CREATE TABLE `cms_banlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `bandate` datetime NOT NULL,
  `int_num` int(11) NOT NULL,
  `int_period` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `autodelete` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_banlist`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_banners`
--

DROP TABLE IF EXISTS `cms_banners`;
CREATE TABLE `cms_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(100) NOT NULL DEFAULT 'banner_top',
  `typeimg` varchar(10) NOT NULL DEFAULT 'image',
  `fileurl` varchar(250) DEFAULT NULL,
  `hits` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  `maxhits` int(11) NOT NULL,
  `maxuser` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `link` varchar(250) DEFAULT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_banners`
--

INSERT INTO `cms_banners` (`id`, `position`, `typeimg`, `fileurl`, `hits`, `clicks`, `maxhits`, `maxuser`, `user_id`, `pubdate`, `title`, `link`, `published`) VALUES
(4, 'banner1', 'image', 'banner468x60v1.gif', 0, 0, 0, 0, 1, '2009-04-04 19:43:53', 'InstantCMS - Бесплатная система управления сайтом', 'http://www.instantcms.ru/', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_banner_hits`
--

DROP TABLE IF EXISTS `cms_banner_hits`;
CREATE TABLE `cms_banner_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_id` int(11) DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `pubdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_banner_hits`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_blogs`
--

DROP TABLE IF EXISTS `cms_blogs`;
CREATE TABLE `cms_blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `pubdate` datetime NOT NULL,
  `allow_who` varchar(15) NOT NULL,
  `view_type` varchar(15) NOT NULL DEFAULT 'list',
  `showcats` int(11) NOT NULL DEFAULT '1',
  `ownertype` varchar(15) NOT NULL DEFAULT 'single',
  `premod` int(11) NOT NULL,
  `forall` int(11) NOT NULL DEFAULT '1',
  `owner` varchar(10) NOT NULL DEFAULT 'user',
  `seolink` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seolink` (`seolink`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_blogs`
--

INSERT INTO `cms_blogs` (`id`, `user_id`, `title`, `pubdate`, `allow_who`, `view_type`, `showcats`, `ownertype`, `premod`, `forall`, `owner`, `seolink`, `rating`) VALUES
(1, 1, 'Пример блога админа', '2008-06-03 13:26:55', 'all', 'list', 1, 'single', 0, 1, 'user', 'primer-bloga-admina', 2),
(23, 2, 'Васин блог', '2010-10-12 14:58:52', 'all', 'list', 1, 'single', 0, 0, 'user', 'vasin-blog', 0),
(24, 14, 'Блог', '2010-10-20 00:02:41', 'all', 'list', 1, 'multi', 0, 0, 'club', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blog_authors`
--

DROP TABLE IF EXISTS `cms_blog_authors`;
CREATE TABLE `cms_blog_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `startdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_blog_authors`
--

INSERT INTO `cms_blog_authors` (`id`, `user_id`, `blog_id`, `description`, `startdate`) VALUES
(1, 1, 1, '', '2010-08-03 17:39:38'),
(2, 2, 23, '', '2010-10-20 00:00:57');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blog_cats`
--

DROP TABLE IF EXISTS `cms_blog_cats`;
CREATE TABLE `cms_blog_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_blog_cats`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_blog_files`
--

DROP TABLE IF EXISTS `cms_blog_files`;
CREATE TABLE `cms_blog_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `filesize` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_blog_files`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_blog_posts`
--

DROP TABLE IF EXISTS `cms_blog_posts`;
CREATE TABLE `cms_blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `title` varchar(250) NOT NULL,
  `feel` varchar(100) NOT NULL,
  `music` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `content_html` text NOT NULL,
  `allow_who` varchar(20) NOT NULL,
  `edit_times` int(11) NOT NULL,
  `edit_date` datetime NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `seolink` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seolink` (`seolink`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_blog_posts`
--

INSERT INTO `cms_blog_posts` (`id`, `user_id`, `cat_id`, `blog_id`, `pubdate`, `title`, `feel`, `music`, `content`, `content_html`, `allow_who`, `edit_times`, `edit_date`, `published`, `seolink`) VALUES
(5, 1, 5, 1, '2009-09-07 11:50:16', 'Пример записи в блоге', '', '', '[b]Блоги InstantCMS - это мощный и надежный инструмент для ваших публикаций:[/b]\r\n\r\n- Редактор BB-кода (скрытый текст, ссылки, цитаты, картинки);\r\n- Вставка изображений "на лету";\r\n- Неограниченное число рубрик;\r\n- Комментарии к записям;\r\n- Возможность ведения коллективных блогов;\r\n- Смайлы и теги;', '<b>Блоги InstantCMS - это мощный и надежный инструмент для ваших публикаций:</b><br />\r\n<br />\r\n- Редактор BB-кода (скрытый текст, ссылки, цитаты, картинки);<br />\r\n- Вставка изображений "на лету";<br />\r\n- Неограниченное число рубрик;<br />\r\n- Комментарии к записям;<br />\r\n- Возможность ведения коллективных блогов;<br />\r\n- Смайлы и теги;', '0', 3, '2009-09-07 11:50:16', 1, 'primer-zapisi-v-bloge'),
(38, 1, 5, 1, '2009-09-07 11:50:50', 'Меловой триас: методология и особенности', '', '', 'Антеклиза кавернозна. Эвтектика относительно слабо поступает в калиево-натриевый полевой шпат, так как совершенно однозначно указывает на существование и рост в период оформления палеогеновой поверхности выравнивания. Фосфоритообразование ожелезнено. Зандровое поле поступает в сталагмит, где на поверхность выведены кристаллические структуры фундамента. Грязевой вулкан причленяет к себе сель, в тоже время поднимаясь в пределах горстов до абсолютных высот 250 м. Зандровое поле несет в себе гетит, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники.', 'Антеклиза кавернозна. Эвтектика относительно слабо поступает в калиево-натриевый полевой шпат, так как совершенно однозначно указывает на существование и рост в период оформления палеогеновой поверхности выравнивания. Фосфоритообразование ожелезнено. Зандровое поле поступает в сталагмит, где на поверхность выведены кристаллические структуры фундамента. Грязевой вулкан причленяет к себе сель, в тоже время поднимаясь в пределах горстов до абсолютных высот 250 м. Зандровое поле несет в себе гетит, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники.', '0', 1, '2009-09-07 11:50:50', 1, 'melovoi-trias-metodologija-i-osobenosti'),
(39, 1, 5, 1, '2009-09-07 11:50:36', 'Ультраосновной кимберлит: методология и особенности', '', '', 'Тектогенез покрывает отрог, но приводит к загрязнению окружающей среды. Тем не менее, нужно учитывать и то обстоятельство, что присоединение органического вещества опущено. При описанных условиях меандр косвенно подпитывает коллювий, основными элементами которого являются обширные плосковершинные и пологоволнистые возвышенности. Если принять во внимание огромный вес Гималайев, карбонатная формация ослабляет останцовый гранит, где присутствуют моренные суглинки днепровского возраста. Поверхность Мохо, скажем, за 100 тыс. лет, несовершенна. Если принять во внимание огромный вес Гималайев, этажное залегание ортогонально сменяет бентос, где на поверхность выведены кристаллические структуры фундамента.\r\n\r\nОбласть развития мерзлых пород вызывает ультраосновной корунд, что лишь подтверждает то, что породные отвалы располагаются на склонах. Блеск первичен. Диабаз длительно опускает терригенный отрог, что позволяет проследить соответствующий денудационный уровень. При рассмотрении возможности поступления загрязнений в подземные воды эксплуатируемых участков карбонатная формация сдвигает цокольный рисчоррит, так как совершенно однозначно указывает на существование и рост в период оформления палеогеновой поверхности выравнивания. Сейчас хорошо известно, что антеклиза опускает известняк, в соответствии с изменениями в суммарной минерализации.', 'Тектогенез покрывает отрог, но приводит к загрязнению окружающей среды. Тем не менее, нужно учитывать и то обстоятельство, что присоединение органического вещества опущено. При описанных условиях меандр косвенно подпитывает коллювий, основными элементами которого являются обширные плосковершинные и пологоволнистые возвышенности. Если принять во внимание огромный вес Гималайев, карбонатная формация ослабляет останцовый гранит, где присутствуют моренные суглинки днепровского возраста. Поверхность Мохо, скажем, за 100 тыс. лет, несовершенна. Если принять во внимание огромный вес Гималайев, этажное залегание ортогонально сменяет бентос, где на поверхность выведены кристаллические структуры фундамента.<br />\r\n<br />\r\nОбласть развития мерзлых пород вызывает ультраосновной корунд, что лишь подтверждает то, что породные отвалы располагаются на склонах. Блеск первичен. Диабаз длительно опускает терригенный отрог, что позволяет проследить соответствующий денудационный уровень. При рассмотрении возможности поступления загрязнений в подземные воды эксплуатируемых участков карбонатная формация сдвигает цокольный рисчоррит, так как совершенно однозначно указывает на существование и рост в период оформления палеогеновой поверхности выравнивания. Сейчас хорошо известно, что антеклиза опускает известняк, в соответствии с изменениями в суммарной минерализации.', '0', 2, '2009-09-07 11:50:36', 1, 'ultraosnovnoi-kimberlit-metodologija-i-osobenosti'),
(45, 1, 0, 1, '2010-10-31 15:20:47', 'Эмпирический график функции многих переменных', '', '', 'Двойной интеграл, не вдаваясь в подробности, является следствием. Функция выпуклая книзу, исключая очевидный случай, отражает ортогональный определитель, в итоге приходим к логическому противоречию. Интегрирование по частям, не вдаваясь в подробности, не критично. Математическое моделирование однозначно показывает, что ряд Тейлора обуславливает интегр\\''ал по поверхности, откуда следует доказываемое равенство.\\r\\n\\r\\nВ общем, алгебра усиливает интеграл от функции комплексной переменной, в итоге приходим к логическому противоречию. Геодезическая линия, следовательно, очевидна не для всех. Согласно последним исследованиям, эпсилон окрестность вырождена. Интеграл Фурье порождает абстрактный интеграл Фурье, как и предполагалось.[IMG]/upload/blogs/8bacf4079654b05176f76c4dfbd97331.png.jpg[/IMG]\r\n\r\n[IMG]/upload/blogs/d4cbfe83a72e16b3da518714dbaf325f.png.jpg[/IMG]', 'Двойной интеграл, не вдаваясь в подробности, является следствием. Функция выпуклая книзу, исключая очевидный случай, отражает ортогональный определитель, в итоге приходим к логическому противоречию. Интегрирование по частям, не вдаваясь в подробности, не критично. Математическое моделирование однозначно показывает, что ряд Тейлора обуславливает интегр\\''ал по поверхности, откуда следует доказываемое равенство.\\r\\n\\r\\nВ общем, алгебра усиливает интеграл от функции комплексной переменной, в итоге приходим к логическому противоречию. Геодезическая линия, следовательно, очевидна не для всех. Согласно последним исследованиям, эпсилон окрестность вырождена. Интеграл Фурье порождает абстрактный интеграл Фурье, как и предполагалось.<img src="/upload/blogs/8bacf4079654b05176f76c4dfbd97331.png.jpg" alt="" /><br />\r\n<br />\r\n<img src="/upload/blogs/d4cbfe83a72e16b3da518714dbaf325f.png.jpg" alt="" />', 'all', 20, '2010-10-31 15:20:47', 1, 'yempiricheskii-grafik-funkci-mnogih-peremenyh'),
(46, 1, 0, 1, '2010-10-08 17:14:31', 'Почему нетривиально умножение двух векторов (векторное)?', '', '', '[h3]Теорема Гаусса - Остроградского[/h3]\r\n\r\nCледовательно, изменяет убывающий критерий интегрируемости, откуда следует доказываемое равенство. \r\n\r\nМатематическое моделирование однозначно показывает, что метод последовательных приближений ускоряет интеграл по бесконечной области, как и предполагалось. Сходящийся ряд, конечно, охватывает ротор векторного поля, явно демонстрируя всю чушь вышесказанного. Можно предположить, что лист Мёбиуса поддерживает неопровержимый неопределенный интеграл, в итоге приходим к логическому противоречию. Согласно последним исследованиям, матожидание недоказуемо. Скалярное произведение категорически концентрирует интеграл по ориентированной области, что известно даже школьникам.\r\n[cut=Читать далее...]\r\nПредел функции, [b]не вда''ваясь в подробности[/b], специфицирует изоморфный интеграл Пуассона, откуда следует доказываемое равенство. Ротор векторного поля, в первом приближении, обуславливает стремящийся максимум, что неудивительно. Точка перегиба отображает криволинейный интеграл, как и предполагалось. В соответствии с законом больших чисел, функция выпуклая книзу отражает экспериментальный интеграл Фурье, дальнейшие выкладки оставим студентам в качестве несложной домашней работы. Мнимая единица вырождена. Число е определяет двойной интеграл, явно демонстрируя всю чушь выше''сказанного.', '<h3 class="bb_tag_h3">Теорема Гаусса - Остроградского</h3>\r\nCледовательно, изменяет убывающий критерий интегрируемости, откуда следует доказываемое равенство. <br />\r\n<br />\r\nМатематическое моделирование однозначно показывает, что метод последовательных приближений ускоряет интеграл по бесконечной области, как и предполагалось. Сходящийся ряд, конечно, охватывает ротор векторного поля, явно демонстрируя всю чушь вышесказанного. Можно предположить, что лист Мёбиуса поддерживает неопровержимый неопределенный интеграл, в итоге приходим к логическому противоречию. Согласно последним исследованиям, матожидание недоказуемо. Скалярное произведение категорически концентрирует интеграл по ориентированной области, что известно даже школьникам.<br />\r\n[cut=Читать далее......] <br />\r\nПредел функции, <b>не вда''ваясь в подробности</b>, специфицирует изоморфный интеграл Пуассона, откуда следует доказываемое равенство. Ротор векторного поля, в первом приближении, обуславливает стремящийся максимум, что неудивительно. Точка перегиба отображает криволинейный интеграл, как и предполагалось. В соответствии с законом больших чисел, функция выпуклая книзу отражает экспериментальный интеграл Фурье, дальнейшие выкладки оставим студентам в качестве несложной домашней работы. Мнимая единица вырождена. Число е определяет двойной интеграл, явно демонстрируя всю чушь выше''сказанного.', '0', 52, '2010-10-08 17:14:31', 1, 'pochemu-netrivialno-umnozhenie-dvuh-vektorov-vektornoe'),
(43, 1, 0, 1, '2010-10-07 13:06:33', 'Центральный болид : методология и особенности', '', '', 'Южный Треугольник выслеживает эллиптический параллакс, однако большинство спутников движутся вокруг своих планет в ту же сторону, в какую вращаются планеты. Метеорит, как бы это ни казалось парадоксальным, выслеживает центральный афелий , об интересе Галла к астрономии и затмениям Цицерон говорит также в трактате "О старости" (De senectute). Реликтовый ледник слабопроницаем. Широта, на первый взгляд, точно отражает астероидный сарос, об этом в минувшую субботу сообщил заместитель администратора NASA.\r\n\r\nНатуральный логарифм, следуя пионерской работе Эдвина Хаббла, притягивает параметр (расчет Тарутия затмения точен - 23 хояка 1 г. II О. = 24.06.-771). Pадиотелескоп Максвелла на следующий год, когда было лунное затмение и сгорел древний храм Афины в Афинах (при эфоре Питии и афинском архонте Каллии), теоретически возможен. Многие кометы имеют два хвоста, однако гелиоцентрическое расстояние перечеркивает далекий ионный хвост, тем не менее, Дон Еманс включил в список всего 82-е Великие Кометы. Исполинская звездная спираль с поперечником в 50 кпк решает спектральный класс – это скорее индикатор, чем примета. Эпоха пространственно дает случайный метеорит, но кольца видны только при 40–50. Декретное время, по определению, иллюстрирует вращательный возмущающий фактор, хотя галактику в созвездии Дракона можно назвать карликовой.', 'Южный Треугольник выслеживает эллиптический параллакс, однако большинство спутников движутся вокруг своих планет в ту же сторону, в какую вращаются планеты. Метеорит, как бы это ни казалось парадоксальным, выслеживает центральный афелий , об интересе Галла к астрономии и затмениям Цицерон говорит также в трактате "О старости" (De senectute). Реликтовый ледник слабопроницаем. Широта, на первый взгляд, точно отражает астероидный сарос, об этом в минувшую субботу сообщил заместитель администратора NASA.<br />\r\n<br />\r\nНатуральный логарифм, следуя пионерской работе Эдвина Хаббла, притягивает параметр (расчет Тарутия затмения точен - 23 хояка 1 г. II О. = 24.06.-771). Pадиотелескоп Максвелла на следующий год, когда было лунное затмение и сгорел древний храм Афины в Афинах (при эфоре Питии и афинском архонте Каллии), теоретически возможен. Многие кометы имеют два хвоста, однако гелиоцентрическое расстояние перечеркивает далекий ионный хвост, тем не менее, Дон Еманс включил в список всего 82-е Великие Кометы. Исполинская звездная спираль с поперечником в 50 кпк решает спектральный класс – это скорее индикатор, чем примета. Эпоха пространственно дает случайный метеорит, но кольца видны только при 40–50. Декретное время, по определению, иллюстрирует вращательный возмущающий фактор, хотя галактику в созвездии Дракона можно назвать карликовой.', '0', 0, '2010-10-07 13:06:33', 1, 'centralnyi-bolid-metodologija-i-osobenosti-43');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_board_cats`
--

DROP TABLE IF EXISTS `cms_board_cats`;
CREATE TABLE `cms_board_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '1',
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSDiffer` varchar(15) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `published` int(11) NOT NULL,
  `orderform` int(11) DEFAULT '1',
  `showdate` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL,
  `orderby` varchar(20) NOT NULL DEFAULT 'title',
  `orderto` varchar(4) NOT NULL DEFAULT 'asc',
  `public` int(11) NOT NULL,
  `perpage` int(11) NOT NULL DEFAULT '15',
  `maxcols` int(11) NOT NULL DEFAULT '1',
  `thumb1` int(11) NOT NULL DEFAULT '64',
  `thumb2` int(11) NOT NULL DEFAULT '400',
  `thumbsqr` int(11) NOT NULL,
  `uplimit` int(11) NOT NULL DEFAULT '10',
  `is_photos` int(11) NOT NULL DEFAULT '1',
  `icon` varchar(25) DEFAULT 'folder_grey.png',
  `obtypes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_board_cats`
--

INSERT INTO `cms_board_cats` (`id`, `parent_id`, `ordering`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `title`, `description`, `published`, `orderform`, `showdate`, `pubdate`, `orderby`, `orderto`, `public`, `perpage`, `maxcols`, `thumb1`, `thumb2`, `thumbsqr`, `uplimit`, `is_photos`, `icon`, `obtypes`) VALUES
(1, 0, 1, 1, 8, '', 0, 0, '-- Корневая рубрика --', '', 1, 1, 1, '2008-09-22 13:39:32', 'title', 'asc', 0, 15, 1, 64, 400, 0, 10, 1, 'folder_grey.png', ''),
(10, 1, 21, 2, 3, '', 0, 1, 'Услуги', '', 1, 1, 1, '2008-09-22 14:30:29', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', 'Предлагаю\r\nТребуется'),
(9, 1, 23, 6, 7, '', 0, 1, 'Автомобили', '', 1, 1, 1, '2008-09-22 14:30:23', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', 'Куплю\r\nПродам\r\nОбменяю'),
(8, 1, 22, 4, 5, '', 0, 1, 'Недвижимость', '', 1, 1, 1, '2008-09-22 14:30:00', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', 'Продам\r\nКуплю\r\nОбменяю\r\nСдам\r\nСниму');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_board_items`
--

DROP TABLE IF EXISTS `cms_board_items`;
CREATE TABLE `cms_board_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `obtype` varchar(50) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `pubdate` datetime DEFAULT NULL,
  `pubdays` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `file` varchar(250) NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_board_items`
--

INSERT INTO `cms_board_items` (`id`, `category_id`, `user_id`, `obtype`, `title`, `content`, `city`, `pubdate`, `pubdays`, `published`, `file`, `hits`) VALUES
(4, 10, 1, 'Предлагаю', 'Предлагаю свои услуги', 'Могу выносить мусор и мыть пол.', 'Москва', '2009-10-26 14:11:18', 10, 1, '550de8a5de9b5301133a815de31be00d.jpg', 7),
(5, 9, 1, 'Обменяю', 'Обменяю ВАЗ-2107 на Nissan Skyline GTR', 'Желательно новый и без доплаты.', 'Москва', '2009-10-26 14:14:24', 10, 1, '931f90c50adcea1ff18177bc22d4ceac.jpg', 34),
(6, 8, 2, 'Сдам', 'Сдам 2-х комнатную квартиру', 'Семье из 2-3 человек', 'Москва', '2010-10-28 15:57:22', 10, 1, '80204e6bad519060bca9d456949158dc.jpg', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_cache`
--

DROP TABLE IF EXISTS `cms_cache`;
CREATE TABLE `cms_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(10) NOT NULL,
  `target_id` varchar(255) NOT NULL,
  `cachedate` datetime NOT NULL,
  `cachefile` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_cache`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_category`
--

DROP TABLE IF EXISTS `cms_category`;
CREATE TABLE `cms_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL,
  `showdate` int(11) NOT NULL DEFAULT '1',
  `showcomm` int(11) NOT NULL DEFAULT '1',
  `orderby` varchar(30) NOT NULL DEFAULT 'date',
  `orderto` varchar(4) NOT NULL DEFAULT 'asc',
  `modgrp_id` int(11) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `NSDiffer` varchar(11) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `maxcols` int(11) NOT NULL DEFAULT '1',
  `showtags` int(11) NOT NULL DEFAULT '1',
  `showrss` int(11) NOT NULL DEFAULT '1',
  `showdesc` int(11) NOT NULL,
  `is_public` int(11) NOT NULL,
  `photoalbum` text NOT NULL,
  `seolink` varchar(200) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seolink` (`seolink`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_category`
--

INSERT INTO `cms_category` (`id`, `parent_id`, `title`, `description`, `published`, `showdate`, `showcomm`, `orderby`, `orderto`, `modgrp_id`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `maxcols`, `showtags`, `showrss`, `showdesc`, `is_public`, `photoalbum`, `seolink`, `url`) VALUES
(1, 0, '--Корневой раздел--', 'Корневой раздел сайта', 1, 1, 1, 'pubdate', 'asc', 0, 1, 14, 0, '', 0, 1, 1, 1, 1, 0, 0, '', '--kornevoi-razdel--', ''),
(2, 1, 'Новости', '', 1, 1, 1, 'pubdate', 'ASC', 0, 12, 13, 1, '', 0, 2, 1, 1, 0, 0, 0, 'a:7:{s:2:"id";i:0;s:6:"titles";i:0;s:6:"header";s:0:"";s:7:"orderby";s:5:"title";s:7:"orderto";s:4:"desc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'novosti', ''),
(6, 1, 'Статьи', '<p>Тексты статей предоставлены службой <a href="http://referats.yandex.ru/">Яндекс.Рефераты</a></p>', 1, 1, 1, 'pubdate', 'ASC', 0, 2, 11, 1, '', 0, 1, 1, 1, 1, 1, 1, 'a:7:{s:2:"id";i:0;s:6:"titles";s:1:"0";s:6:"header";s:18:"Фотографии на тему";s:7:"orderby";s:4:"hits";s:7:"orderto";s:3:"asc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati', ''),
(13, 6, 'Маркетинг', '<p>Статьи по маркетингу</p>', 1, 1, 1, 'pubdate', 'DESC', 0, 7, 8, 2, '', 0, 3, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";i:0;s:6:"titles";s:1:"0";s:6:"header";s:18:"Фотографии на тему";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati/marketing', ''),
(12, 6, 'Геология', 'Статьи по геологии', 1, 1, 1, 'pubdate', 'DESC', 0, 3, 4, 2, '', 0, 1, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";s:1:"0";s:6:"titles";s:1:"0";s:6:"header";s:18:"Фотографии на тему";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";s:1:"2";s:3:"max";s:1:"8";}', 'stati/geologija', ''),
(11, 6, 'Астрономия', '<p>Статьи по астрономии</p>', 1, 1, 1, 'pubdate', 'DESC', 0, 9, 10, 2, '', 0, 4, 1, 1, 1, 1, 1, 'a:7:{s:2:"id";i:0;s:6:"titles";i:0;s:6:"header";s:18:"Фотографии на тему";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati/astronomija', ''),
(14, 6, 'Психология', 'Статьи по психологии', 1, 1, 1, 'pubdate', 'DESC', 0, 5, 6, 2, '', 0, 2, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";s:1:"0";s:6:"titles";s:1:"0";s:6:"header";s:18:"Фотографии на тему";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";s:1:"2";s:3:"max";s:1:"8";}', 'stati/psihologija', '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_clubs`
--

DROP TABLE IF EXISTS `cms_clubs`;
CREATE TABLE `cms_clubs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '1',
  `title` text NOT NULL,
  `description` longtext NOT NULL,
  `imageurl` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `clubtype` varchar(15) NOT NULL DEFAULT 'public',
  `published` int(11) NOT NULL DEFAULT '1',
  `maxsize` int(11) NOT NULL,
  `enabled_blogs` int(11) DEFAULT '1',
  `enabled_photos` int(11) DEFAULT '1',
  `rating` int(11) NOT NULL,
  `photo_premod` int(11) NOT NULL,
  `blog_premod` int(11) NOT NULL,
  `blog_min_karma` int(11) NOT NULL,
  `photo_min_karma` int(11) NOT NULL,
  `album_min_karma` int(11) NOT NULL DEFAULT '25',
  `join_min_karma` int(11) NOT NULL,
  `join_karma_limit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_clubs`
--

INSERT INTO `cms_clubs` (`id`, `admin_id`, `title`, `description`, `imageurl`, `pubdate`, `clubtype`, `published`, `maxsize`, `enabled_blogs`, `enabled_photos`, `rating`, `photo_premod`, `blog_premod`, `blog_min_karma`, `photo_min_karma`, `album_min_karma`, `join_min_karma`, `join_karma_limit`) VALUES
(14, 1, 'Клуб любителей InstantCMS', '', '', '2010-10-12 14:41:45', 'public', 1, 0, 1, 1, 0, 0, 0, 0, 0, 25, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_codecheck`
--

DROP TABLE IF EXISTS `cms_codecheck`;
CREATE TABLE `cms_codecheck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place` varchar(200) NOT NULL,
  `code` varchar(40) NOT NULL,
  `session_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_codecheck`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_comments`
--

DROP TABLE IF EXISTS `cms_comments`;
CREATE TABLE `cms_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `target` varchar(20) NOT NULL,
  `target_id` int(11) NOT NULL,
  `guestname` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `is_new` int(11) NOT NULL DEFAULT '1',
  `target_title` varchar(150) NOT NULL,
  `target_link` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_comments`
--

INSERT INTO `cms_comments` (`id`, `parent_id`, `pid`, `user_id`, `target`, `target_id`, `guestname`, `content`, `pubdate`, `published`, `is_new`, `target_title`, `target_link`) VALUES
(10, 0, 0, 1, 'article', 35, '', 'всегда мечтал прочитать такую статью! Спасибо яндексу :)', '2010-10-13 23:49:59', 1, 1, 'Эмпирический контраст: методология и особенности', '/stati/psihologija/yempiricheskii-kontrast-metodologija-i-osobennosti.html'),
(8, 0, 0, 1, 'article', 32, '', 'Питание прогиба исходным материалом, несмотря на не менее значительную разницу в плотности теплового потока, составляет биотит, так как совершенно однозначно указывает на существование', '2010-10-13 23:45:56', 1, 1, 'Почему нерезко плато?!!!', '/content/stati/geologija/pochemu-nerezko-plato.html'),
(11, 0, 0, 1, 'article', 37, '', 'Нет комментариев. Ваш будет первым!', '2010-10-14 23:28:38', 1, 1, 'Непреложный параметр', '/content/stati/astronomija/neprelozhnyi-parametr.html'),
(12, 11, 0, 2, 'article', 37, '', 'Южный Треугольник, тем не менее, уже 4,5 млрд лет расстояние нашей планеты от Солнца практически не меняется', '2010-11-07 19:01:32', 1, 1, 'Непреложный параметр', '/stati/astronomija/neprelozhnyi-parametr.html');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_comments_votes`
--

DROP TABLE IF EXISTS `cms_comments_votes`;
CREATE TABLE `cms_comments_votes` (
  `comment_id` int(11) NOT NULL,
  `comment_type` varchar(3) NOT NULL,
  `vote` smallint(6) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `comment_id` (`comment_id`,`comment_type`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_comments_votes`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_comment_targets`
--

DROP TABLE IF EXISTS `cms_comment_targets`;
CREATE TABLE `cms_comment_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(32) NOT NULL,
  `component` varchar(32) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target` (`target`,`component`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_comment_targets`
--

INSERT INTO `cms_comment_targets` (`id`, `target`, `component`, `title`) VALUES
(1, 'article', 'content', 'Статьи'),
(2, 'blog', 'blogs', 'Посты блогов'),
(3, 'palbum', 'photos', 'Фотоальбомы'),
(4, 'photo', 'photos', 'Фотографии галереи'),
(5, 'userphoto', 'users', 'Фотографии пользователей'),
(6, 'catalog', 'catalog', 'Записи каталога'),
(7, 'boarditem', 'board', 'Объявления'),
(8, 'faq', 'faq', 'Вопросы FAQ');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_components`
--

DROP TABLE IF EXISTS `cms_components`;
CREATE TABLE `cms_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `link` varchar(200) NOT NULL,
  `config` text NOT NULL,
  `internal` int(11) NOT NULL,
  `author` varchar(200) NOT NULL DEFAULT 'InstantCMS team',
  `published` int(11) NOT NULL DEFAULT '1',
  `version` varchar(6) NOT NULL DEFAULT '1.5',
  `system` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_components`
--

INSERT INTO `cms_components` (`id`, `title`, `link`, `config`, `internal`, `author`, `published`, `version`, `system`) VALUES
(1, 'Каталог статей', 'content', '---\nreaddesc: 0\nrating: 1\nperpage: 15\npt_show: 0\npt_disp: 0\npt_hide: 0\nautokeys: 1\nimg_small_w: 100\nimg_big_w: 200\nimg_sqr: 1\nimg_users: 1\nwatermark: 0\nwatermark_only_big: 0\naf_on: 0\naf_delete: 1\naf_showlink: 1\naf_forum_id: 1\naf_hidecat_id: 2\n', 0, 'InstantCMS team', 1, '1.5', 1),
(2, 'Регистрация', 'registration', '---\nreg_type: open\ninv_count: 3\ninv_karma: 1\ninv_period: WEEK\nis_on: 1\nact: 0\nsend: false\noffmsg: >\n  Регистрация приостановлена по\n  техническим причинам.\nfirst_auth_redirect: profile\nauth_redirect: profile\nname_mode: nickname\nbadnickname: |\n  администратор\n  админ\n  qwert\n  qwerty\n  123\n  admin\n  вася пупкин\nask_icq: 1\nask_birthdate: 1\nsend_greetmsg: 1\ngreetmsg: |\n  <h2>Привет!</h2>\n  <p><span style="font-size: medium;">Мы очень <span style="color: rgb(51, 153, 102);">рады</span> что ты зарегистрировался!</span></p>\n', 0, 'InstantCMS team', 1, '1.5', 1),
(3, 'Фотогалерея', 'photos', '---\nlink: 1\nsaveorig: 1\nmaxcols: 2\norderby: title\norderto: desc\nshowlat: 1\nwatermark: 1\ntumb_view: 2\ntumb_from: 1\ntumb_club: \nis_today: 0\n', 0, 'InstantCMS team', 1, '1.5', 1),
(4, 'Прайс-лист', 'price', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(5, 'Поиск', 'search', '---\nperpage: 10\ncomp: \n  - content\n  - photos\n  - price\n  - catalog\n  - forum\n  - blog\n', 0, 'InstantCMS team', 1, '1.5', 1),
(6, 'Конструктор форм', 'forms', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(7, 'Комментарии пользователей', 'comments', '---\nemail: comments@cms.ru\ncanguests: 1\nregcap: 0\npublish: 1\nsmiles: 1\nbbcode: 1\nselfdel: 0\nsubscribe: 1\nanchors: 0\nrecode: 0\nmin_karma: 0\nmin_karma_show: 0\nmin_karma_add: 0\nperpage: 20\nj_code: 1\ncmm_ajax: 0\n', 1, 'InstantCMS team', 1, '1.5', 1),
(8, 'Архив материалов', 'arhive', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(9, 'Универсальный каталог', 'catalog', '---\nemail: shop@site.ru\ndelivery: |\n  Сведения о доставке.\n  Этот текст можно изменить в настройках компонента &quot;Универсальный каталог&quot;.\nnotice: 1\npremod: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(10, 'Профили пользователей', 'users', '---\nshowgroup: 1\nsw_stats: \nsw_comm: 1\nsw_search: 1\nsw_forum: 1\nsw_photo: 1\nsw_wall: 1\nsw_friends: 1\nsw_blogs: 1\nsw_clubs: 1\nsw_feed: 1\nsw_content: 1\nsw_awards: 1\nsw_board: 1\nsw_msg: 1\nsw_guest: 1\nkarmatime: 3\nkarmaint: HOUR\nphotosize: 20\nwatermark: 0\nsmallw: 64\nmedw: 200\nmedh: 500\nsw_files: 1\nfilessize: 100\nprivforms: \n  - 3\n', 0, 'InstantCMS team', 1, '1.5', 1),
(12, 'Форум', 'forum', '---\nis_on: 1\nkarma: \npp_thread: 15\npp_forum: 15\nshowimg: 1\nimg_on: 1\nimg_max: 5\nfast_on: 1\nfast_bb: 1\nfa_on: 1\nfa_allow: -1\nfa_max: 25\nfa_ext: txt doc zip rar arj png gif jpg jpeg bmp\nfa_size: 1024\n', 0, 'InstantCMS team', 1, '1.5', 1),
(13, 'Статистика сайта', 'statistics', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(15, 'Блоги', 'blogs', '---\nperpage: 10\nmin_karma_private: 0\nmin_karma_public: 0\nmin_karma: 1\nrss_all: 1\nrss_one: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(16, 'Вопросы и ответы', 'faq', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(17, 'Баннеры', 'banners', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(19, 'RSS генератор', 'rssfeed', '---\naddsite: 1\nmaxitems: 50\nicon_on: 1\nicon_url: http://cmssite/images/rss.png\nicon_title: InstantCMS\nicon_link: http://cmssite/\n', 1, 'InstantCMS team', 1, '1.5', 1),
(21, 'Награждение пользователей', 'autoawards', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(22, 'Доска объявлений', 'board', '---\nmaxcols: 3\nobtypes: |\n  Продам\n  Куплю\n  Обменяю\n  Подарю\nshowlat: \npublic: 2\nphotos: 1\nsrok: 1\npubdays: 10\nwatermark: 0\naftertime: \ncomments: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(23, 'Клубы пользователей', 'clubs', '---\nenabled_blogs: 1\nenabled_photos: 1\nthumb1: 48\nthumb2: 200\nthumbsqr: 1\ncancreate: 1\nperpage: 10\ncreate_min_karma: 0\ncreate_min_rating: 0\n', 0, 'InstantCMS team', 1, '1.5', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_content`
--

DROP TABLE IF EXISTS `cms_content`;
CREATE TABLE `cms_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enddate` date NOT NULL,
  `is_end` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `content` longtext,
  `published` int(11) NOT NULL DEFAULT '1',
  `hits` int(11) NOT NULL,
  `meta_desc` text NOT NULL,
  `meta_keys` text NOT NULL,
  `showtitle` int(11) NOT NULL DEFAULT '1',
  `showdate` int(11) NOT NULL DEFAULT '1',
  `showlatest` int(11) NOT NULL DEFAULT '1',
  `showpath` int(11) NOT NULL DEFAULT '1',
  `ordering` int(1) NOT NULL,
  `comments` int(11) NOT NULL DEFAULT '1',
  `is_arhive` int(11) NOT NULL,
  `seolink` varchar(200) NOT NULL,
  `canrate` int(11) NOT NULL DEFAULT '1',
  `pagetitle` varchar(255) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seolink` (`seolink`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_content`
--

INSERT INTO `cms_content` (`id`, `category_id`, `user_id`, `pubdate`, `enddate`, `is_end`, `title`, `description`, `content`, `published`, `hits`, `meta_desc`, `meta_keys`, `showtitle`, `showdate`, `showlatest`, `showpath`, `ordering`, `comments`, `is_arhive`, `seolink`, `canrate`, `pagetitle`, `url`) VALUES
(20, 2, 1, '2009-03-01 15:56:00', '2009-05-22', 0, 'Наш сайт открыт!', '<p>Мы рады приветствовать вас на нашем сайте!</p>', '<p>Наш сайт открыт и начинает активно фунционировать.</p>\r\n<p>У нас большие планы на будущее. На этом сайте мы постарались разместить интересную информацию.</p>\r\n<p>Мы очень рады что вы читаете эту новость.</p>\r\n<p>Наша компания занимается бизнесом в сфере продажи строительных материалов.</p>', 1, 396, '', '', 1, 1, 1, 1, 1, 1, 0, 'novosti/nash-sait-otkryt', 1, '', ''),
(26, 2, 1, '2009-03-01 15:56:00', '2009-05-22', 0, 'Тестовая новость сайта', '<p>Это тестовая новость. Пользователи могут ее комментировать.</p>', '<p>Глубина очага землетрясения поднимает термокарст, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники. Туффит, формируя аномальные геохимические ряды, разогревает плейстоцен, что, однако, не уничтожило доледниковую переуглубленную гидросеть древних долин. Излом благоприятно покрывает трог, в соответствии с изменениями в суммарной минерализации. Топаз занимает форшок, за счет чего увеличивается мощность коры под многими хребтами. Ледниковое озеро сдвигает сель, что в конце концов приведет к полному разрушению хребта под действием собственного веса. При описанных условиях алмаз первичен.</p>', 1, 279, '', '', 1, 1, 1, 1, 2, 1, 0, 'novosti/testovaja-novost-saita', 1, '', ''),
(30, 11, 1, '0000-00-00 00:00:00', '2009-06-17', 0, 'Первоначальный нулевой меридиан', '<p><strong>Все известные астероиды имеют прямое движение</strong>, при этом тропический год меняет сарос &ndash; у таких объектов рукава столь фрагментарны и обрывочны, что их уже нельзя назвать спиральными. Как было показано выше, межзвездная матеpия дает pадиотелескоп Максвелла, при этом плотность Вселенной в 3 * 10 в 18-й степени раз меньше, с учетом некоторой неизвестной добавки скрытой массы. Пpотопланетное облако притягивает непреложный часовой угол (датировка приведена по Петавиусу, Цеху, Хайсу).</p>', '<p>Когда речь идет о галактиках, природа гамма-всплексов вызывает аргумент перигелия, хотя галактику в созвездии Дракона можно назвать карликовой. Хотя хpонологи не увеpены, им кажется, что тропический год точно вращает первоначальный астероид, день этот пришелся на двадцать шестое число месяца карнея, который у афинян называется метагитнионом.<br />\r\n<br />\r\nЛисичка на следующий год, когда было лунное затмение и сгорел древний храм Афины в Афинах (при эфоре Питии и афинском архонте Каллии), гасит случайный поперечник, хотя галактику в созвездии Дракона можно назвать карликовой. В отличие от пылевого и ионного хвостов, магнитное поле стабильно. Прямое восхождение на следующий год, когда было лунное затмение и сгорел древний храм Афины в Афинах (при эфоре Питии и афинском архонте Каллии), потенциально. Широта существенно притягивает непреложный надир, тем не менее, Дон Еманс включил в список всего 82-е Великие Кометы. Уравнение времени, следуя пионерской работе Эдвина Хаббла, вызывает далекий pадиотелескоп Максвелла, при этом плотность Вселенной в 3 * 10 в 18-й степени раз меньше, с учетом некоторой неизвестной добавки скрытой массы.<br />\r\n<br />\r\nСкоpость кометы в пеpигелии иллюстрирует астероид, а оценить проницательную способность вашего телескопа поможет следующая формула: Mпр.= 2,5lg Dмм + 2,5lg Гкрат + 4. Полнолуние пространственно неоднородно. Это можно записать следующим образом: V = 29.8 * sqrt(2/r &ndash; 1/a) км/сек, где ионный хвост однородно иллюстрирует радиант, при этом плотность Вселенной в 3 * 10 в 18-й степени раз меньше, с учетом некоторой неизвестной добавки скрытой массы. Азимут сложен. Магнитное поле непрерывно.</p>', 1, 67, '', '', 1, 1, 1, 1, 1, 1, 0, 'stati/astronomija/pervonachalnyi-nulevoi-meridian', 1, 'Первоначальный нулевой меридиан', ''),
(31, 12, 1, '2009-05-20 16:24:00', '2009-05-20', 0, 'Останцовый лакколит: основные моменты', '<p>Двойное лучепреломление, с учетом региональных факторов, покрывает монтмориллонит, в то время как значения максимумов изменяются в широких пределах. Алмаз эффективно составляет эффузивный блеск, в соответствии с изменениями в суммарной минерализации. Инфлюация структурно ослабляет парагенезис, образуя на границе с Западно-Карельским поднятием своеобразную систему грабенов. Глубина очага землетрясения, разделенные узкими линейновытянутыми зонами выветрелых пород, причленяет к себе каустобиолит, что в общем свидетельствует о преобладании тектонических опусканий в это время.</p>', '<p>Оттаивание пород обрывисто прекращает фирн, поскольку непосредственно мантийные струи не наблюдаются. Капиллярное поднятие интенсивно. Элювиальное образование определяет несовершенный трог, что свидетельствует о проникновении днепровских льдов в бассейн Дона. Сдвиг сингонально фоссилизирует денудационно-аккумулятивный апофиз, что в конце концов приведет к полному разрушению хребта под действием собственного веса. Апофиз дискретно переоткладывает ийолит-уртит, что, однако, не уничтожило доледниковую переуглубленную гидросеть древних долин.</p>\r\n<p>Рисчоррит разогревает цокольный туффит, что, однако, не уничтожило доледниковую переуглубленную гидросеть древних долин. Геосинклиналь обедняет анортит, основными элементами которого являются обширные плосковершинные и пологоволнистые возвышенности. Ложе, так же, как и в других регионах, индивидуально. Брекчия опускает пирокластический бентос, что, однако, не уничтожило доледниковую переуглубленную гидросеть древних долин. Ложе сингонально прекращает молого-шекснинский ортоклаз, основными элементами которого являются обширные плосковершинные и пологоволнистые возвышенности. В типологическом плане вся территория Нечерноземья выветривание поднимает сель, образуя на границе с Западно-Карельским поднятием своеобразную систему грабенов.</p>', 1, 66, 'Останцовый лакколит: основные моменты', 'переуглубленную, гидросеть, долин, доледниковую, древних, уничтожило, однако, обширные, плосковершинные, пологоволнистые, возвышенности, являются, элементами, апофиз, прекращает, сингонально, основными, которого, уничтожило доледниковую, древних долин, гидросеть древних, переуглубленную гидросеть, доледниковую переуглубленную, обширные плосковершинные, пологоволнистые возвышенности, являются обширные, основными элементами, элементами которого, которого являются, уничтожило доледниковую переуглубленную, переуглубленную гидросеть древних, доледниковую переуглубленную гидросеть, являются обширные плосковершинные, которого являются обширные, основными элементами которого, элементами которого являются', 1, 1, 1, 1, 2, 1, 0, 'stati/geologija/ostancovyi-lakkolit-osnovnye-momenty', 1, '', ''),
(32, 12, 1, '2009-05-20 16:24:00', '2009-06-17', 0, 'Почему нерезко плато?!!!', '<p>Трещинноватость пород опускает дрейф континентов, что, однако, не уничтожило доледниковую переуглубленную гидросеть древних долин. Магматическая дифференциация ортогонально смещает форшок, что в общем свидетельствует о преобладании тектонических опусканий в это время. Элювиальное образование, которая в настоящее время находится ниже уровня моря, ослабляет аморфный цвет, в то время как значения максимумов изменяются в широких пределах. Мергель слагает орогенез, делая этот типологический таксон районирования носителем важнейших инженерно-геологических характеристик природных условий.</p>', '<p>Плейстоцен ослабляет плоскостной оз, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники. Согласно изостатической концепции Эйри, делювий покрывает отрог, что в конце концов приведет к полному разрушению хребта под действием собственного веса. Вулканическое стекло занимает мощный коллювий, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники. Грязевой вулкан подпитывает полого-холмистый базис эрозии, в то время как значения максимумов изменяются в широких пределах. Трещинноватость пород составляет первичный водоносный этаж, в соответствии с изменениями в суммарной минерализации. Бифуркация русла, но если принять для простоты некоторые докущения, разогревает кислый протерозой, включая и гряды Чернова, Чернышева и др.<br />\r\n<br />\r\nПо характеру рельефа количество пирокластического материала сменяет эпигенез, что увязывается со структурно-тектонической обстановкой, гидродинамическими условиями и литолого-минералогическим составом пород. Ледниковое озеро, но если принять для простоты некоторые докущения, продольно сбрасывает многолетнемерзлый алмаз, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники. Питание прогиба исходным материалом, несмотря на не менее значительную разницу в плотности теплового потока, составляет биотит, так как совершенно однозначно указывает на существование и рост в период оформления палеогеновой поверхности выравнивания. Брекчия достаточно хорошо вызывает плейстоцен, и в то же время устанавливается достаточно приподнятый над уровнем моря коренной цоколь.</p>', 1, 21, '', '', 1, 1, 1, 1, 1, 1, 0, 'stati/geologija/pochemu-nerezko-plato', 1, 'Почему нерезко плато?!!!', ''),
(33, 13, 1, '2009-05-20 15:50:00', '2010-10-28', 0, 'Культурный медийный канал: методология и особенности', '<p>Поведенческий таргетинг неестественно уравновешивает conversion rate, не считаясь с затратами. Системный анализ изменяет PR, не считаясь с затратами. VIP-мероприятие, анализируя результаты рекламной кампании, допускает побочный PR-эффект, не считаясь с затратами. Как отмечает Майкл Мескон, стимулирование коммьюнити многопланово трансформирует стратегический имидж, не считаясь с затратами. Итак, ясно, что жизненный цикл продукции основан на опыте.</p>', '<p>Стоит отметить, что презентация стабилизирует комплексный анализ ситуации, полагаясь на инсайдерскую информацию. Рейт-карта, отбрасывая подробности, спонтанно трансформирует пак-шот, оптимизируя бюджеты. А вот по мнению аналитиков медиапланирование изменяет общественный рекламный макет, опираясь на опыт западных коллег. Рейт-карта, безусловно, все еще интересна для многих. Такое понимание ситуации восходит к Эл Райс, при этом метод изучения рынка ригиден.</p>\r\n<p>К тому же ретроконверсия национального наследия отталкивает потребительский нестандартный подход, невзирая на действия конкурентов. Анализ рыночных цен традиционно создает эмпирический департамент маркетинга и продаж, осознав маркетинг как часть производства. Лидерство в продажах,&nbsp;следовательно, специфицирует инструмент маркетинга, опираясь на опыт западных коллег. Узнавание бренда, пренебрегая деталями, продуцирует межличностный нишевый проект, используя опыт предыдущих кампаний. Рыночная информация тормозит потребительский рынок, оптимизируя бюджеты.</p>', 1, 9, 'Культурный медийный канал: методология и особенности', 'западных, коллег, потребительский, маркетинга, опираясь, бюджеты, ситуации, карта, оптимизируя, анализ, западных коллег, оптимизируя бюджеты', 1, 1, 1, 1, 2, 1, 0, 'stati/marketing/kulturnyi-mediinyi-kanal-metodologija-i-osobenosti', 1, '', ''),
(34, 13, 1, '2009-05-20 11:33:00', '2009-07-22', 0, 'Эластичность спроса', '<p>Дело в том, что потребление синхронизирует институциональный рекламный макет, полагаясь на инсайдерскую информацию. Целевая аудитория интегрирована. Тем не менее, каждая сфера рынка раскручивает эмпирический анализ зарубежного опыта, расширяя долю рынка.</p>', '<p>Примерная структура маркетингового исследования,&nbsp;в&nbsp;рамках&nbsp;сегодняшних&nbsp;воззрений, тормозит тактический план размещения, оптимизируя бюджеты. Фирменный стиль парадоксально программирует коллективный маркетинг, оптимизируя бюджеты. Фокусировка позиционирует пак-шот, полагаясь на инсайдерскую информацию.</p>\r\n<p>Опросная анкета ускоряет диктат потребителя, используя опыт предыдущих кампаний. Стимулирование сбыта настроено позитивно. Стимулирование коммьюнити охватывает комплексный конкурент, используя опыт предыдущих кампаний. Рекламное сообщество стабилизирует выставочный стенд, опираясь на опыт западных коллег. Стратегический рыночный план программирует потребительский инструмент маркетинга, учитывая результат предыдущих медиа-кампаний. По сути, сервисная стратегия притягивает институциональный выставочный стенд, не считаясь с затратами.</p>\r\n<p>По мнению ведущих маркетологов, маркетинговая активность позитивно стабилизирует рекламный бриф, признавая определенные рыночные тенденции. Рекламная кампания неоднозначна. Правда, специалисты отмечают, что сущность и концепция маркетинговой программы откровенна. Нестандартный подход концентрирует экспериментальный стратегический рыночный план, не считаясь с затратами. Наряду с этим, узнавание бренда слабо упорядочивает креатив, опираясь на опыт западных коллег.</p>\r\n<p>&nbsp;</p>', 1, 4, 'Почему очевидна не для всех эластичность спроса?', 'кампаний, предыдущих, коллег, западных, опираясь, стратегический, затратами, рыночный, стенд, считаясь, стабилизирует, программирует, бюджеты, используя, стимулирование, оптимизируя, позитивно, выставочный, стратегический рыночный, западных коллег, выставочный стенд, предыдущих кампаний, оптимизируя бюджеты', 1, 1, 1, 1, 1, 1, 0, 'stati/marketing/yelastichnost-sprosa', 1, '', ''),
(29, 11, 1, '2009-05-20 13:41:00', '2009-07-21', 0, 'Космический возмущающий фактор: гипотеза и теории', '<p>Лисичка колеблет астероидный эксцентриситет, при этом плотность Вселенной в 3 * 10 в 18-й степени раз меньше, с учетом некоторой неизвестной добавки скрытой массы. Поперечник, после осторожного анализа, гасит случайный аргумент перигелия &ndash; это скорее индикатор, чем примета. Фаза ищет узел, однако большинство спутников движутся вокруг своих планет в ту же сторону, в какую вращаются планеты. Pадиотелескоп Максвелла недоступно колеблет маятник Фуко, таким образом, часовой пробег каждой точки поверхности на экваторе равен 1666км. Природа гамма-всплексов, оценивая блеск освещенного металического шарика, существенно вызывает лимб, как это случилось в 1994 году с кометой Шумейкеpов-Леви 9. Экватор, а там действительно могли быть видны звезды, о чем свидетельствует Фукидид неравномерен.</p>', '<p>Декретное время, это удалось установить по характеру спектра, мгновенно. Зенитное часовое число случайно. Межзвездная матеpия вероятна. Магнитное поле ничтожно иллюстрирует Тукан, хотя это явно видно на фотогpафической пластинке, полученной с помощью 1.2-метpового телескопа. Аргумент перигелия меняет перигелий, но это не может быть причиной наблюдаемого эффекта. Земная группа формировалась ближе к Солнцу, однако апогей иллюстрирует ионный хвост, однако большинство спутников движутся вокруг своих планет в ту же сторону, в какую вращаются планеты.</p>\r\n<p>{pagebreak}</p>\r\n<p>Афелий перечеркивает популяционный индекс, таким образом, часовой пробег каждой точки поверхности на экваторе равен 1666км. Натуральный логарифм, как бы это ни казалось парадоксальным, оценивает натуральный логарифм - это солнечное затмение предсказал ионянам Фалес Милетский. Магнитное поле жизненно гасит маятник Фуко, хотя это явно видно на фотогpафической пластинке, полученной с помощью 1.2-метpового телескопа. Экскадрилья, как бы это ни казалось парадоксальным, отражает перигелий &ndash; это скорее индикатор, чем примета. Атомное время, в первом приближении, постоянно. Небесная сфера, сублимиpуя с повеpхности ядpа кометы, жизненно притягивает астероидный радиант, об этом в минувшую субботу сообщил заместитель администратора NASA.</p>', 1, 92, 'Космический возмущающий фактор: гипотеза и теории', 'натуральный, однако, перигелий, логарифм, казалось, жизненно, парадоксальным, телескопа, метpового, видно, иллюстрирует, магнитное, фотогpафической, пластинке, помощью, полученной, время, казалось парадоксальным, натуральный логарифм, метpового телескопа, пластинке полученной, фотогpафической пластинке, фотогpафической пластинке полученной', 1, 1, 1, 1, 5, 1, 0, 'stati/astronomija/kosmicheskii-vozmushayushii-faktor-gipoteza-i-teori', 1, '', ''),
(35, 14, 1, '2009-05-20 16:24:00', '2009-05-20', 0, 'Эмпирический контраст: методология и особенности', '', '<p>Чувство аннигилирует кризис, следовательно тенденция к конформизму связана с менее низким интеллектом.</p>\r\n<p>Архетип изменяем. Сознание представляет собой объект, тем не менее как только ортодоксальность окончательно возобладает, даже эта маленькая лазейка будет закрыта. Сознание, в первом приближении, иллюстрирует бихевиоризм, здесь описывается централизующий процесс или создание нового центра личности. Реакция параллельна. Гомеостаз, конечно, дает филосовский аутизм, что отмечают такие крупнейшие ученые как Фрейд, Адлер, Юнг, Эриксон, Фромм. Толпа социально просветляет депрессивный стимул в силу которого смешивает субъективное и объективное, переносит свои внутренние побуждения на реальные связи вещей.</p>\r\n<p>Парадигма интегрирует онтогенез речи одинаково по всем направлениям. Роджерс определял терапию как, лидерство отражает контраст, хотя этот факт нуждается в дальнейшей проверке наблюдением. Ригидность, иcходя из того, что аннигилирует сублимированный ассоцианизм, так, например, Ричард Бендлер для построения эффективных состояний использовал изменение субмодальностей. Интеллект, например, неравномерен. Психосоматика, например, просветляет сублимированный гештальт, о чем и писал А. Маслоу в своей работе &quot;Мотивация и личность&quot;. Генезис, конечно, неумеренно дает понимающий инсайт, как и предсказывает теория о бесполезном знании.</p>', 1, 8, 'Эмпирический контраст: методология и особенности', 'например, сублимированный, просветляет, конечно, менее, сознание, аннигилирует', 1, 1, 1, 1, 1, 1, 0, 'stati/psihologija/yempiricheskii-kontrast-metodologija-i-osobennosti', 1, '', ''),
(36, 14, 1, '2009-05-20 18:29:00', '2009-09-16', 0, 'Групповой аутотренинг глазами современников', '<p>Ассоцианизм, как бы это ни казалось парадоксальным, аннигилирует аутизм, здесь описывается централизующий процесс или создание нового центра личности. Структурный голод столь же важен для жизни, как и закон жизненно отражает опасный код, что отмечают такие крупнейшие ученые как Фрейд, Адлер, Юнг, Эриксон, Фромм.</p>', '<p><strong>Л.С. Выготский понимал тот факт, что сновидение последовательно отталкивает интеракционизм, это обозначено Ли Россом как фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах. Чувство абсурдно понимает понимающий эриксоновский гипноз одинаково по всем направлениям. Генезис, в представлении Морено, отталкивает социальный объект, хотя Уотсон это отрицал. Структурный голод столь же важен для жизни, как и сознание начинает опасный конформизм, следовательно тенденция к конформизму связана с менее низким интеллектом. Личность, по определению, доступна.</strong><br />\r\n<br />\r\nКоллективное бессознательное, например, начинает субъект, так, например, Ричард Бендлер для построения эффективных состояний использовал изменение субмодальностей. Психическая саморегуляция последовательно притягивает позитивистский стресс, что лишний раз подтверждает правоту З. Фрейда. Репрезентативная система выбирает эгоцентризм, это обозначено Ли Россом как фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах. Выготский разработал, ориентируясь на методологию марксизма, учение которое утверждает что, предсознательное непоследовательно начинает бихевиоризм, независимо от психического состояния пациента. Сновидение осознаёт инсайт, хотя этот факт нуждается в дальнейшей проверке наблюдением. Однако Э. Дюркгейм утверждал, что комплекс изящно дает гештальт, независимо от психического состояния пациента.</p>\r\n<p>{ФОРМА=Написать письмо}</p>', 1, 13, 'Групповой аутотренинг глазами современников', 'начинает, экспериментах, многих, например, независимо, пациента, состояния, психического, прослеживается, которая, отталкивает, последовательно, сновидение, обозначено, россом, атрибуции, ошибка, фундаментальная, выготский, психического состояния, состояния пациента, многих экспериментах, которая прослеживается, ошибка атрибуции, атрибуции которая, фундаментальная ошибка, психического состояния пациента, атрибуции которая прослеживается, ошибка атрибуции которая, фундаментальная ошибка атрибуции', 1, 1, 0, 1, 2, 0, 0, 'stati/psihologija/gruppovoi-autotrening-glazami-sovremennikov', 1, 'Аутотренинг', ''),
(37, 13, 1, '2010-10-14 00:08:00', '2010-10-28', 0, 'Непреложный параметр', '<p>Восход , на первый взгляд, пространственно отражает случайный секстант,  Плутон не входит в эту классификацию. Юпитер вращает близкий параллакс,  хотя это явно видно на фотогpафической пластинке, полученной с помощью  1.2-метpового телескопа. Перигелий, оценивая блеск освещенного  металического шарика, жизненно ищет космический мусор, в таком случае  эксцентриситеты и наклоны орбит возрастают. Зенит прочно ищет  первоначальный азимут, как это случилось в 1994 году с кометой  Шумейкеpов-Леви 9.</p>', '<p>Эпоха меняет нулевой меридиан  &ndash; у таких объектов рукава столь  фрагментарны и обрывочны, что их уже нельзя назвать спиральными. В  отличие от пылевого и ионного хвостов, pадиотелескоп Максвелла жизненно  решает экватор, данное соглашение было заключено на 2-й международной  конференции &quot;Земля из космоса - наиболее эффективные решения&quot;. В отличие  от давно известных астрономам планет земной группы, уравнение времени  оценивает случайный натуральный логарифм, однако большинство спутников  движутся вокруг своих планет в ту же сторону, в какую вращаются планеты.  Метеорный дождь теоретически возможен. Соединение параллельно.</p>\r\n<p>Фаза, после осторожного анализа, точно перечеркивает метеорный дождь   &ndash; север вверху, восток слева. После того как тема сформулирована,  газопылевое облако представляет собой часовой угол (расчет Тарутия  затмения точен - 23 хояка 1 г. II О. = 24.06.-771). Солнечное затмение, и  это следует подчеркнуть, отражает Южный Треугольник, тем не менее, уже  4,5 млрд лет расстояние нашей планеты от Солнца практически не меняется.  Противостояние, на первый взгляд, однородно выбирает метеорит, данное  соглашение было заключено на 2-й международной конференции &quot;Земля из  космоса - наиболее эффективные решения&quot;. Ганимед недоступно решает  метеорит, об интересе Галла к астрономии и затмениям Цицерон говорит  также в трактате &quot;О старости&quot; (De senectute). Спектральный класс  перечеркивает экваториальный болид , день этот пришелся на двадцать  шестое число месяца карнея, который у афинян называется метагитнионом.</p>', 1, 30, 'Непреложный параметр', '", планет, решения", планеты, эффективные, метеорный, метеорит, перечеркивает, дождь, наиболее, земля, данное, решает, отличие, соглашение, заключено, –, конференции, международной, космоса, наиболее эффективные, эффективные решения", метеорный дождь, космоса наиболее, " земля, международной конференции, конференции ", данное соглашение, наиболее эффективные решения", космоса наиболее эффективные', 1, 1, 1, 1, 3, 1, 0, 'stati/astronomija/neprelozhnyi-parametr', 1, 'Непреложный параметр', ''),
(38, 11, 2, '2010-10-14 00:08:00', '2010-10-28', 0, 'Пpотопланетное облако как ионный хвост', '', '<p>Все известные астероиды имеют прямое движение, при этом зенитное  часовое число однократно. Космогоническая гипотеза Шмидта позволяет  достаточно просто объяснить эту нестыковку, однако отвесная линия решает  натуральный логарифм, однако большинство спутников движутся вокруг  своих планет в ту же сторону, в какую вращаются планеты. Различное  расположение перечеркивает спектральный класс, учитывая, что в одном  парсеке 3,26 световых года. Очевидно, что нулевой меридиан гасит  космический Каллисто, но это не может быть причиной наблюдаемого  эффекта. Многие кометы имеют два хвоста, однако азимут перечеркивает  секстант, таким образом, атмосферы этих планет плавно переходят в жидкую  мантию. Отвесная линия дает популяционный индекс, как это случилось в  1994 году с кометой Шумейкеpов-Леви 9.</p>\r\n<p>У планет-гигантов нет твёрдой поверхности, таким образом  пpотопланетное облако жизненно притягивает космический керн, Плутон не  входит в эту классификацию. Звезда, несмотря на внешние воздействия,  отражает далекий Млечный Путь, данное соглашение было заключено на 2-й  международной конференции &quot;Земля из космоса - наиболее эффективные  решения&quot;. Уравнение времени, на первый взгляд, колеблет перигей, это  довольно часто наблюдается у сверхновых звезд второго типа. Рефракция,  как бы это ни казалось парадоксальным, ищет первоначальный радиант, но  это не может быть причиной наблюдаемого эффекта. Вселенная достаточно  огромна, чтобы гелиоцентрическое расстояние представляет собой  эффективный диаметp, и в этом вопросе достигнута такая точность  расчетов, что, начиная с того дня, как мы видим, указанного Эннием и  записанного в &quot;Больших анналах&quot;, было вычислено время предшествовавших  затмений солнца, начиная с того, которое в квинктильские ноны произошло в  царствование Ромула. Hатpиевые атомы предварительно были замечены  близко с центром других комет, но экскадрилья оценивает эллиптический  перигей, и в этом вопросе достигнута такая точность расчетов, что,  начиная с того дня, как мы видим, указанного Эннием и записанного в  &quot;Больших анналах&quot;, было вычислено время предшествовавших затмений  солнца, начиная с того, которое в квинктильские ноны произошло в  царствование Ромула.</p>\r\n<p>Космогоническая гипотеза Шмидта позволяет достаточно просто объяснить  эту нестыковку, однако природа гамма-всплексов доступна. Комета  Хейла-Боппа последовательно перечеркивает керн, учитывая, что в одном  парсеке 3,26 световых года. Ганимед последовательно отражает восход , но  кольца видны только при 40&ndash;50. Орбита ищет вращательный лимб, хотя для  имеющих глаза-телескопы туманность Андромеды показалась бы на небе  величиной с треть ковша Большой Медведицы. Млечный Путь перечеркивает  далекий восход  (датировка приведена по Петавиусу, Цеху, Хайсу). Прямое  восхождение, по определению, вращает Юпитер, но это не может быть  причиной наблюдаемого эффекта.</p>', 1, 0, 'Пpотопланетное облако как ионный хвост', 'перечеркивает, начиная, причиной, эффекта, планет, наблюдаемого, ", записанного, больших, анналах", вычислено, эннием, указанного, расчетов, прямое, видим, время, предшествовавших, царствование, ромула, последовательно, восход, произошло, квинктильские, затмений, солнца, которое, точность, такая, парсеке, световых, космический, позволяет, одном, учитывая, линия, нестыковку, просто, шмидта, гипотеза, имеют, перигей, вопросе, достигнута, млечный, далекий, таким, образом, отражает, отвесная, наблюдаемого эффекта, причиной наблюдаемого, " больших, указанного эннием, видим указанного, больших анналах", вычислено время, солнца начиная, царствование ромула, затмений солнца, предшествовавших затмений, время предшествовавших, точность расчетов, достигнута такая, просто объяснить, достаточно просто, позволяет достаточно, шмидта позволяет, нестыковку однако, отвесная линия, гипотеза шмидта, вопросе достигнута, таким образом, одном парсеке, такая точность, причиной наблюдаемого эффекта, " больших анналах", время предшествовавших затмений, предшествовавших затмений солнца, затмений солнца начиная, видим указанного эннием, такая точность расчетов, шмидта позволяет достаточно, позволяет достаточно просто, достаточно просто объяснить, гипотеза шмидта позволяет', 1, 1, 1, 1, 2, 1, 0, 'stati/astronomija/ppotoplanetnoe-oblako-kak-ionyi-hvost', 1, 'Пpотопланетное облако как ионный хвост', '');
INSERT INTO `cms_content` (`id`, `category_id`, `user_id`, `pubdate`, `enddate`, `is_end`, `title`, `description`, `content`, `published`, `hits`, `meta_desc`, `meta_keys`, `showtitle`, `showdate`, `showlatest`, `showpath`, `ordering`, `comments`, `is_arhive`, `seolink`, `canrate`, `pagetitle`, `url`) VALUES
(39, 11, 6, '2010-10-28 15:50:00', '2010-10-28', 0, 'Линейно зависимый постулат: предпосылки и развитие', '<p>Подмножество специфицирует постулат, что известно даже школьникам.  Интеграл по поверхности,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, позитивно  обуславливает нормальный бином Ньютона, что известно даже школьникам.  Рассмотрим непрерывную функцию  y = f ( x ), заданную на отрезке [ a, b  ], высшая арифметика непосредственно раскручивает абсолютно сходящийся  ряд, что неудивительно. Критерий сходимости  Коши,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, однородно создает неопровержимый  интеграл по ориентированной области, что несомненно приведет нас к  истине.</p>', '<p>Бесконечно малая величина,&nbsp;очевидно, в принципе масштабирует  косвенный интеграл Пуассона, что несомненно приведет нас к истине.  Иррациональное число позиционирует бином Ньютона, что и требовалось  доказать. Следствие: криволинейный интеграл оправдывает убывающий  детерминант, что и требовалось доказать. Функция B(x,y),&nbsp;следовательно,  позиционирует отрицательный лист Мёбиуса, при этом, вместо 13 можно  взять любую другую константу. Определитель системы линейных уравнений  притягивает интеграл от функции, обращающейся в бесконечность вдоль  линии, явно демонстрируя всю чушь вышесказанного.</p>\r\n<p>Не факт, что скачок функции масштабирует экспериментальный бином  Ньютона, что несомненно приведет нас к истине. Метод последовательных  приближений порождает интеграл по ориентированной области, что и  требовалось доказать. Скалярное поле масштабирует экспериментальный  полином, дальнейшие выкладки оставим студентам в качестве несложной  домашней работы. Умножение двух векторов (векторное) последовательно.  Аксиома, не вдаваясь в подробности, по-прежнему востребована.</p>', 1, 0, 'Линейно зависимый постулат: предпосылки и развитие', 'интеграл, требовалось, масштабирует, доказать, экспериментальный, ньютона, функции, позиционирует, несомненно, приведет, истине, &nbsp, бином, требовалось доказать, масштабирует экспериментальный, бином ньютона, несомненно приведет', 1, 1, 1, 1, 4, 1, 0, 'stati/astronomija/lineino-zavisimyi-postulat-predposylki-i-razvitie', 1, 'Линейно зависимый постулат: предпосылки и развитие', ''),
(40, 11, 8, '2010-10-28 15:52:00', '2010-10-28', 0, 'Линейно зависимый постулат: предпосылки и развитие', '', '<p>Подмножество специфицирует постулат, что известно даже школьникам.  Интеграл по поверхности,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, позитивно  обуславливает нормальный бином Ньютона, что известно даже школьникам.  Рассмотрим непрерывную функцию  y = f ( x ), заданную на отрезке [ a, b  ], высшая арифметика непосредственно раскручивает абсолютно сходящийся  ряд, что неудивительно. Критерий сходимости  Коши,&nbsp;как&nbsp;следует&nbsp;из&nbsp;вышесказанного, однородно создает неопровержимый  интеграл по ориентированной области, что несомненно приведет нас к  истине.</p>\r\n<p>Бесконечно малая величина,&nbsp;очевидно, в принципе масштабирует  косвенный интеграл Пуассона, что несомненно приведет нас к истине.  Иррациональное число позиционирует бином Ньютона, что и требовалось  доказать. Следствие: криволинейный интеграл оправдывает убывающий  детерминант, что и требовалось доказать. Функция B(x,y),&nbsp;следовательно,  позиционирует отрицательный лист Мёбиуса, при этом, вместо 13 можно  взять любую другую константу. Определитель системы линейных уравнений  притягивает интеграл от функции, обращающейся в бесконечность вдоль  линии, явно демонстрируя всю чушь вышесказанного.</p>\r\n<p>Не факт, что скачок функции масштабирует экспериментальный бином  Ньютона, что несомненно приведет нас к истине. Метод последовательных  приближений порождает интеграл по ориентированной области, что и  требовалось доказать. Скалярное поле масштабирует экспериментальный  полином, дальнейшие выкладки оставим студентам в качестве несложной  домашней работы. Умножение двух векторов (векторное) последовательно.  Аксиома, не вдаваясь в подробности, по-прежнему востребована.</p>', 1, 0, 'Линейно зависимый постулат: предпосылки и развитие', 'интеграл, &nbsp, ньютона, масштабирует, несомненно, бином, приведет, требовалось, доказать, вышесказанного, истине, экспериментальный, позиционирует, функции, ориентированной, как&nbsp, школьникам, следует&nbsp, из&nbsp, известно, области, требовалось доказать, несомненно приведет, бином ньютона, как&nbsp следует&nbsp, масштабирует экспериментальный, следует&nbsp из&nbsp, ориентированной области, &nbsp как&nbsp, из&nbsp вышесказанного, следует&nbsp из&nbsp вышесказанного, как&nbsp следует&nbsp из&nbsp, &nbsp как&nbsp следует&nbsp', 1, 1, 1, 1, 3, 1, 0, 'stati/astronomija/lineino-zavisimyi-postulat-predposylki-i-razvitie-40', 1, 'Линейно зависимый постулат: предпосылки и развитие', '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_content_access`
--

DROP TABLE IF EXISTS `cms_content_access`;
CREATE TABLE `cms_content_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content_type` varchar(100) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_content_access`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_cron_jobs`
--

DROP TABLE IF EXISTS `cms_cron_jobs`;
CREATE TABLE `cms_cron_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(50) NOT NULL,
  `job_interval` smallint(6) NOT NULL DEFAULT '1',
  `job_run_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `component` varchar(20) NOT NULL,
  `model_method` varchar(100) NOT NULL,
  `custom_file` varchar(250) NOT NULL,
  `is_enabled` tinyint(4) NOT NULL DEFAULT '1',
  `is_new` smallint(6) NOT NULL DEFAULT '1',
  `comment` varchar(200) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `class_method` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_name` (`job_name`,`is_enabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_cron_jobs`
--

INSERT INTO `cms_cron_jobs` (`id`, `job_name`, `job_interval`, `job_run_date`, `component`, `model_method`, `custom_file`, `is_enabled`, `is_new`, `comment`, `class_name`, `class_method`) VALUES
(1, 'photos_clear', 24, '2010-11-11 20:53:13', 'users', 'clearUploadedPhotos', '', 1, 1, 'Удаление неиспользуемых личных фотографий', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_downloads`
--

DROP TABLE IF EXISTS `cms_downloads`;
CREATE TABLE `cms_downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileurl` varchar(250) NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_downloads`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_event_hooks`
--

DROP TABLE IF EXISTS `cms_event_hooks`;
CREATE TABLE `cms_event_hooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(50) NOT NULL,
  `plugin_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event` (`event`,`plugin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_event_hooks`
--

INSERT INTO `cms_event_hooks` (`id`, `event`, `plugin_id`) VALUES
(6, 'GET_ARTICLE', '5'),
(3, 'INSERT_WYSIWYG', '3'),
(7, 'USER_PROFILE', '6'),
(11, 'ADD_ARTICLE_DONE', '8'),
(10, 'ADD_POST_DONE', '8');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_faq_cats`
--

DROP TABLE IF EXISTS `cms_faq_cats`;
CREATE TABLE `cms_faq_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_faq_cats`
--

INSERT INTO `cms_faq_cats` (`id`, `parent_id`, `title`, `description`, `published`) VALUES
(3, 0, 'Технические вопросы', '<p>Обсуждение неисправностей и поломок</p>', 1),
(4, 0, 'Юридические вопросы', '<p>Связанные с законодательным правом</p>', 1),
(5, 3, 'Вопросы сервисному отделу', '<p>По поводу гарантийного ремонта</p>', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_faq_quests`
--

DROP TABLE IF EXISTS `cms_faq_quests`;
CREATE TABLE `cms_faq_quests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `quest` text NOT NULL,
  `answer` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `answeruser_id` int(11) NOT NULL,
  `answerdate` datetime NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_faq_quests`
--

INSERT INTO `cms_faq_quests` (`id`, `category_id`, `pubdate`, `published`, `quest`, `answer`, `user_id`, `answeruser_id`, `answerdate`, `hits`) VALUES
(3, 3, '2008-04-14 20:09:54', 1, 'У меня не работает чайник. Когда я наливаю в него воду и ставлю кипятиться, он светится не естественным образом. Когда вода начинает кипеть, чайник перестает светиться и начинает подпрыгивать. Один раз он упрыгал в ванну.\r\n\r\nПодскажите, как мне его починить?', '<p>Василий, ваша проблема встречается очень часто. И, к сожалению, вряд ли вам чем-то можно помочь. Единственное, что мы можем - это заявить на вас в милицию. Так что решайте, что вам дороже.</p>', 2, 1, '2008-04-14 20:09:54', 32),
(4, 4, '2008-04-14 20:28:51', 1, 'Я продал свою собаку. Потом я передумал. И потребовал ее назад. Деньги конечно вернул. Но случилась небольшая беда. Я не помню кому продал собаку и кому вернул деньги. Как мне быть?', '<p>Вероятнее всего что вашей собаки уже нет вживых.</p>\r\n<p>Вот посудите сами - идет по улице одинокий щенок. Тут к нему подходит негр и говорит - &quot;займи пять рублей&quot;. Ну конечно щенок испугался и убежал.</p>\r\n<p>Но все бы было хорошо если бы не трамвай. Щенок побежал и попал под трамвай. Вот такая печальная история.</p>', 2, 1, '2008-04-14 20:28:51', 32),
(5, 5, '2008-04-14 00:00:00', 1, 'Скажите пожалуйста когда мне вернут экскаватор который я сдал на ремонт в июне 1937 года?', '<p>К сожалению, ответственному за это упущение сотруднику недавно исполнилось 94 года и мы не смогли добиться от него внятного ответа. Он утверждает что не помнит, мы конечно не верим и продолжим пытки, но на это потребуется время. Просим отнестись с пониманием.</p>', 2, 1, '2008-04-16 00:00:00', 36);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_filters`
--

DROP TABLE IF EXISTS `cms_filters`;
CREATE TABLE `cms_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(100) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_filters`
--

INSERT INTO `cms_filters` (`id`, `title`, `description`, `link`, `published`) VALUES
(1, 'Автозамена / Фильтр ссылок', 'Фильтр выполняет замену выражений по созданным вами правилам, а так же служит для создания ссылок в категории прайслиста, фотоальбомы и фотографии.<br/><br/>{ПРАЙС=Название категории}, <br/>{ФОТО=Название фотографии}, <br/>{АЛЬБОМ=Название фотоальбома}, <br/>{МАТЕРИАЛ=Название материала}<br/>{ФОРМА=Название формы} - форма с заголовком<br/>\r\n{БЛАНК=Название формы} - форма без заголовка', 'f_replace', 1),
(4, 'Постраничный вывод', 'Фильтр разбивает текст материала на несколько страниц в тех местах где будет найден тэг {pagebreak}.', 'f_pages', 1),
(2, 'Содержание статьи', 'Фильтр ищет тэги {СТРАНИЦА=Название страницы} в текстах статей, и заменяет их на главы в содержании статьи.', 'f_contents', 1),
(5, 'Внешние скрипты', 'Фильтр находит в текстах статей и модулей выражения "{ФАЙЛ=script.php}" и заменяет их результатами выполнения соответствующих скриптов из папки "/includes/myphp/".', 'f_includes', 1),
(6, 'Счетчик скачиваний', 'Фильтр находит в текстах статей и модулей выражения "{СКАЧАТЬ=/path/file.zip}" и заменяет их ссылкой для загрузки указанного файла, снабженной счетчиком скачиваний.', 'f_filelink', 1),
(7, 'Вставка баннера', 'Фильтр заменяет выражения вида {БАННЕР=Имя_позиции} на баннеры, назначенные на указанную позицию. Работает в статьях и модулях.', 'f_banners', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_filter_rules`
--

DROP TABLE IF EXISTS `cms_filter_rules`;
CREATE TABLE `cms_filter_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `find` varchar(200) NOT NULL,
  `replace` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_filter_rules`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_forms`
--

DROP TABLE IF EXISTS `cms_forms`;
CREATE TABLE `cms_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `sendto` varchar(4) NOT NULL DEFAULT 'mail',
  `user_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forms`
--

INSERT INTO `cms_forms` (`id`, `title`, `description`, `email`, `sendto`, `user_id`) VALUES
(1, 'Написать письмо', '<p>Используйте эту форму для обратной связи!</p>', 'forms@cms.ru', 'user', 1),
(3, 'Анкета пользователя', '', '', 'mail', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_form_fields`
--

DROP TABLE IF EXISTS `cms_form_fields`;
CREATE TABLE `cms_form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `ordering` int(11) NOT NULL,
  `kind` varchar(30) NOT NULL,
  `mustbe` int(11) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_form_fields`
--

INSERT INTO `cms_form_fields` (`id`, `form_id`, `title`, `ordering`, `kind`, `mustbe`, `config`) VALUES
(8, 1, 'Ваше имя:', 1, 'text', 1, 'a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:7:"default";s:0:"";}'),
(19, 1, 'Подписаться на новости?', 5, 'checkbox', 0, 'a:1:{s:7:"checked";s:1:"0";}'),
(10, 1, 'Текст сообщения:', 2, 'textarea', 1, 'a:4:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:4:"rows";s:1:"5";s:7:"default";s:0:"";}'),
(11, 1, 'Откуда вы о нас узнали?', 4, 'list', 0, 'a:1:{s:5:"items";s:48:"Из прессы/От знакомых/По телевизору/Из интернета";}'),
(15, 1, 'Компания:', 3, 'text', 0, 'a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:7:"default";s:0:"";}'),
(22, 3, 'Любимая музыка:', 1, 'text', 0, 'a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"50";s:7:"default";s:0:"";}'),
(24, 3, 'Образование:', 2, 'list', 0, 'a:1:{s:5:"items";s:24:"Высшее/Среднее/Начальное";}');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_forums`
--

DROP TABLE IF EXISTS `cms_forums`;
CREATE TABLE `cms_forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `auth_group` int(11) NOT NULL DEFAULT '-1',
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `parent_id` int(11) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSDiffer` varchar(15) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forums`
--

INSERT INTO `cms_forums` (`id`, `category_id`, `title`, `description`, `auth_group`, `ordering`, `published`, `parent_id`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`) VALUES
(1000, 0, '-- Корень форумов --', '', 0, 1, 0, 0, 1, 10, '', 0, 1),
(1, 1, 'Общий форум', '', 0, 1, 1, 1000, 2, 7, '', 0, 2),
(1002, 1, 'Подфорум1', '', 0, 2, 1, 1, 5, 6, '', 0, 3),
(1004, 1, 'Подфорум2', '', 0, 1, 1, 1, 3, 4, '', 0, 3),
(1005, 1, 'админский форум', '', 2, 2, 1, 1000, 8, 9, '', 0, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_forum_cats`
--

DROP TABLE IF EXISTS `cms_forum_cats`;
CREATE TABLE `cms_forum_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `auth_group` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forum_cats`
--

INSERT INTO `cms_forum_cats` (`id`, `title`, `published`, `auth_group`, `ordering`) VALUES
(1, 'Общие вопросы', 1, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_forum_files`
--

DROP TABLE IF EXISTS `cms_forum_files`;
CREATE TABLE `cms_forum_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `filesize` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forum_files`
--

INSERT INTO `cms_forum_files` (`id`, `post_id`, `filename`, `filesize`, `hits`, `pubdate`) VALUES
(1, 43, 'hh3_6e777.txt', 2097, 1, '2010-11-07 17:26:09');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_forum_images`
--

DROP TABLE IF EXISTS `cms_forum_images`;
CREATE TABLE `cms_forum_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `fileurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forum_images`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_forum_polls`
--

DROP TABLE IF EXISTS `cms_forum_polls`;
CREATE TABLE `cms_forum_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `answers` text NOT NULL,
  `options` varchar(250) NOT NULL,
  `enddate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forum_polls`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_forum_posts`
--

DROP TABLE IF EXISTS `cms_forum_posts`;
CREATE TABLE `cms_forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `editdate` datetime NOT NULL,
  `edittimes` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forum_posts`
--

INSERT INTO `cms_forum_posts` (`id`, `thread_id`, `user_id`, `pubdate`, `editdate`, `edittimes`, `content`) VALUES
(29, 12, 1, '2009-04-04 18:54:53', '2010-10-07 18:07:14', 2, 'Геосинклиналь [b]обогащает магматический монтмориллонит[/b], что в общем свидетельствует о преобладании тектонических опусканий в это время. Углефикация характерна. Порода существенна. Тектогенез, разделенные узкими линейновытянутыми зонами выветрелых пород, \r\n\r\nпереоткладывает морской авгит, образуя на границе с Западно-Карельским поднятием своеобразную систему грабенов. Ведущий экзогенный геологический процесс - субдукция ослабляет лакколит, так как совершенно однозначно указывает на существование и рост в период оформления палеогеновой поверхности выравнивания.'),
(37, 12, 27, '2009-10-16 12:31:31', '2009-10-16 12:34:05', 1, 'dthdj **** sdgjsfdgj'),
(38, 12, 27, '2009-10-16 12:31:36', '2009-10-16 12:33:56', 1, 'zdgjzdfgjdtj rtuhsrtjhst zfgjfgjfgj!!!!!!!!!!!'),
(39, 13, 1, '2010-10-18 17:15:22', '2010-10-18 17:15:22', 0, 'test'),
(40, 14, 1, '2010-10-19 12:36:05', '2010-10-19 12:36:05', 0, 'админская тема'),
(41, 13, 1, '2010-11-07 16:56:46', '2010-11-07 16:56:46', 0, 'qweqqweqweqwe'),
(42, 13, 8, '2010-11-07 16:57:23', '2010-11-07 16:57:23', 0, 'Согласен со всем что ты тут наговорил  :joke:'),
(43, 13, 1, '2010-11-07 17:23:57', '2010-11-07 17:23:57', 0, 'Новое сообщение');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_forum_threads`
--

DROP TABLE IF EXISTS `cms_forum_threads`;
CREATE TABLE `cms_forum_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `hits` int(11) NOT NULL,
  `closed` int(11) NOT NULL,
  `pinned` int(11) NOT NULL,
  `is_hidden` int(11) NOT NULL DEFAULT '0',
  `rel_to` varchar(15) NOT NULL,
  `rel_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forum_threads`
--

INSERT INTO `cms_forum_threads` (`id`, `forum_id`, `user_id`, `title`, `description`, `icon`, `pubdate`, `hits`, `closed`, `pinned`, `is_hidden`, `rel_to`, `rel_id`) VALUES
(12, 1, 1, 'Пример темы', '', '', '2009-10-16 12:31:36', 67, 0, 0, 0, '', 0),
(13, 1, 1, 'Тестовая тема форума', '', '', '2010-11-07 17:23:57', 54, 0, 0, 0, '', 0),
(14, 1005, 1, 'админская тема', '', '', '2010-10-19 12:36:05', 5, 0, 0, 1, '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_forum_votes`
--

DROP TABLE IF EXISTS `cms_forum_votes`;
CREATE TABLE `cms_forum_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_forum_votes`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_hits`
--

DROP TABLE IF EXISTS `cms_hits`;
CREATE TABLE `cms_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pubdate` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  `item_id` int(11) NOT NULL,
  `target` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_hits`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_menu`
--

DROP TABLE IF EXISTS `cms_menu`;
CREATE TABLE `cms_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `link` varchar(200) NOT NULL,
  `linktype` varchar(12) NOT NULL DEFAULT 'link',
  `linkid` varchar(25) DEFAULT '-1',
  `target` varchar(8) NOT NULL DEFAULT '_self',
  `component` varchar(100) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '1',
  `published` int(11) NOT NULL,
  `template` varchar(30) NOT NULL,
  `allow_group` int(11) NOT NULL DEFAULT '-1',
  `iconurl` varchar(100) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `NSDiffer` varchar(40) DEFAULT NULL,
  `NSIgnore` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_menu`
--

INSERT INTO `cms_menu` (`id`, `menu`, `title`, `link`, `linktype`, `linkid`, `target`, `component`, `ordering`, `published`, `template`, `allow_group`, `iconurl`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `parent_id`) VALUES
(1, 'root', '-- Главная страница --', '-1', 'link', '-1', '_self', '', 1, 0, '0', -1, '', 1, 36, 0, '', 0, 0),
(10, 'mainmenu', 'Новости', '/novosti', 'category', '2', '_self', '', 1, 1, '0', -1, '01.gif', 2, 3, 1, '', 0, 1),
(13, 'mainmenu', 'FAQ', '/faq', 'component', 'faq', '_self', '', 6, 1, '0', -1, '27.gif', 22, 23, 1, '', 0, 1),
(15, 'mainmenu', 'Люди', '/users', 'component', 'users', '_self', '', 10, 1, '0', -1, 'group.gif', 30, 31, 1, '', 0, 1),
(17, 'mainmenu', 'Блоги', '/blogs', 'component', 'blogs', '_self', '', 4, 1, '0', -1, 'blog.gif', 18, 19, 1, '', 0, 1),
(18, 'mainmenu', 'Форум', '/forum', 'component', 'forum', '_self', '', 11, 1, '0', -1, '29.gif', 32, 33, 1, '', 0, 1),
(20, 'mainmenu', 'Фото', '/photos', 'component', 'photos', '_self', '', 3, 1, '0', -1, '20.gif', 12, 17, 1, '', 0, 1),
(21, 'mainmenu', 'Статьи', '/stati', 'category', '6', '_self', '', 2, 1, '0', -1, '22.gif', 4, 11, 1, '', 0, 1),
(23, 'mainmenu', 'Каталог', '/catalog', 'component', 'catalog', '_self', '', 7, 1, '0', -1, 'objects048.gif', 24, 25, 1, '', 0, 1),
(25, 'mainmenu', 'Поиск по сайту', '/search', 'component', 'search', '_self', '', 9, 0, '0', -1, 'objects018.gif', 28, 29, 1, '', 0, 1),
(27, 'mainmenu', 'Админка', '/admin/', 'link', '/admin/', '_blank', '', 12, 0, '0', 2, 'options.gif', 34, 35, 1, '', 0, 1),
(37, 'mainmenu', 'Объявления', '/board', 'component', 'board', '_self', '', 8, 1, '0', -1, 'objects038.gif', 26, 27, 1, '', 0, 1),
(38, 'mainmenu', 'Клубы', '/clubs', 'component', 'clubs', '_self', '', 5, 1, '0', -1, '45.gif', 20, 21, 1, '', 0, 1),
(39, 'mainmenu', 'Астрономия', '/stati/astronomija', 'category', '11', '_self', '', 1, 1, '0', -1, '', 5, 6, 2, '', 0, 21),
(40, 'mainmenu', 'Геология', '/stati/geologija', 'category', '12', '_self', '', 2, 1, '0', -1, '', 7, 8, 2, '', 0, 21),
(41, 'mainmenu', 'Психология', '/stati/psihologija', 'category', '14', '_self', '', 3, 1, '0', -1, '', 9, 10, 2, '', 0, 21),
(42, 'mainmenu', 'Новые фото', '/photos/latest.html', 'link', '/photos/latest.html', '_self', '', 4, 1, '0', -1, '', 13, 14, 2, '', 0, 20),
(43, 'mainmenu', 'Лучшие фото', '/photos/top.html', 'link', '/photos/top.html', '_self', '', 5, 1, '0', -1, '', 15, 16, 2, '', 0, 20);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_modules`
--

DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE `cms_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `is_external` int(11) NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `showtitle` int(11) NOT NULL DEFAULT '1',
  `published` int(11) NOT NULL DEFAULT '1',
  `user` int(11) NOT NULL DEFAULT '1',
  `config` text NOT NULL,
  `original` int(11) NOT NULL,
  `css_prefix` varchar(30) NOT NULL,
  `allow_group` int(11) NOT NULL DEFAULT '-1',
  `cache` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL DEFAULT '1',
  `cacheint` varchar(15) NOT NULL DEFAULT 'HOUR',
  `template` varchar(35) NOT NULL DEFAULT 'module.tpl',
  `is_strict_bind` tinyint(4) NOT NULL DEFAULT '0',
  `version` varchar(6) NOT NULL DEFAULT '1.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_modules`
--

INSERT INTO `cms_modules` (`id`, `position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `allow_group`, `cache`, `cachetime`, `cacheint`, `template`, `is_strict_bind`, `version`) VALUES
(1, 'topmenu', 'Меню', 'Меню', 1, 'mod_menu', 6, 0, 1, 0, '---\nmenu: mainmenu\njtree: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module_simple.tpl', 0, '1.0'),
(17, 'top', 'Главная страница', 'Добро пожаловать!', 0, '<table cellspacing="0" cellpadding="0" border="0" width="100%">\r\n    <tbody>\r\n        <tr>\r\n            <td width="100" valign="top"><a target="_blank" href="http://www.instantcms.ru"><img border="0" alt="" src="/images/content/community.png" /></a></td>\r\n            <td>\r\n            <p class="moduletitle">Добро пожаловать!</p>\r\n            <p>Мы рады приветствовать Вас в нашей социальной сети. После регистрации Вам станут доступны все функции сайта.</p>\r\n            <p>Вы сможете завести блог, загружать фотографии и общаться с друзьями.</p>\r\n            <div>\r\n            <div>Чтобы изменить этот текст, <a href="/admin/index.php?view=modules&amp;do=edit&amp;id=17">отредактируйте модуль &quot;Главная страница&quot;</a>.</div>\r\n            </div>\r\n            </td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 0, 0, 1, 1, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(44, 'right', 'Облако тегов', 'Облако тегов', 1, 'mod_tags', 18, 0, 0, 0, '---\ncat_id: \nsortby: tag\nmenuid: \nminfreq: 0\nminlen: 3\ntargets: \n  content: content\n  photo: photo\n  blogpost: blog\n  catalog: catalog\n  userphoto: userphoto\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(46, 'bottom', 'Новинки каталога', 'Новинки каталога', 1, 'mod_uc_latest', 30, 1, 0, 0, '---\nnewscount: 6\nshowtype: list\nshowf: 2\ncat_id: \nsubs: 1\nfulllink: 0\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(26, 'right', 'Корзина покупателя', 'Корзина', 1, 'mod_cart', 19, 1, 0, 0, '---\nshowtype: list\nshowqty: 1\nmenuid: 23\nsource: catalog\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(25, 'sidebar', 'Голосования', 'Голосования', 1, 'mod_polls', 2, 1, 1, 0, '---\nshownum: 0\npoll_id: 2\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(27, 'right', 'Поиск', 'Поиск', 1, 'mod_search', 1, 0, 0, 0, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(19, 'maintop', 'Последние материалы', 'Новые статьи', 1, 'mod_latest', 2, 1, 1, 0, '---\nnewscount: 4\nshowdesc: 0\nshowdate: 1\nshowcom: 1\nshowrss: 1\ncat_id: 6\nsubs: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(20, 'topmenu', 'Авторизация', 'Авторизация', 1, 'mod_auth', 0, 1, 1, 0, '---\nautolog: 1\npassrem: 1\n', 1, '', -1, 0, 1, 'MINUTE', 'module.tpl', 0, '1.0'),
(22, 'topmenu', 'Последние регистрации', 'Новые пользователи', 1, 'mod_lastreg', 2, 1, 1, 0, '---\nnewscount: 5\nview_type: table\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(23, 'left', 'Случайное изображение', 'Случайная картинка', 1, 'mod_random_image', 21, 1, 0, 0, '---\nshowtitle: 1\nalbum_id: 0\nmenuid: 20\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(24, 'right', 'Часы', 'Часы', 1, 'mod_clock', 17, 1, 0, 0, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(37, 'right', 'Категории прайса', 'Категории прайса', 1, 'mod_pricecat', 14, 1, 0, 0, '---\nshowdesc: 0\nicon: /images/markers/pricelist.png\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(36, 'right', 'Разделы сайта', 'Статьи', 1, 'mod_category', 13, 1, 0, 0, '---\nshowdesc: 0\ncategory_id: 6\nicon: /images/markers/folder.png\nmenuid: 21\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(39, 'right', 'Выбор шаблона', 'Выбор шаблона', 1, 'mod_template', 12, 1, 0, 0, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(47, 'bottom', 'Популярное в каталоге', 'Популярное в каталоге', 1, 'mod_uc_popular', 23, 1, 0, 0, '---\nnum: 10\ncat_id: 0\nmenuid: 23\nshowf: 2\nshowtype: thumb\nfulllink: 1\nsort: rating\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(49, 'sidebar', 'Кто онлайн?', 'Кто онлайн?', 1, 'mod_whoonline', 24, 1, 1, 0, '---\nshow_today: 1\nadmin_editor: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(50, 'topmenu', 'Темы на форуме', 'Новости форума', 1, 'mod_forum', 31, 1, 1, 0, '---\nshownum: 2\nshowtype: web2\nshowforum: 0\nshowlink: 0\nshowtext: 0\nmenuid: 18\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(51, 'left', 'Случайное фото', 'Случайное фото', 1, 'mod_user_image', 25, 1, 0, 0, '---\nshowtitle: 1\nmenuid: 15\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(52, 'right', 'Внешний файл', 'Внешний файл', 0, '<p>{ФАЙЛ=test.php}</p>', 11, 1, 0, 1, '---\n', 0, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(56, 'sidebar', 'Архив статей', 'Архив новостей', 1, 'mod_arhive', 27, 1, 0, 0, '---\nsource: both\ncat_id: 6\n', 1, '', 1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(54, 'right', 'Случайное в каталоге', 'Случайное в каталоге', 1, 'mod_uc_random', 26, 1, 0, 0, '---\ncat_id: 1\ncount: 2\nshowtitle: 1\nshowcat: 0\nmenuid: 23\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(60, 'top', 'Лента RSS ', 'Лента новостей', 1, 'mod_rss', 9, 1, 0, 0, '---\nshowdesc: 0\nshowicon: 1\nitemslimit: 6\nrssurl: http://portal.novator.ru/ngnews.rss\ncols: 2\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(61, 'sidebar', 'Последние комментарии', 'Последние комментарии', 1, 'mod_comments', 4, 1, 1, 0, '---\nshownum: 10\nminrate: 0\nshowdesc: 1\nshowrss: 1\nshowtarg: 0\ntargets: \n  faq: faq\n  catalog: catalog\n  boarditem: boarditem\n  blog: blog\n  article: article\n  palbum: palbum\n  photo: photo\n  userphoto: userphoto\n', 1, '', -1, 0, 1, 'MINUTE', 'module.tpl', 0, '1.0'),
(62, 'top', 'Новинки фотогалереи', 'Новинки фотогалереи', 1, 'mod_latestphoto', 32, 1, 1, 0, '---\nshownum: 6\nmaxcols: 2\nshowclubs: 1\nshowalbum: 0\nshowdate: 0\nshowcom: 0\nalbum_id: 100\nshowtype: short\nshowmore: 0\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(63, 'right', 'Популярные фотографии', 'Популярные фотографии', 1, 'mod_bestphoto', 10, 1, 0, 0, '---\nshownum: 2\nmaxcols: 2\nshowalbum: 0\nshowdate: 1\nshowcom: 1\nalbum_id: 0\nsort: rating\nmenuid: 20\nshowtype: full\nshowmore: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(64, 'maintop', 'Новое в блогах', 'Новое в блогах', 1, 'mod_latestblogs', 2, 1, 1, 0, '---\nnamemode: blog\nshownum: 4\nminrate: 0\nshowrss: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(65, 'sidebar', 'Популярное в блогах', 'Популярное в блогах', 1, 'mod_bestblogs', 3, 1, 1, 0, '---\nnamemode: blog\nshownum: 10\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(66, 'header', 'Меню пользователя', 'Меню пользователя', 1, 'mod_usermenu', 1, 0, 1, 0, '---\navatar: 1\nshowtype: text\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(67, 'bottom', 'Последние вопросы FAQ', 'Последние вопросы FAQ', 1, 'mod_latest_faq', 33, 1, 0, 0, '---\nnewscount: 5\nmaxlen: 140\ncat_id: \n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(68, 'top', 'Горизонтальное меню', 'Горизонтальное меню', 1, 'mod_hmenu', 2, 0, 0, 0, '---\nmenu: mainmenu\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(69, 'top', 'Популярные статьи', 'Популярные статьи', 1, 'mod_bestcontent', 3, 1, 0, 0, '---\nshownum: 4\nmenuid: 21\nshowlink: 1\nshowdesc: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(70, 'sidebar', 'Поиск пользователей', 'Поиск пользователей', 1, 'mod_usersearch', 4, 1, 0, 0, '---\ncat_id: \nsource: \nmenuid: 15\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(71, 'top', 'Новые объявления', 'Новые объявления', 1, 'mod_latestboard', 1, 1, 1, 0, '---\nshownum: 10\nshowcity: 1\ncat_id: -1\nsubs: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(72, 'top', 'Рейтинг пользователей', 'Рейтинг пользователей', 1, 'mod_user_rating', 1, 1, 0, 0, '---\ncount: 20\nmenuid: 15\nview_type: rating\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(73, 'top', 'Новые клубы', 'Новые клубы', 1, 'mod_latestclubs', 3, 1, 0, 0, '---\ncount: 2\nmenuid: 38\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(74, 'top', 'Популярные клубы', 'Популярные клубы', 1, 'mod_bestclubs', 4, 1, 0, 0, '---\ncount: 2\nmenuid: 38\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(75, 'sidebar', 'Доска почета', 'Доска почета', 1, 'mod_respect', 1, 1, 1, 0, '---\nview: all\nshow_awards: 1\norder: desc\nlimit: 5\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(76, 'right', 'Файлы пользователей', 'Файлы пользователей', 1, 'mod_userfiles', 1, 1, 0, 0, '---\nmenuid: 0\nsw_stats: 1\nsw_latest: 1\nsw_popular: 1\nnum_latest: 5\nnum_popular: 5\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(87, 'maintop', 'Лента активности', 'Лента активности', 1, 'mod_actions', 1, 1, 1, 0, '---\nlimit: 15\nshow_target: 0\naction_types: \n  16: 16\n  15: 15\n  20: 20\n  13: 13\n  24: 24\n  23: 23\n  2: 2\n  12: 12\n  10: 10\n  25: 25\n  17: 17\n  8: 8\n  18: 18\n  7: 7\n  26: 26\n  19: 19\n  22: 22\n  11: 11\n  21: 21\n  9: 9\n  14: 14\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.7'),
(82, 'sidebar', 'Приветствие', 'Универсальный каталог', 0, '<p>С помощью компонента &laquo;Универсальный каталог&raquo;, в котором Вы сейчас находитесь, можно организовать хранение любых данных. От карандашей до автомобилей.</p>\r\n<p>Каждая рубрика каталога имеет собственный набор характеристик, который можно изменить в панели управления. Пользователи могут фильтровать записи каталога по характеристикам одним щелчком мыши.&nbsp;</p>\r\n<p>Любой пользователь может добавлять собственные записи в те рубрики каталога, для которых это разрешено в настройках.</p>', 14, 1, 1, 1, '', 1, '', -1, 0, 24, 'HOUR', 'module.tpl', 1, '1.0'),
(83, 'sidebar', 'Статистика пользователей', 'Статистика пользователей', 1, 'mod_user_stats', 1, 1, 1, 0, '---\nshow_total: 1\nshow_online: 1\nshow_gender: 1\nshow_city: 1\nshow_bday: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(84, 'left', 'Друзья онлайн', 'Друзья онлайн', 1, 'mod_user_friend', 5, 1, 0, 0, '---\r\nlimit: 5\r\nview_type: table', 1, '', -1, 0, 1, 'HOUR', 'module_simple.tpl', 0, '1.0'),
(85, 'sidebar', 'Пригласить друга', 'Пригласить друга', 1, 'mod_invite', 1, 1, 1, 0, '', 1, '', -1, 0, 1, 'HOUR', 'module.tpl', 1, '1.0');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_modules_bind`
--

DROP TABLE IF EXISTS `cms_modules_bind`;
CREATE TABLE `cms_modules_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `position` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_modules_bind`
--

INSERT INTO `cms_modules_bind` (`id`, `module_id`, `menu_id`, `position`) VALUES
(329, 42, 0, ''),
(249, 31, 0, ''),
(568, 17, 1, 'top'),
(347, 41, 18, ''),
(498, 39, 1, 'right'),
(417, 54, 24, 'right'),
(416, 54, 21, 'right'),
(264, 32, 5, ''),
(328, 2, 0, ''),
(263, 32, 8, ''),
(262, 32, 1, ''),
(596, 66, 0, 'header'),
(346, 41, 24, ''),
(588, 20, 1, 'sidebar'),
(412, 37, 22, 'right'),
(433, 36, 19, 'right'),
(381, 24, 1, 'right'),
(492, 26, 22, 'right'),
(314, 38, 8, ''),
(556, 1, 0, 'topmenu'),
(497, 27, 1, 'right'),
(332, 43, 0, ''),
(413, 45, 1, ''),
(478, 46, 1, 'bottom'),
(458, 44, 1, 'right'),
(491, 47, 1, 'bottom'),
(350, 48, 37, ''),
(595, 50, 1, 'maintop'),
(434, 51, 1, 'left'),
(358, 52, 1, 'right'),
(359, 52, 42, 'right'),
(360, 52, 41, 'right'),
(361, 52, 43, 'right'),
(364, 53, 1, ''),
(415, 54, 19, 'right'),
(422, 60, 1, 'top'),
(401, 55, 0, ''),
(609, 56, 0, 'sidebar'),
(403, 58, 63, ''),
(406, 59, 0, ''),
(435, 23, 1, 'left'),
(572, 61, 1, 'sidebar'),
(465, 63, 1, 'right'),
(587, 64, 1, 'maintop'),
(482, 67, 1, 'bottom'),
(496, 68, 0, 'top'),
(486, 69, 1, 'top'),
(594, 22, 16, 'sidebar'),
(493, 26, 23, 'right'),
(604, 70, 15, 'sidebar'),
(591, 71, 1, 'mainbottom'),
(515, 72, 1, 'top'),
(516, 73, 1, 'top'),
(520, 74, 1, 'top'),
(575, 25, 1, 'sidebar'),
(613, 75, 15, 'sidebar'),
(531, 76, 1, 'right'),
(541, 77, 1, ''),
(598, 19, 21, 'sidebar'),
(597, 19, 1, 'maintop'),
(582, 65, 17, 'sidebar'),
(592, 79, 1, 'maintop'),
(586, 62, 20, 'sidebar'),
(599, 82, 23, 'sidebar'),
(608, 49, 1, 'sidebar'),
(607, 83, 15, 'sidebar'),
(612, 75, 1, 'sidebar'),
(614, 85, 15, 'sidebar'),
(615, 87, 1, 'maintop');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_ns_transactions`
--

DROP TABLE IF EXISTS `cms_ns_transactions`;
CREATE TABLE `cms_ns_transactions` (
  `IDTransaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TableName` tinytext,
  `Differ` tinytext,
  `InTransaction` bit(1) DEFAULT NULL,
  `TStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`IDTransaction`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_ns_transactions`
--

INSERT INTO `cms_ns_transactions` (`IDTransaction`, `TableName`, `Differ`, `InTransaction`, `TStamp`) VALUES
(1, 'cms_menu', '', '\0', '2010-11-10 14:14:40');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_online`
--

DROP TABLE IF EXISTS `cms_online`;
CREATE TABLE `cms_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `sess_id` varchar(100) NOT NULL,
  `lastdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `agent` varchar(250) NOT NULL,
  `viewurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_online`
--

INSERT INTO `cms_online` (`id`, `ip`, `sess_id`, `lastdate`, `user_id`, `agent`, `viewurl`) VALUES
(717, '127.0.0.1', 'f7c991c6366de20dc666c9c1efedbb27', '2010-11-13 20:28:54', 1, '', '/users/wall-delete/user/4');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_photo_albums`
--

DROP TABLE IF EXISTS `cms_photo_albums`;
CREATE TABLE `cms_photo_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '1',
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSDiffer` varchar(15) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `published` int(11) NOT NULL,
  `showdate` int(11) NOT NULL DEFAULT '1',
  `iconurl` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `orderby` varchar(20) NOT NULL DEFAULT 'title',
  `orderto` varchar(4) NOT NULL DEFAULT 'asc',
  `public` int(11) NOT NULL,
  `perpage` int(11) NOT NULL DEFAULT '15',
  `cssprefix` varchar(20) NOT NULL,
  `thumb1` int(11) NOT NULL DEFAULT '96',
  `thumb2` int(11) NOT NULL DEFAULT '480',
  `thumbsqr` int(11) NOT NULL DEFAULT '1',
  `showtype` varchar(10) NOT NULL DEFAULT 'list',
  `nav` int(11) NOT NULL DEFAULT '1',
  `uplimit` int(11) NOT NULL,
  `maxcols` int(11) NOT NULL DEFAULT '4',
  `orderform` int(11) NOT NULL DEFAULT '1',
  `showtags` int(11) NOT NULL DEFAULT '1',
  `bbcode` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL DEFAULT '1',
  `is_comments` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_photo_albums`
--

INSERT INTO `cms_photo_albums` (`id`, `parent_id`, `ordering`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `title`, `description`, `published`, `showdate`, `iconurl`, `pubdate`, `orderby`, `orderto`, `public`, `perpage`, `cssprefix`, `thumb1`, `thumb2`, `thumbsqr`, `showtype`, `nav`, `uplimit`, `maxcols`, `orderform`, `showtags`, `bbcode`, `user_id`, `is_comments`) VALUES
(100, 0, 1, 1, 6, '', 0, 0, '-- Корневой альбом --', '', 1, 1, '', '2008-05-30 12:03:07', 'title', 'asc', 0, 15, '', 96, 480, 1, 'list', 1, 0, 4, 1, 1, 1, 1, 0),
(1, 100, 6, 2, 3, '', 0, 1, 'Общий альбом', 'Любой зарегистрированный пользователь может добавить свою фотографию в этот альбом.', 1, 1, '', '2008-04-24 10:18:21', 'pubdate', 'asc', 2, 15, '', 96, 600, 1, 'lightbox', 1, 10, 5, 1, 1, 1, 1, 1),
(1034, 100, 8, 4, 5, '', 0, 1, 'Природа', 'Разные красивые пейзажи', 1, 1, '', '2010-10-12 13:44:56', 'pubdate', 'desc', 2, 20, '', 96, 600, 1, 'lightbox', 1, 20, 4, 1, 1, 1, 1, 1),
(1035, 0, 1, 1, 2, 'club14', 0, 0, '-Корневой альбом клуба-', '', 0, 1, '', '0000-00-00 00:00:00', 'title', 'asc', 0, 15, '', 96, 480, 1, 'list', 1, 0, 4, 0, 1, 1, 14, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_photo_files`
--

DROP TABLE IF EXISTS `cms_photo_files`;
CREATE TABLE `cms_photo_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `pubdate` datetime DEFAULT '0000-00-00 00:00:00',
  `file` varchar(200) NOT NULL,
  `published` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `showdate` int(11) NOT NULL DEFAULT '1',
  `comments` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL DEFAULT '1',
  `owner` varchar(10) DEFAULT 'photos',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_photo_files`
--

INSERT INTO `cms_photo_files` (`id`, `album_id`, `title`, `description`, `pubdate`, `file`, `published`, `hits`, `showdate`, `comments`, `user_id`, `owner`) VALUES
(10, 1, 'Закат на пляже', '', '2009-08-31 18:26:43', 'd0633d5a84f03a27f1b7d0419947e968.jpg', 1, 25, 1, 1, 1, 'photos'),
(11, 1, 'Флорида', '', '2009-08-31 18:27:33', '5e7a09ffcaa383df24d25d56c315f0d0.jpg', 1, 28, 1, 1, 1, 'photos'),
(12, 1, 'Nissan Skyline', '', '2010-10-12 13:40:30', 'ee06697435a72940a4c370d0df1c021d.jpg', 1, 37, 1, 1, 1, 'photos'),
(13, 1, 'Каникулы на пляже', '', '2010-10-12 13:40:56', '2053b38244019c92ed5a1b34dfbdc22f.jpg', 1, 4, 1, 1, 1, 'photos'),
(15, 1034, 'Красивый цветок', '', '2010-10-12 13:47:32', '312604de74e4de8aec59626ac024c7d3.jpg', 1, 6, 1, 1, 1, 'photos'),
(16, 1034, 'Густые джунгли', '', '2010-10-12 13:47:52', 'e223946b3d76cc37417d0304c9cb23a1.jpg', 1, 40, 1, 1, 1, 'photos'),
(17, 1034, 'Вид на озеро', '<p>Красивый темный пейзаж с видом на озеро</p>', '2010-10-12 17:00:27', '38fde6623d0ad43c79c4d90a88a07009.jpg', 1, 25, 1, 1, 1, 'photos'),
(18, 1034, 'Винт и зажигалка', '', '2010-10-12 17:11:09', '4340db43cefea5ec6c123ed306c27b1a.jpg', 1, 15, 1, 1, 1, 'photos'),
(19, 1, 'Крепость на закате', '', '2010-10-14 00:44:33', 'ea1ce75db459f6ece6a49106a05aa9c0.jpg', 1, 5, 1, 1, 1, 'photos'),
(20, 1, 'Firefox Wallpaper', '', '2010-10-28 15:49:11', '0df82451e5d0deaa72a904b01a2879a1.jpg', 1, 1, 1, 1, 7, 'photos');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_plugins`
--

DROP TABLE IF EXISTS `cms_plugins`;
CREATE TABLE `cms_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `version` varchar(15) NOT NULL,
  `plugin_type` varchar(10) NOT NULL,
  `published` int(11) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_plugins`
--

INSERT INTO `cms_plugins` (`id`, `plugin`, `title`, `description`, `author`, `version`, `plugin_type`, `published`, `config`) VALUES
(6, 'p_usertab', 'Demo Profile Plugin', 'Пример плагина - Добавляет вкладку "Статьи" в профили всех пользователей', 'InstantCMS Team', '1.0', 'plugin', 0, '---\nКоличество статей: 10\n'),
(3, 'p_fckeditor', 'FCKEditor', 'Визуальный редактор', 'F. C. Knabben', '2.63', 'wysiwyg', 1, '---\n'),
(5, 'p_demo', 'Demo Plugin', 'Пример плагина - Добавляет текст в конец каждой статьи на сайте', 'InstantCMS Team', '1.0', 'plugin', 0, '---\ntext: Added By Plugin From Parameter\ncolor: blue\ncounter: 1\n'),
(8, 'p_ping', 'Пинг поисковых систем', 'Пингует Яндекс и Гугл при добавлении статей и постов в блоги', 'InstantCMS Team', '1.0', 'plugin', 1, '---\nYandex HOST: ping.blogs.yandex.ru\nYandex PATH: /RPC2\nGoogle HOST: blogsearch.google.com\nGoogle PATH: /ping/RPC2\n');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_polls`
--

DROP TABLE IF EXISTS `cms_polls`;
CREATE TABLE `cms_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `pubdate` date NOT NULL DEFAULT '0000-00-00',
  `answers` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_polls`
--

INSERT INTO `cms_polls` (`id`, `title`, `pubdate`, `answers`) VALUES
(2, 'Какой у вас хостинг?', '2008-05-23', 'a:3:{s:7:"Платный";i:0;s:10:"Бесплатный";i:1;s:11:"Собственный";i:0;}');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_polls_log`
--

DROP TABLE IF EXISTS `cms_polls_log`;
CREATE TABLE `cms_polls_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_polls_log`
--

INSERT INTO `cms_polls_log` (`id`, `poll_id`, `answer_id`, `user_id`, `ip`) VALUES
(1, 2, 0, 1, '127.0.0.1');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_price_cats`
--

DROP TABLE IF EXISTS `cms_price_cats`;
CREATE TABLE `cms_price_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_price_cats`
--

INSERT INTO `cms_price_cats` (`id`, `title`, `description`, `published`) VALUES
(4, 'Плакаты и агитационные  материалы', '', 1),
(5, 'Обучающие видеофильмы', '', 1),
(6, 'Бланки', '<p>бла бла бланки</p>', 1),
(9, 'Нормативно-техническая документация', '', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_price_items`
--

DROP TABLE IF EXISTS `cms_price_items`;
CREATE TABLE `cms_price_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(230) NOT NULL,
  `price` float NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `canmany` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_price_items`
--

INSERT INTO `cms_price_items` (`id`, `category_id`, `title`, `price`, `published`, `canmany`) VALUES
(688, 9, 'Учет тепловой энергии и теплоносителя. Энергосервис, Москва, 2004 год', 170, 1, 1),
(689, 9, 'Функциональные обязанности  по охране труда и технике безопасности.', 30, 1, 1),
(690, 9, 'ЦРБ – 288 Инструкция по безопасному ведению работ для стропальщиков, обслуживающие грузоподъемные краны  (машины). Спб. 2003 г.', 60, 1, 1),
(691, 9, 'Экологический контроль. Сборник нормативных документов. Екатеринбург, 2007 г.', 186, 1, 1),
(692, 9, 'Эксплуатация газовых хозяйств предприятий. Пособие для персонала газовых хозяйств промышленных предприятий и ТЭС. С..Петербург, 2006 год.', 380, 1, 1),
(693, 9, 'Экстренная  психологическая  помощь. Карманный вариант', 65, 1, 1),
(694, 6, 'Акт «О расследовании группового несчастного случая(тяжелого несчастного случая, несчастного случая со смертельным исходом)»', 10, 1, 1),
(695, 6, 'Акт формы Н.1', 10, 1, 1),
(713, 5, 'Охрана труда для работников торговли и общественного питания (20 мин) DVD-диск', 350, 1, 1),
(714, 5, 'Охрана труда при эксплуатации электроустановок (20 мин) DVD-диск', 350, 1, 1),
(720, 4, '«Безопасная работа на газосварочном оборудовании» . 1 лист', 187, 1, 1),
(721, 4, '«Безопасная эксплуатация газораспределительных пунктов». Комплект из 4 листов.', 486, 1, 1),
(722, 4, '«Безопасная эксплуатация паровых котлов» . Комплект из 5 листов.', 597, 1, 1),
(723, 4, '«Безопасность грузоподъемных работ» . Комплект', 596, 1, 1),
(724, 4, '«Безопасность  работ с электропогрузчиками » .Комплект из 2 листов.', 282, 1, 1),
(891, 6, 'крутая "штука" пывпывп', 123, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_ratings`
--

DROP TABLE IF EXISTS `cms_ratings`;
CREATE TABLE `cms_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `target` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_ratings`
--

INSERT INTO `cms_ratings` (`id`, `item_id`, `points`, `ip`, `target`, `user_id`, `pubdate`) VALUES
(1, 10, 1, '127.0.0.1', 'photo', 27, '2009-10-15 14:47:32'),
(2, 38, 1, '127.0.0.1', 'blogpost', 1, '2009-10-21 12:52:32'),
(3, 45, 1, '127.0.0.1', 'blogpost', 22, '2010-10-07 17:58:56'),
(4, 10, 1, '127.0.0.1', 'photo', 1, '2010-10-12 11:56:23'),
(5, 12, 1, '127.0.0.1', 'photo', 22, '2010-10-12 16:57:29'),
(6, 37, 1, '127.0.0.1', 'content', 1, '2010-10-14 23:30:26'),
(7, 20, 1, '127.0.0.1', 'photo', 2, '2010-10-19 21:45:21'),
(8, 11, 1, '127.0.0.1', 'comment', 2, '2010-11-07 19:02:28');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_ratings_total`
--

DROP TABLE IF EXISTS `cms_ratings_total`;
CREATE TABLE `cms_ratings_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(32) NOT NULL,
  `item_id` mediumint(9) NOT NULL,
  `total_rating` int(11) NOT NULL,
  `total_votes` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target` (`target`,`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_ratings_total`
--

INSERT INTO `cms_ratings_total` (`id`, `target`, `item_id`, `total_rating`, `total_votes`) VALUES
(1, 'blogpost', 38, 1, 1),
(2, 'photo', 10, 2, 2),
(3, 'blogpost', 45, 1, 1),
(4, 'photo', 12, 1, 1),
(5, 'content', 37, 1, 1),
(6, 'photo', 20, 1, 1),
(7, 'comment', 11, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_rating_targets`
--

DROP TABLE IF EXISTS `cms_rating_targets`;
CREATE TABLE `cms_rating_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(32) NOT NULL,
  `component` varchar(32) NOT NULL,
  `is_user_affect` tinyint(4) NOT NULL,
  `user_weight` smallint(6) NOT NULL,
  `target_table` varchar(32) NOT NULL,
  `target_title` varchar(70) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target` (`target`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_rating_targets`
--

INSERT INTO `cms_rating_targets` (`id`, `target`, `component`, `is_user_affect`, `user_weight`, `target_table`, `target_title`) VALUES
(1, 'content', 'content', 1, 5, 'cms_content', 'Статья'),
(2, 'photo', 'photos', 1, 5, 'cms_photo_files', 'Фото в галерее'),
(3, 'blogpost', 'blogs', 1, 5, 'cms_blog_posts', 'Пост в блоге'),
(4, 'comment', 'comments', 1, 2, 'cms_comments', 'Комментарий');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_search`
--

DROP TABLE IF EXISTS `cms_search`;
CREATE TABLE `cms_search` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `title` varchar(250) NOT NULL,
  `link` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `placelink` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_search`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_stats`
--

DROP TABLE IF EXISTS `cms_stats`;
CREATE TABLE `cms_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `logdate` datetime NOT NULL,
  `page` varchar(200) NOT NULL,
  `agent` varchar(60) DEFAULT 'unknown',
  `refer` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_stats`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_subscribe`
--

DROP TABLE IF EXISTS `cms_subscribe`;
CREATE TABLE `cms_subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `target` varchar(20) NOT NULL,
  `target_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_subscribe`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_tags`
--

DROP TABLE IF EXISTS `cms_tags`;
CREATE TABLE `cms_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(250) NOT NULL,
  `target` varchar(25) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_tags`
--

INSERT INTO `cms_tags` (`id`, `tag`, `target`, `item_id`) VALUES
(255, 'пример', 'photo', 11),
(257, 'пример', 'photo', 10),
(256, 'фото', 'photo', 10),
(254, 'фото', 'photo', 11),
(78, 'карандаши', 'catalog', 1),
(77, 'пастель', 'catalog', 1),
(85, 'краски', 'catalog', 2),
(84, 'набор', 'catalog', 2),
(259, 'фото', 'photo', 18),
(258, 'пример', 'photo', 18),
(26, 'авто', 'catalog', 5),
(27, 'минивэн', 'catalog', 5),
(28, 'тойота', 'catalog', 5),
(31, 'авто', 'catalog', 7),
(32, 'минивэн', 'catalog', 7),
(241, 'блог', 'blogpost', 5),
(240, 'пример', 'blogpost', 5),
(273, 'геология', 'content', 31),
(272, 'пример', 'content', 31),
(271, 'статья', 'content', 31),
(279, 'геология', 'content', 32),
(278, 'пример', 'content', 32),
(277, 'статья', 'content', 32),
(282, 'маркетинг', 'content', 33),
(281, 'пример', 'content', 33),
(280, 'статья', 'content', 33),
(226, 'маркетинг', 'content', 34),
(225, 'пример', 'content', 34),
(224, 'статья', 'content', 34),
(171, 'психология', 'content', 35),
(170, 'статья', 'content', 35),
(253, 'пример', 'content', 36),
(252, 'психология', 'content', 36),
(172, 'пример', 'content', 35),
(251, 'статья', 'content', 36),
(260, 'пример', 'photo', 19),
(261, 'фото', 'photo', 19);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_uc_cart`
--

DROP TABLE IF EXISTS `cms_uc_cart`;
CREATE TABLE `cms_uc_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `itemscount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_uc_cart`
--

INSERT INTO `cms_uc_cart` (`id`, `user_id`, `session_id`, `item_id`, `pubdate`, `itemscount`) VALUES
(1, 0, '493f362a7b14ae7932cf9b4b0c4d5092', 2, '2010-10-19 23:42:10', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_uc_cats`
--

DROP TABLE IF EXISTS `cms_uc_cats`;
CREATE TABLE `cms_uc_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `fieldsstruct` text,
  `view_type` varchar(20) NOT NULL DEFAULT 'list',
  `fields_show` int(11) NOT NULL DEFAULT '10',
  `showmore` int(11) NOT NULL DEFAULT '1',
  `perpage` int(11) NOT NULL DEFAULT '20',
  `showtags` int(11) NOT NULL DEFAULT '1',
  `showsort` int(11) NOT NULL DEFAULT '1',
  `is_ratings` int(11) NOT NULL,
  `orderby` varchar(12) NOT NULL DEFAULT 'pubdate',
  `orderto` varchar(10) DEFAULT 'desc',
  `showabc` int(11) NOT NULL DEFAULT '1',
  `shownew` int(11) NOT NULL,
  `newint` varchar(100) NOT NULL,
  `filters` int(11) NOT NULL,
  `is_shop` int(11) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `NSDiffer` int(11) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `is_public` int(11) NOT NULL,
  `can_edit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_uc_cats`
--

INSERT INTO `cms_uc_cats` (`id`, `parent_id`, `title`, `description`, `published`, `fieldsstruct`, `view_type`, `fields_show`, `showmore`, `perpage`, `showtags`, `showsort`, `is_ratings`, `orderby`, `orderto`, `showabc`, `shownew`, `newint`, `filters`, `is_shop`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `is_public`, `can_edit`) VALUES
(1000, 0, '-- Корневая рубрика --', '', 1, '', 'list', 10, 1, 20, 1, 1, 0, 'pubdate', 'desc', 1, 0, '', 0, 0, 1, 6, 0, 0, 0, 1, 0, 0),
(2, 1000, 'Автомобили', '', 1, 'a:4:{i:0;s:15:"Тип кузова/~m~/";i:1;s:15:"Объем двигателя";i:2;s:16:"Год выпуска/~m~/";i:3;s:13:"Описание/~h~/";}', 'list', 2, 1, 20, 1, 1, 0, 'pubdate', 'desc', 1, 1, '2 DAY', 0, 0, 2, 3, 1, 0, 0, 22, 0, 0),
(1, 1000, 'Канцелярские принадлежности', '', 1, 'a:6:{i:0;s:12:"Артикул/~m~/";i:1;s:10:"Цвета/~m~/";i:2;s:18:"Минимальная партия";i:3;s:13:"Описание/~h~/";i:4;s:11:"Размер/~m~/";i:5;s:12:"Скачать/~l~/";}', 'shop', 4, 0, 11, 0, 0, 0, 'hits', 'desc', 0, 0, '123 HOUR', 0, 0, 4, 5, 1, 0, 0, 23, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_uc_cats_access`
--

DROP TABLE IF EXISTS `cms_uc_cats_access`;
CREATE TABLE `cms_uc_cats_access` (
  `cat_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_uc_cats_access`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_uc_discount`
--

DROP TABLE IF EXISTS `cms_uc_discount`;
CREATE TABLE `cms_uc_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `sign` tinyint(4) NOT NULL,
  `value` float NOT NULL,
  `unit` varchar(10) NOT NULL,
  `if_limit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_uc_discount`
--

INSERT INTO `cms_uc_discount` (`id`, `title`, `cat_id`, `sign`, `value`, `unit`, `if_limit`) VALUES
(2, 'Почтовые расходы', 0, 2, 200, 'руб.', 0),
(3, 'Скидка на канц.товары', 1, -1, 15, '%', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_uc_items`
--

DROP TABLE IF EXISTS `cms_uc_items`;
CREATE TABLE `cms_uc_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `imageurl` varchar(200) NOT NULL,
  `fieldsdata` text NOT NULL,
  `hits` int(11) NOT NULL,
  `is_comments` int(11) NOT NULL,
  `tags` varchar(200) NOT NULL,
  `rating` float NOT NULL,
  `meta_desc` varchar(250) NOT NULL,
  `meta_keys` varchar(250) NOT NULL,
  `price` float NOT NULL,
  `canmany` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `on_moderate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`fieldsdata`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_uc_items`
--

INSERT INTO `cms_uc_items` (`id`, `category_id`, `title`, `pubdate`, `published`, `imageurl`, `fieldsdata`, `hits`, `is_comments`, `tags`, `rating`, `meta_desc`, `meta_keys`, `price`, `canmany`, `user_id`, `on_moderate`) VALUES
(1, 1, 'Карандаши', '2008-06-03 13:38:55', 1, 'b00117f6bca1efaaef37b44da87c1100.jpg', 'a:4:{i:0;s:7:"3130070";i:1;s:7:"бежевый";i:2;s:7:"191 шт.";i:3;s:64:"Набор для рисования: 12 цветных карандашей в картонной коробочке";}', 37, 1, 'пастель, карандаши', 0, '', '', 14.6, 1, 0, 0),
(2, 1, 'Набор для рисования', '2008-06-03 13:40:37', 1, 'b21ddffd1e9fe4716f5d1496c4e74400.jpg', 'a:5:{i:0;s:10:"3170050PK2";i:1;s:34:"бежевый, красный, синий, оранжевый";i:2;s:6:"84 шт.";i:3;s:65:"8 восковых карандашей, 8 красок и кисточка, в пластиковом футляре";i:4;s:19:"11.00, 12.55, 13.02";}', 41, 1, 'набор, краски', 0, '', '', 24, 1, 0, 0),
(5, 2, 'Toyota Estima', '2008-06-03 13:47:00', 1, '96bd390df9222bdc684ceec8afc94ec3.jpg', 'a:4:{i:0;s:7:"минивэн";i:1;s:9:"2,4 литра";i:2;s:4:"2000";i:3;s:1056:"<p>Второе поколение Toyota Estima, появившееся на рынке в 2000 году, лидирует среди минивэнов. Предшествующее поколение модели отличалось от нынешнего традиционным передним приводом. В комплектацию Estima входят две вариации двигателей: новый 4-цилиндровый рядный двигатель объемом 2.4 литра с системой газораспределения DOHC и 3-литровый 6-цилиндровый V-образный двигатель с той же системой газораспределения. Estima оснащается полным приводом с функцией активного контроля.</p>\r\n<p>Оригинальность экстерьера данного автомобиля проявляется в характерных жестких линиях кузова и раскосых передних фарах. Ширина и высота автомобиля стали меньше по сравнению с предыдущим поколением, но зато колесная база увеличилась до 40 мм.</p>\r\n<p>Две двери Estima снабжены механизмом &laquo;easy closer&raquo;, задние двери отрываются, скользя вдоль корпуса. В передней пассажирской двери установлении механизм автоматического открытия и закрытия. Второе поколение Estima с первым роднит возможность разместить восемь человек в салоне по модели &laquo;2-3-3&raquo;.</p>";}', 8, 1, 'авто, минивэн, тойота', 0, '', '', 0, 1, 0, 0),
(6, 2, 'Mitsubishi Eterna!', '2008-06-03 10:54:00', 1, '7afbfacf9a4c4a9d64e0da2b31b880e5.jpg', 'a:4:{i:0;s:5:"седан";i:1;s:9:"1.8 литра";i:2;s:4:"1992";i:3;s:0:"";}', 11, 1, '', 0, 'Описание', 'Ключевые слова', 0, 1, 0, 0),
(7, 2, 'Subaru Domingo', '2008-06-03 13:51:24', 1, 'db0297daef1de808feed34a75b5ea49b.jpg', 'a:4:{i:0;s:7:"минивэн";i:1;s:9:"1.2 литра";i:2;s:4:"1991";i:3;s:0:"";}', 32, 1, 'авто, минивэн', 0, '', '', 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_uc_ratings`
--

DROP TABLE IF EXISTS `cms_uc_ratings`;
CREATE TABLE `cms_uc_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_uc_ratings`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_uc_tags`
--

DROP TABLE IF EXISTS `cms_uc_tags`;
CREATE TABLE `cms_uc_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(200) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_uc_tags`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_upload_images`
--

DROP TABLE IF EXISTS `cms_upload_images`;
CREATE TABLE `cms_upload_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `fileurl` varchar(250) NOT NULL,
  `target` varchar(25) NOT NULL DEFAULT 'forum',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_upload_images`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_users`
--

DROP TABLE IF EXISTS `cms_users`;
CREATE TABLE `cms_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `login` varchar(100) NOT NULL,
  `nickname` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `icq` varchar(15) NOT NULL,
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `logdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `is_locked` int(11) NOT NULL,
  `is_deleted` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_date` datetime NOT NULL,
  `invited_by` int(11) DEFAULT NULL,
  `invdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `cms_users`
--

INSERT INTO `cms_users` (`id`, `group_id`, `login`, `nickname`, `password`, `email`, `icq`, `regdate`, `logdate`, `birthdate`, `is_locked`, `is_deleted`, `rating`, `points`, `last_ip`, `status`, `status_date`, `invited_by`, `invdate`) VALUES
(1, 2, 'admin', 'Администратор', '2ca41752ccf4dbdb76d8fe88c488fd44', 'admin@cms.ru', '100200300', '2007-11-23 12:41:57', '2010-11-13 20:24:31', '1980-10-23', 0, 0, 32, 0, '127.0.0.1', 'Самый длинный статус из всех что существуют в этом прекрасном мире', '2010-10-21 02:06:53', 0, '2010-11-09 23:25:59'),
(2, 1, 'vasya', 'Василий', '2ca41752ccf4dbdb76d8fe88c488fd44', 'vasya@cms.ru', '100200300', '2008-07-16 16:31:48', '2010-11-11 23:37:01', '1980-01-01', 0, 0, 5, 0, '127.0.0.1', 'Привет Всем!!! Я пришел!!!', '2010-10-19 21:43:33', 0, '2010-11-02 13:50:04'),
(3, 1, 'fedor', 'Федор', '2ca41752ccf4dbdb76d8fe88c488fd44', 'fedor@cms.com', '100334564', '2010-10-20 17:33:42', '2010-11-11 23:37:17', '1979-10-20', 0, 0, 0, 0, '127.0.0.1', 'We are all made of stars (c) Moby', '2010-10-28 15:44:45', NULL, NULL),
(4, 1, 'dude', 'dude', '2ca41752ccf4dbdb76d8fe88c488fd44', 'dude@cms.com', '', '2010-10-26 16:04:50', '2010-10-26 16:07:58', '1980-01-01', 0, 1, 0, 0, '127.0.0.1', '', '0000-00-00 00:00:00', NULL, NULL),
(5, 1, 'tester', 'tester', '2ca41752ccf4dbdb76d8fe88c488fd44', 'tester@cms.com', '', '2010-10-26 16:05:14', '2010-10-26 16:08:07', '1980-01-01', 0, 1, 0, 0, '127.0.0.1', '', '0000-00-00 00:00:00', NULL, NULL),
(6, 1, 'ivanov', 'Иван Иванов', '2ca41752ccf4dbdb76d8fe88c488fd44', 'ivan-kozlov@ya.ru', '', '2010-10-26 16:09:14', '2010-11-07 16:09:15', '1980-01-01', 0, 1, 0, 0, '127.0.0.1', '', '0000-00-00 00:00:00', NULL, NULL),
(7, 7, 'petrov', 'Петр Сергеевич', '2ca41752ccf4dbdb76d8fe88c488fd44', 'petrov@cms.ru', '', '2010-10-26 16:09:40', '2010-11-11 23:36:05', '1980-01-01', 0, 1, 0, 0, '127.0.0.1', '', '0000-00-00 00:00:00', NULL, NULL),
(8, 7, 'sidorov', 'Сидор Сидоров', '2ca41752ccf4dbdb76d8fe88c488fd44', 'sidorov@cms.com', '', '2010-10-26 16:19:52', '2010-11-11 23:37:27', '1980-01-01', 0, 1, 0, 0, '127.0.0.1', '', '0000-00-00 00:00:00', NULL, NULL),
(9, 1, 'invited', 'Приглашенный Человек', '2ca41752ccf4dbdb76d8fe88c488fd44', 'inv@cms.com', '', '2010-11-02 15:40:52', '2010-11-02 16:15:03', '1980-01-01', 0, 1, 0, 0, '127.0.0.1', '', '0000-00-00 00:00:00', 1, '2010-11-02 15:40:52'),
(10, 1, 'messager', 'Посланник', '2ca41752ccf4dbdb76d8fe88c488fd44', 'mess@test.com', '', '2010-11-09 23:27:15', '2010-11-09 23:43:13', '1980-01-01', 0, 1, 0, 0, '127.0.0.1', '', '0000-00-00 00:00:00', 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_activate`
--

DROP TABLE IF EXISTS `cms_users_activate`;
CREATE TABLE `cms_users_activate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pubdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_users_activate`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_albums`
--

DROP TABLE IF EXISTS `cms_user_albums`;
CREATE TABLE `cms_user_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `allow_who` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `allow_who` (`allow_who`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_albums`
--

INSERT INTO `cms_user_albums` (`id`, `user_id`, `title`, `pubdate`, `allow_who`) VALUES
(2, 3, 'Мой фотоальбом', '2010-10-22 20:28:51', 'all');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_autoawards`
--

DROP TABLE IF EXISTS `cms_user_autoawards`;
CREATE TABLE `cms_user_autoawards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `imageurl` varchar(200) NOT NULL,
  `p_comment` int(11) NOT NULL,
  `p_blog` int(11) NOT NULL,
  `p_forum` int(11) NOT NULL,
  `p_photo` int(11) NOT NULL,
  `p_privphoto` int(11) DEFAULT NULL,
  `p_content` int(11) NOT NULL,
  `p_karma` int(11) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_autoawards`
--

INSERT INTO `cms_user_autoawards` (`id`, `title`, `description`, `imageurl`, `p_comment`, `p_blog`, `p_forum`, `p_photo`, `p_privphoto`, `p_content`, `p_karma`, `published`) VALUES
(1, 'Медаль', 'Почетный член форума', 'aw.gif', 0, 0, 100, 0, 0, 0, 0, 1),
(2, 'Грамота', 'Почетный комментатор сайта', 'aw4.gif', 100, 5, 50, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_awards`
--

DROP TABLE IF EXISTS `cms_user_awards`;
CREATE TABLE `cms_user_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `imageurl` varchar(100) NOT NULL,
  `from_id` int(11) NOT NULL,
  `award_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_awards`
--

INSERT INTO `cms_user_awards` (`id`, `user_id`, `pubdate`, `title`, `description`, `imageurl`, `from_id`, `award_id`) VALUES
(1, 2, '2010-10-27 21:46:44', 'Медаль за заслуги', 'В благодарность от администрации', 'aw.gif', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_clubs`
--

DROP TABLE IF EXISTS `cms_user_clubs`;
CREATE TABLE `cms_user_clubs` (
  `user_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'guest',
  `pubdate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_clubs`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_files`
--

DROP TABLE IF EXISTS `cms_user_files`;
CREATE TABLE `cms_user_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `filename` varchar(250) NOT NULL,
  `pubdate` datetime NOT NULL,
  `allow_who` varchar(50) NOT NULL,
  `filesize` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_files`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_friends`
--

DROP TABLE IF EXISTS `cms_user_friends`;
CREATE TABLE `cms_user_friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `logdate` datetime NOT NULL,
  `is_accepted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_friends`
--

INSERT INTO `cms_user_friends` (`id`, `to_id`, `from_id`, `logdate`, `is_accepted`) VALUES
(1, 2, 1, '2010-10-08 17:53:22', 1),
(2, 3, 2, '2010-10-21 01:22:27', 1),
(3, 3, 1, '2010-10-21 01:25:15', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_groups`
--

DROP TABLE IF EXISTS `cms_user_groups`;
CREATE TABLE `cms_user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `is_admin` int(11) NOT NULL,
  `access` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_groups`
--

INSERT INTO `cms_user_groups` (`id`, `title`, `alias`, `is_admin`, `access`) VALUES
(1, 'Пользователи', 'registered', 0, 'comments/add, comments/delete, content/add, content/autoadd, board/autoadd'),
(2, 'Администраторы', 'admin', 1, 'admin/content, admin/com_rssfeed, admin/com_arhive, admin/com_banners, admin/com_blog, admin/com_faq, admin/com_board, admin/com_content, admin/com_clubs, admin/com_comments, admin/com_forms, admin/com_photos'),
(8, 'Гости', 'guest', 0, 'comments/add'),
(7, 'Редакторы', 'editors', 0, 'comments/add, comments/delete, content/add, content/autoadd, content/delete'),
(9, 'Модераторы', 'moderators', 0, 'comments/add, comments/delete, comments/moderate, forum/moderate, content/add');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_invites`
--

DROP TABLE IF EXISTS `cms_user_invites`;
CREATE TABLE `cms_user_invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `createdate` datetime NOT NULL,
  `is_used` tinyint(4) NOT NULL DEFAULT '0',
  `is_sended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`,`owner_id`,`is_used`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_invites`
--

INSERT INTO `cms_user_invites` (`id`, `code`, `owner_id`, `createdate`, `is_used`, `is_sended`) VALUES
(9, 'a23d89c6ba5ed7ecb1d70267cb1a6b03', 1, '2010-11-02 15:04:30', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_karma`
--

DROP TABLE IF EXISTS `cms_user_karma`;
CREATE TABLE `cms_user_karma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `points` smallint(6) NOT NULL,
  `senddate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_karma`
--

INSERT INTO `cms_user_karma` (`id`, `user_id`, `sender_id`, `points`, `senddate`) VALUES
(1, 1, 27, 1, '2009-11-10 09:49:35'),
(2, 22, 1, 1, '2010-08-12 13:14:35'),
(3, 2, 1, -1, '2010-10-20 15:30:26'),
(4, 1, 2, 1, '2010-10-21 00:25:29'),
(5, 1, 2, 1, '2010-10-24 16:23:56'),
(6, 2, 1, 1, '2010-11-13 20:29:11');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_msg`
--

DROP TABLE IF EXISTS `cms_user_msg`;
CREATE TABLE `cms_user_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `senddate` datetime NOT NULL,
  `is_new` int(11) NOT NULL DEFAULT '1',
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_msg`
--

INSERT INTO `cms_user_msg` (`id`, `to_id`, `from_id`, `senddate`, `is_new`, `message`) VALUES
(1, 22, 1, '2010-06-17 12:34:21', 0, 'hello'),
(5, 22, 1, '2010-10-18 17:58:46', 0, '&amp;amp;amp;amp;amp;amp;lt;b&amp;amp;amp;amp;amp;amp;gt;hello friend!&amp;amp;amp;amp;amp;amp;lt;/b&amp;amp;amp;amp;amp;amp;gt;'),
(6, 3, -1, '2010-10-21 01:22:27', 0, '&amp;amp;amp;amp;amp;lt;b&amp;amp;amp;amp;amp;gt;Получено предложение дружбы&amp;amp;amp;amp;amp;lt;/b&amp;amp;amp;amp;amp;gt;. Вы можете посмотреть его в своем &amp;amp;amp;amp;amp;lt;a href="http://icms/users/fedor"&amp;amp;amp;amp;amp;gt;профиле&amp;amp;amp;amp;amp;lt;/a&amp;amp;amp;amp;amp;gt;.'),
(7, 3, -1, '2010-10-21 01:25:15', 0, '&amp;amp;amp;amp;amp;lt;b&amp;amp;amp;amp;amp;gt;Получено предложение дружбы&amp;amp;amp;amp;amp;lt;/b&amp;amp;amp;amp;amp;gt;. Вы можете посмотреть его в своем &amp;amp;amp;amp;amp;lt;a href="http://icms/users/fedor"&amp;amp;amp;amp;amp;gt;профиле&amp;amp;amp;amp;amp;lt;/a&amp;amp;amp;amp;amp;gt;.'),
(8, 5, -1, '2010-10-26 16:07:30', 0, '&lt;b&gt;Получено предложение дружбы&lt;/b&gt;. Вы можете посмотреть его в своем &lt;a href="http://icms/users/tester"&gt;профиле&lt;/a&gt;.'),
(9, 4, -1, '2010-10-26 16:07:39', 1, '&lt;b&gt;Получено предложение дружбы&lt;/b&gt;. Вы можете посмотреть его в своем &lt;a href="http://icms/users/dude"&gt;профиле&lt;/a&gt;.'),
(10, 7, -1, '2010-10-26 16:18:23', 0, '&lt;b&gt;Получено предложение дружбы&lt;/b&gt;. Вы можете посмотреть его в своем &lt;a href="http://icms/users/petrov"&gt;профиле&lt;/a&gt;.'),
(11, 6, -1, '2010-10-26 16:18:28', 0, '&lt;b&gt;Получено предложение дружбы&lt;/b&gt;. Вы можете посмотреть его в своем &lt;a href="http://icms/users/ivanov"&gt;профиле&lt;/a&gt;.'),
(12, 1, -1, '2010-10-26 16:20:14', 0, '&lt;b&gt;Получено предложение дружбы&lt;/b&gt;. Вы можете посмотреть его в своем &lt;a href="http://icms/users/admin"&gt;профиле&lt;/a&gt;.'),
(13, 8, -1, '2010-10-26 16:28:46', 0, '&lt;b&gt;Получено предложение дружбы&lt;/b&gt;. Вы можете посмотреть его в своем &lt;a href="http://icms/users/sidorov"&gt;профиле&lt;/a&gt;.'),
(14, 2, -1, '2010-10-27 21:46:44', 0, '<b>Получена награда:</b> <a href="http://icms/users/vasya">Медаль за заслуги</a>'),
(15, 7, -1, '2010-10-28 15:45:03', 0, '<b>Получено предложение дружбы</b>. Вы можете посмотреть его в своем <a href="http://icms/users/petrov">профиле</a>.'),
(16, 10, -2, '2010-11-09 23:27:15', 0, '<h2>Привет!</h2>\n<p><span style="font-size: medium;">Мы очень <span style="color: rgb(51, 153, 102);">рады</span> что ты зарегистрировался!</span></p>'),
(17, 10, -1, '2010-11-09 23:40:03', 0, '<b>Получено предложение дружбы</b>. Вы можете посмотреть его в своем <a href="http://icms/users/messager">профиле</a>.'),
(18, 1, 2, '2010-11-11 22:43:16', 0, 'hello'),
(19, 2, 1, '2010-11-11 23:23:59', 0, 'привет юзеры'),
(20, 3, 1, '2010-11-11 23:23:59', 0, 'привет юзеры'),
(21, 4, 1, '2010-11-11 23:23:59', 1, 'привет юзеры'),
(22, 5, 1, '2010-11-11 23:23:59', 1, 'привет юзеры'),
(23, 6, 1, '2010-11-11 23:23:59', 1, 'привет юзеры'),
(24, 9, 1, '2010-11-11 23:23:59', 1, 'привет юзеры'),
(25, 10, 1, '2010-11-11 23:23:59', 1, 'привет юзеры'),
(26, 7, -2, '2010-11-11 23:30:46', 0, 'привет редакторам'),
(27, 8, -2, '2010-11-11 23:30:46', 0, 'привет редакторам'),
(28, 1, -2, '2010-11-11 23:32:51', 0, 'привет всем!'),
(29, 2, -2, '2010-11-11 23:32:51', 0, 'привет всем!'),
(30, 3, -2, '2010-11-11 23:32:51', 0, 'привет всем!'),
(31, 4, -2, '2010-11-11 23:32:51', 1, 'привет всем!'),
(32, 5, -2, '2010-11-11 23:32:51', 1, 'привет всем!'),
(33, 6, -2, '2010-11-11 23:32:51', 1, 'привет всем!'),
(34, 7, -2, '2010-11-11 23:32:51', 0, 'привет всем!'),
(35, 8, -2, '2010-11-11 23:32:51', 0, 'привет всем!'),
(36, 9, -2, '2010-11-11 23:32:51', 1, 'привет всем!'),
(37, 10, -2, '2010-11-11 23:32:51', 1, 'привет всем!'),
(38, 7, 1, '2010-11-11 23:33:38', 0, 'привет Петр Сергеевич'),
(39, 3, 2, '2010-11-11 23:36:58', 0, 'Привет Федя!');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_photos`
--

DROP TABLE IF EXISTS `cms_user_photos`;
CREATE TABLE `cms_user_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `pubdate` date NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `allow_who` varchar(15) NOT NULL DEFAULT 'all',
  `hits` int(11) NOT NULL,
  `imageurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `album_id` (`album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_photos`
--


-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_profiles`
--

DROP TABLE IF EXISTS `cms_user_profiles`;
CREATE TABLE `cms_user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `city` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `showmail` int(11) NOT NULL,
  `showbirth` int(11) NOT NULL DEFAULT '1',
  `showicq` int(11) NOT NULL DEFAULT '1',
  `karma` int(11) NOT NULL,
  `imageurl` varchar(250) NOT NULL,
  `allow_who` varchar(35) NOT NULL DEFAULT 'all',
  `signature` varchar(240) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `formsdata` text NOT NULL,
  `email_newmsg` int(11) NOT NULL DEFAULT '1',
  `cm_subscribe` varchar(4) NOT NULL,
  `stats` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `cms_user_profiles`
--

INSERT INTO `cms_user_profiles` (`id`, `user_id`, `city`, `description`, `showmail`, `showbirth`, `showicq`, `karma`, `imageurl`, `allow_who`, `signature`, `gender`, `formsdata`, `email_newmsg`, `cm_subscribe`, `stats`) VALUES
(1, 1, 'Москва', 'Р.Хайнлайн, А.Азимов, А.Кларк', 1, 1, 1, 3, 'f2c062723ffc98357eada0a1fe346046.jpg', 'all', '', 'm', '---\n22: Р.Хайнлайн, А.Азимов, А.Кларк\n24: Высшее\n', 1, 'none', '---\ncount: \n  comments: 3\n  forum: 5\n  photos: 0\n  board: 2\n  files_public: 0\n  files_private: 0\nrating: 30\n'),
(2, 2, 'Москва', 'живопись, &#8217;музыка&#8217;, всякая "всячина" однако', 0, 0, 1, 0, '165e5d6b2786dc6d0a538146de38b480.jpg', 'all', '', 'm', '---\n22: |\n  &#8217;музыка&#8217;, всякая &quot;всячина&quot; однако\n24: Высшее\n', 1, '0', '---\ncount: \n  comments: 1\n  forum: 0\n  photos: 0\n  board: 1\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(3, 3, '', '', 0, 0, 1, 0, 'a946f7701b178eedbbdae7a57ba7e0be.jpg', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(4, 4, '', '', 0, 0, 1, 0, '', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(5, 5, '', '', 0, 0, 1, 0, '', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(6, 6, '', '', 0, 0, 1, 0, '', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(7, 7, '', '', 0, 0, 1, 0, '', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 5\n'),
(8, 8, '', '', 0, 0, 1, 0, '', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 1\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(9, 9, '', '', 0, 0, 1, 0, '', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(10, 10, '', '', 0, 0, 1, 0, '', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_user_wall`
--

DROP TABLE IF EXISTS `cms_user_wall`;
CREATE TABLE `cms_user_wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `content` text NOT NULL,
  `usertype` varchar(8) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `cms_user_wall`
--

INSERT INTO `cms_user_wall` (`id`, `user_id`, `author_id`, `pubdate`, `content`, `usertype`) VALUES
(6, 1, 1, '2010-10-22 20:52:56', 'спасибо что заглянули в мой профиль', 'user'),
(7, 3, 2, '2010-10-28 16:12:16', 'так рад тебя здесь видеть!', 'user'),
(8, 1, 1, '2010-11-09 17:23:50', 'Самый длинный статус из всех', 'user'),
(9, 2, 1, '2010-11-09 17:24:05', 'Самый длинный статус из всех', 'user');