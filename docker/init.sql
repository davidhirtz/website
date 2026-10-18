-- Runs once, when the database volume is created: the site's database and the one the tests use.
CREATE DATABASE IF NOT EXISTS `website` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `website_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
