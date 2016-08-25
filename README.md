# CrossRoad API
http://przytrzask.usermd.net/

### Technologies used:
- PHP & MYSQL for backend
- HAML/HTML + SASS/CSS + Aangular/JS for frontend

##### Croosroad folder includes single crossroad lights 
- light API 
- Data stored in mysql database
- simplyfied API	(REpresentationalStateTransfer).
- Access to	API	is secured by unique key in http header.
- API use GET to read a state in JSON format
- API use POST to set the appropriate state.

##### Master-crossroad folder includes Control system 
- Data is stored in MySql database
- PHP CLI updates light phases
- sends to crossroad	request using cURL

### Setting Up
### Crossroad Database
```
use `crossroad`;

DROP TABLE IF EXISTS `lights`;
CREATE TABLE `lights`(
  `id` INT(10) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `state` ENUM ('shutdown','suspended','green','red','yellow','red_yellow') DEFAULT  'suspended',
  PRIMARY KEY (`id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

INSERT INTO `lights` (`id`,`state`) VALUES
  (1,'suspended'),
  (2,'shutdown'),
  (3,'green'),
  (4,'red');
```

### Master Crossroad Database
```
use `mastercrossroad`;


DROP TABLE IF EXISTS `current_crossroads_state`;
DROP TABLE IF EXISTS `light_phases`;
DROP TABLE IF EXISTS `phases`;
DROP TABLE IF EXISTS `crossroad_lights`;
DROP TABLE IF EXISTS `cycles`;
DROP TABLE IF EXISTS `crossroads`;

CREATE TABLE `crossroads` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `auth_key` VARCHAR(32) NOT NULL,
  `host` VARCHAR(50) NOT NULL,
  `active` ENUM('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `crossroad_lights` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `crossroads_id` INT(10) UNSIGNED NOT NULL,
  `external_id` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`crossroads_id`),
  CONSTRAINT `crossroad_lights_ibfk_1` FOREIGN KEY (`crossroads_id`) REFERENCES `crossroads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cycles` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `crossroads_id` INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `active` ENUM('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY (`crossroads_id`),
  KEY (`active`),
  CONSTRAINT `cycles_ibfk_1` FOREIGN KEY (`crossroads_id`) REFERENCES `crossroads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `phases` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cycles_id` INT(10) UNSIGNED NOT NULL,
  `interval` INT(5) UNSIGNED NOT NULL,
  `priority` TINYINT UNSIGNED NOT NULL,
  `description` TEXT,
  PRIMARY KEY (`id`),
  KEY (`cycles_id`),
  KEY (`priority`),
  CONSTRAINT `phases_ibfk_1` FOREIGN KEY (`cycles_id`) REFERENCES `cycles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `light_phases` (
  `phases_id` INT(10) UNSIGNED NOT NULL,
  `crossroad_lights_id` INT(10) UNSIGNED NOT NULL,
  `state` ENUM ('shutdown','suspended','green','red','yellow','red_yellow') NOT NULL DEFAULT 'suspended',
  PRIMARY KEY `light_phases_id` (`phases_id`, `crossroad_lights_id`),
  CONSTRAINT `light_phases_ibfk_1` FOREIGN KEY (`crossroad_lights_id`) REFERENCES `crossroad_lights` (`id`) ON DELETE CASCADE,
  CONSTRAINT `light_phases_ibfk_2` FOREIGN KEY (`phases_id`) REFERENCES `phases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `current_crossroads_state` (
  `crossroads_id` INT(10) UNSIGNED NOT NULL,
  `phases_id` INT(10) UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY `current_crossroads_state_id` (`crossroads_id`),
  CONSTRAINT `current_crossroads_state_ibfk_1` FOREIGN KEY (`crossroads_id`) REFERENCES `crossroads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `current_crossroads_state_ibfk_2` FOREIGN KEY (`phases_id`) REFERENCES `phases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `crossroads` (`id`, `name`, `auth_key`, `host`, `active`) VALUES
  (1, 'Grunwaldzka przy galerii', 'trzasq', 'http://crossroad.local/api/', 'yes');

INSERT INTO `crossroad_lights` (`id`, `crossroads_id`, `external_id`) VALUES
  (1, 1, '1'),
  (2, 1, '2'),
  (3, 1, '3'),
  (4, 1, '4')
;

INSERT INTO `cycles` (`id`, `crossroads_id`, `name`,`active`) VALUES
  (1, 1, 'cykl poranny', 'yes'),
  (2, 1, 'cykl popoludniowy','no')
;

INSERT INTO `phases` (`id`, `cycles_id`, `interval`, `priority`, `description`) VALUES
  (1, 1, 10000, 0, 'Horizontal red + Vertical green'),
  (2, 1, 2000, 1, 'Horizontal red_yellow + Vertical yellow'),
  (3, 1, 15000, 2, 'Horizontal green + Vertical red'),
  (4, 1, 2000, 3, 'Horizontal yellow + Vertical red_yellow'),
  (5, 2, 20000, 0, 'Horizontal red + Vertical green'),
  (6, 2, 2000, 1, 'Horizontal red_yellow + Vertical yellow'),
  (7, 2, 15000, 2, 'Horizontal green + Vertical red'),
  (8, 2, 2000, 3, 'Horizontal yellow + Vertical red_yellow')
;

INSERT INTO `light_phases` (`phases_id`, `crossroad_lights_id`, `state`) VALUES
  (1, 1, 'green'),
  (1, 3, 'green'),
  (1, 2, 'red'),
  (1, 4, 'red'),
  (2, 1, 'yellow'),
  (2, 3, 'yellow'),
  (2, 2, 'red_yellow'),
  (2, 4, 'red_yellow'),
  (3, 2, 'green'),
  (3, 4, 'green'),
  (3, 1, 'red'),
  (3, 3, 'red'),
  (4, 2, 'yellow'),
  (4, 4, 'yellow'),
  (4, 1, 'red_yellow'),
  (4, 3, 'red_yellow'),
  (5, 1, 'green'),
  (5, 3, 'green'),
  (5, 2, 'red'),
  (5, 4, 'red'),
  (6, 1, 'yellow'),
  (6, 3, 'yellow'),
  (6, 2, 'red_yellow'),
  (6, 4, 'red_yellow'),
  (7, 2, 'green'),
  (7, 4, 'green'),
  (7, 1, 'red'),
  (7, 3, 'red'),
  (8, 2, 'yellow'),
  (8, 4, 'yellow'),
  (8, 1, 'red_yellow'),
  (8, 3, 'red_yellow')
;

INSERT INTO `current_crossroads_state` (`crossroads_id`, `phases_id`, `created_at`)
VALUES
  (1, 1, '2016-08-15 19:06:20');

```
#### Launching the cycler
 You can use cron.
 On windows just use this simple script
  :point_down:

```
:loop
start /b pathto\php.exe pathTo\master-crossroad\bin\cycler.php
timeout /t 1
goto loop
```
#### License
do whatever You want :relaxed:
