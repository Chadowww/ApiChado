DROP DATABASE IF EXISTS APICHADO;

CREATE DATABASE APICHADO;
USE APICHADO;

CREATE TABLE user
(
    `id`       INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `email`    VARCHAR(255)                   NULL,
    `password` VARCHAR(255)                   NULL,
    `roles`     INT                            NULL,
    `is_verified` BOOLEAN DEFAULT FALSE       NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE candidate
(
    `id`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `firstname`   VARCHAR(50)                    NULL,
    `lastname`    VARCHAR(50)                    NULL,
    `phone`       VARCHAR(10)                    NULL,
    `address`     VARCHAR(255)                   NULL,
    `city`        VARCHAR(50)                    NULL,
    `country`     VARCHAR(50)                    NULL,
    `avatar`      VARCHAR(255)                   NULL,
    `slug`        VARCHAR(255)                   NULL,
    `coverLetter` TEXT                           NULL,
    `user_id`     INT                            NULL,
    CONSTRAINT `fk_candidate_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
);

CREATE TABLE company
(
    `id`      INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name`    VARCHAR(255)                   NULL,
    `phone`   VARCHAR(10)                    NULL,
    `address` VARCHAR(255)                   NULL,
    `city`    VARCHAR(50)                    NULL,
    `country` VARCHAR(50)                    NULL,
    `description` TEXT                       NULL,
    `siret`       VARCHAR(14)                NULL,
    `logo`        VARCHAR(255)               NULL,
    `slug`       VARCHAR(255)                NULL,
    `cover`       VARCHAR(255)               NULL,
    `user_id` INT                            NULL,
    CONSTRAINT `fk_company_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
);

CREATE TABLE socialeMedia(
    `id`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `linkedin`    VARCHAR(255)                   NULL,
    `github`      VARCHAR(255)                   NULL,
    `twitter`     VARCHAR(255)                   NULL,
    `facebook`    VARCHAR(255)                   NULL,
    `instagram`   VARCHAR(255)                   NULL,
    `website`     VARCHAR(255)                   NULL,
    `user_id` INT                            NULL,
    CONSTRAINT `fk_socialeMedia_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
);

CREATE TABLE contract
(
    `id`   INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `type` VARCHAR(255)                   NULL
);

CREATE TABLE `category`
(
    `id`   INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(255)                   NULL
);

CREATE TABLE `joboffer`
(
    `id`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `title`       VARCHAR(255)                   NULL,
    `description` TEXT                           NULL,
    `city`        VARCHAR(50)                    NULL,
    `salaryMin`   INT                            NULL,
    `salaryMax`   INT                            NULL,
    `contract_id` INT                            NULL,
    `company_id`  INT                            NULL,
    `category_id` INT                            NULL,
    CONSTRAINT `fk_joboffer_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_joboffer_contract_id` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_joboffer_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL

);

CREATE TABLE `application`
(
    `id`           INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `status`       VARCHAR(255)                   NULL,
    `candidate_id` INT                            NULL,
    `joboffer_id`  INT                            NULL,
    CONSTRAINT `fk_application_candidate_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidate` (`id`),
    CONSTRAINT `fk_application_joboffer_id` FOREIGN KEY (`joboffer_id`) REFERENCES `joboffer` (`id`) ON DELETE CASCADE
);

CREATE TABLE `technology`
(
    `id`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name`        VARCHAR(255)                   NULL,
    `category_id` INT                            NULL,
    CONSTRAINT `fk_technology_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
);

CREATE TABLE `favlist`
(
    `id`           INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `candidate_id` INT                            NULL,
    `joboffer_id`  INT                            NULL,
    CONSTRAINT `fk_favlist_candidate_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidate` (`id`),
    CONSTRAINT `fk_favlist_joboffer_id` FOREIGN KEY (`joboffer_id`) REFERENCES `joboffer` (`id`) ON DELETE CASCADE
);

CREATE TABLE `resume`(
                         `id`           INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                         `title`        VARCHAR(255)                   NULL,
                         `file`         varchar(255)                   NULL,
                         `candidate_id` INT                            NULL,
                         CONSTRAINT `fk_resume_candidate_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidate` (`id`)
);

CREATE TABLE `resume_technology`(
                                    `ìd`            INT PRIMARY KEY AUTO_INCREMENT NOT NULL ,
                                    `resume_id`     INT                            NULL,
                                    `technology_id` INT                            NULL
);

# Create index for fulltext search
CREATE FULLTEXT INDEX `idx_joboffer_title_description` ON `joboffer` (`title`, `description`);
CREATE FULLTEXT INDEX `idx_candidate_firstname_lastname_description` ON `candidate` (`firstname`, `lastname`);

# Create data user
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company1@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company2@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company3@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company4@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company5@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company6@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('candidate7@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('candidate8@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('candidate9@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('candidate10@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('candidate11@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('candidate12@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('admin@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 9, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company7@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company8@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company9@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, created_at, updated_at)
VALUES ('company10@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');

# Create data candidate
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, user_id)
VALUES ('Jean', 'Dupont', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis un candidat motivé et passionné par le développement web.', 7);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, user_id)
VALUES ('Marie', 'Martin', '0123456789', '123 rue de la République', 'Lyon', 'France',
         'Je suis une candidate motivée et passionnée par le développement web.', 8);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, user_id)
VALUES ('Pierre', 'Durand', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis un candidat motivé et passionné par le développement web.', 9);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, user_id)
VALUES ('Julie', 'Dufour', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis une candidate motivée et passionnée par le développement web.', 10);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, user_id)
VALUES ('Thomas', 'Leroy', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis un candidat motivé et passionné par le développement web.', 11);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, user_id)
VALUES ('Sophie', 'Moreau', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis une candidate motivée et passionnée par le développement web.', 12);


# Create data company
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('AS Turing', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'as-turing', '0123456789',1);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('Entreprise XYZ', '123 rue de la République', 'Lyon', 'France', '12345678901234', 'description',
        'Entreprise-XYZ', '0123456789', 2);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('Startup ABC', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Startup-ABC',
        '0123456789', 3);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('Entreprise 123', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Entreprise-123',
        '0123456789', 4);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('Startup 456', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Startup-456',
        '0123456789', 5);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('Startup 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Startup-789',
        '0123456789', 6);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('Company 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Company-789',
        '0123456789', 14);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('sas 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'sas-789',
        '0123456789', 15);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('entreprise 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'entreprise-789',
        '0123456789', 16);
INSERT INTO company (name, address, city, country, siret, description, slug, phone, user_id)
VALUES ('s2i 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 's2i-789',
        '0123456789', 17);

# Create data contract
INSERT INTO contract (type)
VALUES ('CDI');
INSERT INTO contract (type)
VALUES ('CDD');
INSERT INTO contract (type)
VALUES ('Stage');
INSERT INTO contract (type)
VALUES ('Alternance');
INSERT INTO contract (type)
VALUES ('Freelance');

# Create data category
INSERT INTO category (name)
VALUES ('Développement Web');
INSERT INTO category (name)
VALUES ('Développement Mobile');
INSERT INTO category (name)
VALUES ('Développement Logiciel');
INSERT INTO category (name)
VALUES ('Design');
INSERT INTO category (name)
VALUES ('Data Science');
INSERT INTO category (name)
VALUES ('Cybersécurité');
INSERT INTO category (name)
VALUES ('Gestion de Projet');
INSERT INTO category (name)
VALUES ('Intelligence Artificielle');
INSERT INTO category (name)
VALUES ('Développement Back-End');
INSERT INTO category (name)
VALUES ('Développement Front-End');
INSERT INTO category (name)
VALUES ('DevOps');
INSERT INTO category (name)
VALUES ('Autre');

# Create data joboffer
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Web',
        'Titre du poste : Développeur Web


Présentation de l''entreprise :
Vous travaillerez pour la société AS Turing dans le pole déveoppement
Description du poste :
En tant que développeur web au sein de notre entreprise, vous rejoindrez une équipe dynamique et passionnée qui se consacre à la création de solutions web exceptionnelles. Vous participerez au développement, à la maintenance et à l''amélioration de nos applications web, contribuant ainsi à la croissance de notre entreprise.

Responsabilités principales :

Concevoir, développer, tester et mettre en œuvre des applications web de haute qualité.
Collaborer avec les équipes interfonctionnelles pour comprendre les besoins et les exigences du projet.
Résoudre les problèmes techniques et optimiser les performances des applications existantes.
Suivre les meilleures pratiques en matière de développement web, y compris la sécurité et l''accessibilité.
Exigences :

Diplôme en informatique, génie logiciel ou dans un domaine connexe.
Solide expérience dans le développement web, y compris la maîtrise de langages comme HTML, CSS, JavaScript, et PHP (ou d''autres langages pertinents).
Connaissance des frameworks web, des systèmes de gestion de contenu (CMS) et des outils de développement.
Capacité à résoudre des problèmes de manière créative et à travailler de manière autonome ou en équipe.
Excellentes compétences en communication et en résolution de problèmes.
Avantages :

Environnement de travail stimulant et innovant.
Opportunités de formation continue pour rester à la pointe de la technologie.
Équipe dynamique et collaborative.
Possibilités d''évolution au sein de l''entreprise.',
        'Paris',
        30000,
        40000,
        1,
        1,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Front-End',
        'Présentation de l\'entreprise : Entreprise XYZ, leader dans le secteur de l\'e-commerce. Description du
           poste : Nous recherchons un développeur Front-End pour rejoindre notre équipe et contribuer au développement de notre site web.',
        'New York',
        45000,
        60000,
        2,
        2,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Full-Stack',
        'Présentation de l''entreprise : Startup innovante dans le domaine de la santé connectée. Description du poste : En tant que développeur Full-Stack, vous participerez à la création d''une application de suivi de la santé.',
        'San Francisco',
        55000,
        70000,
        3,
        3,
        6);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Ingénieur DevOps',
        'Présentation de l''entreprise : Entreprise de technologie spécialisée dans le cloud computing. Description du poste : Nous cherchons un ingénieur DevOps pour optimiser notre infrastructure cloud.',
        'Seattle',
        60000,
        80000,
        4,
        4,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Designer UX/UI',
        'Présentation de l''entreprise : Agence de design renommée. Description du poste : Nous recrutons un designer UX/UI pour créer des interfaces utilisateur intuitives et esthétiques.',
        'Londres',
        40000,
        55000,
        5,
        5,
        2);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Analyste de données',
        'Présentation de l''entreprise : Géant de la finance. Description du poste : Rejoignez notre équipe d''analystes de données pour analyser les tendances financières.',
        'Francfort',
        70000,
        90000,
        1,
        6,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Mobile (iOS)',
        'Présentation de l''entreprise : Startup en pleine croissance dans le domaine des applications mobiles. Description du poste : Vous serez responsable du développement d''applications iOS de qualité.',
        'Toronto',
        50000,
        65000,
        2,
        1,
        5);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Ingénieur en cybersécurité',
        'Présentation de l''entreprise : Leader de la sécurité informatique. Description du poste : Protégez nos systèmes contre les menaces en tant qu''ingénieur en cybersécurité.',
        'Singapour',
        65000,
        85000,
        3,
        2,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Chef de Projet Agile',
        'Présentation de l''entreprise : Entreprise de conseil en gestion. Description du poste : Dirigez des projets agiles et collaborez avec nos clients pour atteindre leurs objectifs.',
        'Paris',
        60000,
        80000,
        4,
        3,
        3);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Ingénieur en intelligence artificielle',
        'Présentation de l''entreprise : Société technologique axée sur l''IA. Description du poste : Vous travaillerez sur des projets d''intelligence artificielle passionnants.',
        'Pékin',
        70000,
        95000,
        5,
        4,
        2);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Web',
        'Titre du poste : Développeur Web


Présentation de l''entreprise :
Vous travaillerez pour la société AS Turing dans le pole déveoppement
Description du poste :
En tant que développeur web au sein de notre entreprise, vous rejoindrez une équipe dynamique et passionnée qui se consacre à la création de solutions web exceptionnelles. Vous participerez au développement, à la maintenance et à l''amélioration de nos applications web, contribuant ainsi à la croissance de notre entreprise.

Responsabilités principales :

Concevoir, développer, tester et mettre en œuvre des applications web de haute qualité.
Collaborer avec les équipes interfonctionnelles pour comprendre les besoins et les exigences du projet.
Résoudre les problèmes techniques et optimiser les performances des applications existantes.
Suivre les meilleures pratiques en matière de développement web, y compris la sécurité et l''accessibilité.
Exigences :

Diplôme en informatique, génie logiciel ou dans un domaine connexe.
Solide expérience dans le développement web, y compris la maîtrise de langages comme HTML, CSS, JavaScript, et PHP (ou d''autres langages pertinents).
Connaissance des frameworks web, des systèmes de gestion de contenu (CMS) et des outils de développement.
Capacité à résoudre des problèmes de manière créative et à travailler de manière autonome ou en équipe.
Excellentes compétences en communication et en résolution de problèmes.
Avantages :

Environnement de travail stimulant et innovant.
Opportunités de formation continue pour rester à la pointe de la technologie.
Équipe dynamique et collaborative.
Possibilités d''évolution au sein de l''entreprise.',
        'Paris',
        30000,
        40000,
        1,
        1,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Front-End',
        'Présentation de l\'entreprise : Entreprise XYZ, leader dans le secteur de l\'e-commerce. Description du
           poste : Nous recherchons un développeur Front-End pour rejoindre notre équipe et contribuer au développement de notre site web.',
        'New York',
        45000,
        60000,
        2,
        2,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Full-Stack',
        'Présentation de l''entreprise : Startup innovante dans le domaine de la santé connectée. Description du poste : En tant que développeur Full-Stack, vous participerez à la création d''une application de suivi de la santé.',
        'San Francisco',
        55000,
        70000,
        3,
        3,
        6);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Ingénieur DevOps',
        'Présentation de l''entreprise : Entreprise de technologie spécialisée dans le cloud computing. Description du poste : Nous cherchons un ingénieur DevOps pour optimiser notre infrastructure cloud.',
        'Seattle',
        60000,
        80000,
        4,
        4,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Designer UX/UI',
        'Présentation de l''entreprise : Agence de design renommée. Description du poste : Nous recrutons un designer UX/UI pour créer des interfaces utilisateur intuitives et esthétiques.',
        'Londres',
        40000,
        55000,
        5,
        5,
        2);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Analyste de données',
        'Présentation de l''entreprise : Géant de la finance. Description du poste : Rejoignez notre équipe d''analystes de données pour analyser les tendances financières.',
        'Francfort',
        70000,
        90000,
        1,
        6,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Développeur Mobile (iOS)',
        'Présentation de l''entreprise : Startup en pleine croissance dans le domaine des applications mobiles. Description du poste : Vous serez responsable du développement d''applications iOS de qualité.',
        'Toronto',
        50000,
        65000,
        2,
        1,
        5);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Ingénieur en cybersécurité',
        'Présentation de l''entreprise : Leader de la sécurité informatique. Description du poste : Protégez nos systèmes contre les menaces en tant qu''ingénieur en cybersécurité.',
        'Singapour',
        65000,
        85000,
        3,
        2,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Chef de Projet Agile',
        'Présentation de l''entreprise : Entreprise de conseil en gestion. Description du poste : Dirigez des projets agiles et collaborez avec nos clients pour atteindre leurs objectifs.',
        'Paris',
        60000,
        80000,
        4,
        3,
        3);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contract_id, company_id, category_id)
