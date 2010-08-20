DROP TABLE IF EXISTS `installations`;

CREATE TABLE `installations` (                                                                                           
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `secret` varchar(255) NOT NULL,
  `public_token` varchar(255) NOT NULL,
  `network` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);
