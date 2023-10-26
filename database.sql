DROP DATABASE IF EXISTS APICHADO;

CREATE DATABASE APICHADO;
USE APICHADO;

CREATE TABLE 'joboffer' (
    'id' INT NOT NULL AUTO_INCREMENT,
    'title' VARCHAR(255) NULL,
    'description' TEXT NULL,
    'city' VARCHAR(255) NULL,
    'salaryMin' INT NULL,
    'salaryMax' INT NULL,
    'created_at' DATETIME NULL,
    'updated_at' DATETIME NULL
);

INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, created_at, updated_at) VALUES ('Développeur Web', 'Développeur Web', 'Paris', 30000, 40000, '2019-01-01', '2019-01-01');