VALUES ('Ingénieur en intelligence artificielle',
        'Présentation de l''entreprise : Société technologique axée sur l''IA. Description du poste : Vous travaillerez sur des projets d''intelligence artificielle passionnants.',
        'Pékin',
        70000,
        95000,
        5,
        4,
        2);

# Create data application
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 1, 1);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 2, 1);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 3, 1);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 4, 1);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 5, 1);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 6, 1);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 1, 2);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 2, 2);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 3, 2);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 4, 2);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 5, 2);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 6, 2);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 1, 3);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 2, 3);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 3, 3);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 4, 3);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 5, 3);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 6, 3);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 1, 4);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 2, 4);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 3, 4);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 4, 4);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 5, 4);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 6, 4);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 1, 5);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 2, 5);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 3, 5);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 4, 5);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 5, 5);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 6, 5);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 1, 6);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 2, 6);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 3, 6);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 4, 6);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 5, 6);
INSERT INTO application (status, candidate_id, joboffer_id)
VALUES ('En attente', 6, 6);

# Create data technology
INSERT INTO technology (name, category_id)
VALUES ('HTML', 1);
INSERT INTO technology (name, category_id)
VALUES ('CSS', 1);
INSERT INTO technology (name, category_id)
VALUES ('JavaScript', 1);
INSERT INTO technology (name, category_id)
VALUES ('PHP', 1);
INSERT INTO technology (name, category_id)
VALUES ('Python', 1);
INSERT INTO technology (name, category_id)
VALUES ('Java', 1);
INSERT INTO technology (name, category_id)
VALUES ('C#', 1);
INSERT INTO technology (name, category_id)
VALUES ('C++', 1);
INSERT INTO technology (name, category_id)
VALUES ('Ruby', 1);
INSERT INTO technology (name, category_id)
VALUES ('SQL', 1);
INSERT INTO technology (name, category_id)
VALUES ('NoSQL', 1);
INSERT INTO technology (name, category_id)
VALUES ('MongoDB', 1);
INSERT INTO technology (name, category_id)
VALUES ('Node.js', 1);
INSERT INTO technology (name, category_id)
VALUES ('React', 1);
INSERT INTO technology (name, category_id)
VALUES ('Angular', 1);
INSERT INTO technology (name, category_id)
VALUES ('Vue.js', 1);
INSERT INTO technology (name, category_id)
VALUES ('Symfony', 1);
INSERT INTO technology (name, category_id)
VALUES ('Laravel', 1);
INSERT INTO technology (name, category_id)
VALUES ('Spring', 1);
INSERT INTO technology (name, category_id)
VALUES ('Django', 1);
INSERT INTO technology (name, category_id)
VALUES ('Flask', 1);
INSERT INTO technology (name, category_id)
VALUES ('Bootstrap', 1);
INSERT INTO technology (name, category_id)
VALUES ('jQuery', 1);
INSERT INTO technology (name, category_id)
VALUES ('WordPress', 1);
INSERT INTO technology (name, category_id)
VALUES ('Magento', 1);
INSERT INTO technology (name, category_id)
VALUES ('Shopify', 1);
INSERT INTO technology (name, category_id)
VALUES ('PrestaShop', 1);
INSERT INTO technology (name, category_id)
VALUES ('iOS', 2);
INSERT INTO technology (name, category_id)
VALUES ('Android', 2);
INSERT INTO technology (name, category_id)
VALUES ('Swift', 2);
INSERT INTO technology (name, category_id)
VALUES ('Kotlin', 2);
INSERT INTO technology (name, category_id)
VALUES ('Objective-C', 2);
INSERT INTO technology (name, category_id)
VALUES ('Java', 2);
INSERT INTO technology (name, category_id)
VALUES ('React Native', 2);
INSERT INTO technology (name, category_id)
VALUES ('Ionic', 2);
INSERT INTO technology (name, category_id)
VALUES ('Xamarin', 2);
INSERT INTO technology (name, category_id)
VALUES ('Flutter', 2);
INSERT INTO technology (name, category_id)
VALUES ('Cordova', 2);
INSERT INTO technology (name, category_id)
VALUES ('PhoneGap', 2);
INSERT INTO technology (name, category_id)
VALUES ('Ionic', 2);

