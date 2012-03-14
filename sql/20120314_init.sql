CREATE TABLE `flickr_cache` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`search_term` varchar(255) DEFAULT NULL,
`search_time` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)
);

CREATE TABLE `flickr_image` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`flickr_cache_id` int(11) NOT NULL,
`api_key` varchar(255) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`url` varchar(255) DEFAULT NULL,
`page` int default 1,
`per_page` int default 0,
PRIMARY KEY (`id`),
CONSTRAINT `FK_flickr_image_flickr_cache_id` FOREIGN KEY (`flickr_cache_id`) REFERENCES `flickr_cache` (`id`)
);
