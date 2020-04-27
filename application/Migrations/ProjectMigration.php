<?php


namespace Mini\Migrations;


use Mini\Core\Model;

class ProjectMigration extends Model implements Migration
{

    function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `".DB_NAME."`.`projects` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `project_id` int(11) NOT NULL UNiQUE,
          `name` text COLLATE utf8_unicode_ci,
          `link` text COLLATE utf8_unicode_ci,
          `skills` varchar(255) COLLATE utf8_unicode_ci,
          `budget` int(24) COLLATE utf8_unicode_ci ,
          `currency` varchar(255) COLLATE utf8_unicode_ci,
          `user_name` varchar(255) COLLATE utf8_unicode_ci,
          `user_login` text COLLATE utf8_unicode_ci,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id` (`id`),
          UNIQUE KEY (`project_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $query = $this->db->prepare($sql);
        $query->execute();
    }
}