# Create data favlist
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (1, 1);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (2, 2);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (4, 3);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (1, 4);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (6, 5);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (1, 6);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (2, 1);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (3, 2);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (2, 3);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (4, 4);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (2, 5);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (5, 6);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (6, 1);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (3, 2);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (3, 3);
INSERT INTO favlist (candidate_id, joboffer_id)
VALUES (3, 4);

# Create data resume
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Jean Dupont', 'cv_jean_dupont.pdf', 1);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Marie Martin', 'cv_marie_martin.pdf', 2);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Pierre Durand', 'cv_pierre_durand.pdf', 3);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Julie Dufour', 'cv_julie_dufour.pdf', 4);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Thomas Leroy', 'cv_thomas_leroy.pdf', 5);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Sophie Moreau', 'cv_sophie_moreau.pdf', 6);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Jean Dupont', 'cv_jean_dupont.pdf', 1);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Marie Martin', 'cv_marie_martin.pdf', 2);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Pierre Durand', 'cv_pierre_durand.pdf', 3);
INSERT INTO resume (title, file, candidate_id)
VALUES ('CV Julie Dufour', 'cv_julie_dufour.pdf', 4);

# Create data resume_technology
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 1);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 2);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 3);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 4);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 5);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 6);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 7);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 8);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 9);
INSERT INTO resume_technology (resume_id, technology_id)
VALUES (1, 10);
