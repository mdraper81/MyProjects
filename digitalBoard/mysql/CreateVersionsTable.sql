-- Creating versions table to support upgrading the database from one version to another
DROP TABLE IF EXISTS Versions;
CREATE TABLE Versions (
    version INT NOT NULL,
    ts DATETIME DEFAULT NOW(),
    PRIMARY KEY (version)
) ENGINE=INNODB;
