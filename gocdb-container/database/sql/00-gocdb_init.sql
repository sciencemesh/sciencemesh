-- gocdb_init.sql -- Initializes the GOCDB database with necessary startup database
CREATE DATABASE gocdb DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_bin;

-- User creation
CREATE USER 'gocdbuser'@'%' IDENTIFIED BY 'srgh85tz7sdgz2';
GRANT ALL PRIVILEGES ON gocdb.* TO 'gocdbuser'@'%';
FLUSH PRIVILEGES;
