

CREATE TABLE `points` (
  `id`        int(6) NOT NULL AUTO_INCREMENT,
  `datatime`  varchar(20) NOT NULL,
  `lat`       varchar(20) NOT NULL,
  `lon`       varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `points` (`id`, `datatime`, `lat`, `lon`) VALUES
(1, '2014-06-01 01:12:26', '52.6609',     '-2.4823416' ),
(2, '2015-06-01 01:12:26', '52.3478033',  '-2.9738599' ),
(3, '2016-06-01 01:12:26', '52.3127466',  '-3.1178516' ),
(4, '2017-06-01 01:12:26', '52.24931',    '-3.3797816' ),
(5, '2018-06-01 01:12:26', '52.162925',   '-3.5629366' );


ALTER TABLE `points`
  ADD PRIMARY KEY (`id`);