DROP DATABASE IF EXISTS APICHADO;

CREATE DATABASE APICHADO;
USE APICHADO;

CREATE TABLE user
(
    `userId`       INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `email`    VARCHAR(255)                   NULL,
    `password` VARCHAR(255)                   NULL,
    `roles`     INT                            NULL,
    `is_verified` BOOLEAN DEFAULT FALSE       NULL,
    `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE candidate
(
    `candidateId`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `firstname`   VARCHAR(50)                    NULL,
    `lastname`    VARCHAR(50)                    NULL,
    `phone`       VARCHAR(10)                    NULL,
    `address`     VARCHAR(255)                   NULL,
    `city`        VARCHAR(50)                    NULL,
    `country`     VARCHAR(50)                    NULL,
    `avatar`      VARCHAR(255)                   NULL,
    `slug`        VARCHAR(255)                   NULL,
    `coverLetter` TEXT                           NULL,
    `userId`     INT                            NULL,
    CONSTRAINT `fk_candidate_user_id` FOREIGN KEY (`userId`) REFERENCES `user` (userId) ON DELETE CASCADE
);

CREATE TABLE company
(
    `companyId`      INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
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
    `userId` INT                            NULL,
    CONSTRAINT `fk_company_user_id` FOREIGN KEY (`userId`) REFERENCES `user` (userId) ON DELETE CASCADE
);

CREATE TABLE socialeMedia(
     `socialeMediaId`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
     `linkedin`    VARCHAR(255)                   NULL,
     `github`      VARCHAR(255)                   NULL,
     `twitter`     VARCHAR(255)                   NULL,
     `facebook`    VARCHAR(255)                   NULL,
     `instagram`   VARCHAR(255)                   NULL,
     `website`     VARCHAR(255)                   NULL,
     `userId`     INT                            NULL,
     CONSTRAINT `fk_socialeMedia_user_id` FOREIGN KEY (`userId`) REFERENCES `user` (userId) ON DELETE CASCADE
);

CREATE TABLE contract
(
    `contractId`   INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `type` VARCHAR(255)                   NULL
);

CREATE TABLE `category`
(
    `categoryId`   INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(255)                   NULL
);

CREATE TABLE `joboffer`
(
    `jobofferId`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `title`       VARCHAR(255)                   NULL,
    `description` TEXT                           NULL,
    `city`        VARCHAR(50)                    NULL,
    `salaryMin`   INT                            NULL,
    `salaryMax`   INT                            NULL,
    `contractId` INT                            NULL,
    `companyId`  INT                            NULL,
    `categoryId` INT                            NULL,
    `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT `fk_joboffer_company_id` FOREIGN KEY (`companyId`) REFERENCES `company` (companyId) ON DELETE SET NULL,
    CONSTRAINT `fk_joboffer_contract_id` FOREIGN KEY (`contractId`) REFERENCES `contract` (contractId),
    CONSTRAINT `fk_joboffer_category_id` FOREIGN KEY (`categoryId`) REFERENCES `category` (categoryId)

);

CREATE TABLE `technology`
(
    `technologyId`          INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name`        VARCHAR(255)                   NULL,
    `categoryId` INT                            NULL,
    CONSTRAINT `fk_technology_category_id` FOREIGN KEY (`categoryId`) REFERENCES `category` (categoryId)
);

CREATE TABLE `favlist`
(
    `favlistId`           INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `candidateId` INT                            NULL,
    `jobofferId`  INT                            NULL,
    CONSTRAINT `fk_favlist_candidate_id` FOREIGN KEY (`candidateId`) REFERENCES `candidate` (candidateId) ON DELETE CASCADE ,
    CONSTRAINT `fk_favlist_joboffer_id` FOREIGN KEY (`jobofferId`) REFERENCES `joboffer` (jobofferId) ON DELETE CASCADE
);

CREATE TABLE `resume`(
     `resumeId`           INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
     `title`               VARCHAR(255)                   NULL,
     `filename`             VARCHAR(255)                   NULL,
     `candidateId`        INT                            NULL,
     `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
     `updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
     CONSTRAINT `fk_resume_candidate_id` FOREIGN KEY (`candidateId`) REFERENCES `candidate` (candidateId) ON DELETE CASCADE
);

CREATE TABLE `apply`
(
    `applyId`           INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `status`       VARCHAR(255)                   NULL,
    `message`      TEXT                           NULL,
    `resumeId`    INT                            NULL,
    `candidateId` INT                            NULL,
    `jobofferId`  INT                            NULL,
    `createdAt`   DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `updatedAt`   DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT `fk_apply_resume_id` FOREIGN KEY (`resumeId`) REFERENCES `resume` (resumeId),
    CONSTRAINT `fk_apply_candidate_id` FOREIGN KEY (`candidateId`) REFERENCES `candidate` (candidateId) ON DELETE CASCADE,
    CONSTRAINT `fk_apply_joboffer_id` FOREIGN KEY (`jobofferId`) REFERENCES `joboffer` (jobofferId) ON DELETE CASCADE
);

CREATE TABLE `resume_technology`(
                                    `resume_technology_ìd`            INT PRIMARY KEY AUTO_INCREMENT NOT NULL ,
                                    `resumeId`     INT                            NULL,
                                    `technologyId` INT                            NULL,
                                    CONSTRAINT `fk_resume_technology_resume_id` FOREIGN KEY (`resumeId`) REFERENCES `resume` (resumeId) ON DELETE CASCADE,
                                    CONSTRAINT `fk_resume_technology_technology_id` FOREIGN KEY (`technologyId`) REFERENCES `technology` (technologyId) ON DELETE CASCADE
);

# Create index for fulltext search
CREATE FULLTEXT INDEX `idx_joboffer_title_description` ON `joboffer` (`title`, `description`);
CREATE FULLTEXT INDEX `idx_candidate_firstname_lastname_description` ON `candidate` (`firstname`, `lastname`);

# Create data user
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company1@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company2@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company3@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company4@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company5@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company6@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('candidate7@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('candidate8@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('candidate9@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('candidate10@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('candidate11@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('candidate12@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('admin@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 9, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company7@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company8@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company9@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('company10@hotmail.fr', '$2y$13$3vm8QvCTBKu/ZAI0NHpIE.tYjFgaijYCrKtxCHZnNpWqLdAxIn63i', 5, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');
INSERT INTO user (email, password, roles, is_verified, createdAt, updatedAt)
VALUES ('a.sale@outlook.fr', '$2y$13$U755YOX.VbHBuxhv4Y.UyeLj3aopPcOIm1734bKyECKfijyR/34ne', 3, 1, '2021-05-01 00:00:00', '2021-05-01 00:00:00');

# Create data candidate
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, userId)
VALUES ('Jean', 'Dupont', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis un candidat motivé et passionné par le développement web.', 7);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, userId)
VALUES ('Marie', 'Martin', '0123456789', '123 rue de la République', 'Lyon', 'France',
        'Je suis une candidate motivée et passionnée par le développement web.', 8);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, userId)
VALUES ('Pierre', 'Durand', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis un candidat motivé et passionné par le développement web.', 9);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, userId)
VALUES ('Julie', 'Dufour', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis une candidate motivée et passionnée par le développement web.', 10);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, userId)
VALUES ('Thomas', 'Leroy', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis un candidat motivé et passionné par le développement web.', 11);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, userId)
VALUES ('Sophie', 'Moreau', '0123456789', '1 rue de la Paix', 'Paris', 'France',
        'Je suis une candidate motivée et passionnée par le développement web.', 12);
INSERT INTO candidate (firstname, lastname, phone, address, city, country, coverLetter, userId)
VALUES ('Alexandre', 'Salé', '0783070052', '1 rue de la Paix', 'Paris', 'France',
        'Je suis un candidat motivé et passionné par le développement web.', 18);


# Create data company
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('AS Turing', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'as-turing',
        'logo.png', 'GroupWork20.jpg', '0123456789',1);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('Entreprise XYZ', '123 rue de la République', 'Lyon', 'France', '12345678901234', 'description',
        'Entreprise-XYZ','logo1.png', 'GroupWork23.jpg', '0123456789', 2);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('Startup ABC', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Startup-ABC',
        'logo2.png', 'GroupWork24.jpg',
        '0123456789', 3);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('Entreprise 123', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Entreprise-123',
        'logo3.png', 'GroupWork5
.jpg',
        '0123456789', 4);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('Startup 456', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Startup-456',
        'logo4.png', 'GroupWork6.jpg',
        '0123456789', 5);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('Startup 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Startup-789',
        'logo5.png', 'GroupWork7.jpg',
        '0123456789', 6);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('Company 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'Company-789',
        'logo6.png', 'GroupWork8.jpg',
        '0123456789', 14);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('sas 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'sas-789', 'logo7.jpg',
        'GroupWork9.jpg',
        '0123456789', 15);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('entreprise 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 'entreprise-789',
        'logo8.jpg', 'GroupWork10.jpg',
        '0123456789', 16);
INSERT INTO company (name, address, city, country, siret, description, slug, logo, cover, phone, userId)
VALUES ('s2i 789', '1 rue de la Paix', 'Paris', 'France', '12345678901234', 'description', 's2i-789',
        'logo.9.png', 'GroupWork11.jpg', '0123456789', 17);

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
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Web', '<h6>À propos de nous :</h6>
  <p>
    Nous sommes une entreprise dynamique et en pleine croissance spécialisée dans le développement de solutions web
    innovantes. Notre équipe passionnée est composée de professionnels talentueux qui repoussent constamment les limites
    de la technologie pour offrir des produits de qualité supérieure à nos clients. Nous cherchons actuellement un
    développeur Web PHP Symfony motivé et compétent pour rejoindre notre équipe.
  </p>

  <h6>Responsabilités :</h6>
  <ul>
    <li>Participer activement au cycle de développement de nos projets web en utilisant le framework PHP Symfony.</li>
    <li>Analyser les spécifications fonctionnelles et techniques,
    et participer à la conception de solutions adaptées.</li>
    <li>Développer, tester et déployer des fonctionnalités en respectant les normes de qualité et les délais fixés.</li>
    <li>Collaborer étroitement avec les membres de l\'équipe, y compris les concepteurs UX/UI,
        les développeurs front-end et les testeurs.</li>
    <li>Résoudre les problèmes techniques et assurer la maintenance des applys existantes.</li>
    <li>Suivre les bonnes pratiques de développement, les normes de codage et les procédures internes.</li>
  </ul>

  <h6>Exigences :</h6>
  <ul>
    <li>Expérience professionnelle préalable en développement web PHP avec une maîtrise de Symfony.</li>
    <li>Solide connaissance des langages de programmation web tels que HTML, CSS et JavaScript.</li>
    <li>Expérience avec les bases de données relationnelles, notamment MySQL ou PostgreSQL.</li>
    <li>Compréhension des principes de développement orienté objet.</li>
    <li>Familiarité avec les outils de gestion de versions, tels que Git.</li>
    <li>Capacité à travailler en équipe, à communiquer efficacement
    et à s\'adapter à un environnement en évolution rapide.</li>
    <li>Attitude proactive et souci du détail pour fournir des solutions de haute qualité.</li>
  </ul>

  <h6>Avantages :</h6>
  <ul>
    <li>Salaire compétitif correspondant à l\'expérience et aux compétences.</li>
    <li>Environnement de travail collaboratif et innovant.</li>
    <li>Opportunités de développement professionnel et de formation continue.</li>
    <li>Projets stimulants et variés.</li>
    <li>Horaires flexibles et possibilité de télétravail.</li>
  </ul>',
        'Paris',
        30000,
        40000,
        1,
        1,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Front-End',
        '<h6>À propos de nous :</h6>
  <p>
    Nous sommes une entreprise dynamique et en pleine croissance spécialisée dans le développement de solutions web
    innovantes. Notre équipe passionnée est composée de professionnels talentueux qui repoussent constamment les limites
    de la technologie pour offrir des produits de qualité supérieure à nos clients. Nous cherchons actuellement un
    développeur Web PHP Symfony motivé et compétent pour rejoindre notre équipe.
  </p>

  <h6>Responsabilités :</h6>
  <ul>
    <li>Participer activement au cycle de développement de nos projets web en utilisant le framework PHP Symfony.</li>
    <li>Analyser les spécifications fonctionnelles et techniques,
    et participer à la conception de solutions adaptées.</li>
    <li>Développer, tester et déployer des fonctionnalités en respectant les normes de qualité et les délais fixés.</li>
    <li>Collaborer étroitement avec les membres de l\'équipe, y compris les concepteurs UX/UI,
        les développeurs front-end et les testeurs.</li>
    <li>Résoudre les problèmes techniques et assurer la maintenance des applys existantes.</li>
    <li>Suivre les bonnes pratiques de développement, les normes de codage et les procédures internes.</li>
  </ul>

  <h6>Exigences :</h6>
  <ul>
    <li>Expérience professionnelle préalable en développement web PHP avec une maîtrise de Symfony.</li>
    <li>Solide connaissance des langages de programmation web tels que HTML, CSS et JavaScript.</li>
    <li>Expérience avec les bases de données relationnelles, notamment MySQL ou PostgreSQL.</li>
    <li>Compréhension des principes de développement orienté objet.</li>
    <li>Familiarité avec les outils de gestion de versions, tels que Git.</li>
    <li>Capacité à travailler en équipe, à communiquer efficacement
    et à s\'adapter à un environnement en évolution rapide.</li>
    <li>Attitude proactive et souci du détail pour fournir des solutions de haute qualité.</li>
  </ul>

  <h6>Avantages :</h6>
  <ul>
    <li>Salaire compétitif correspondant à l\'expérience et aux compétences.</li>
    <li>Environnement de travail collaboratif et innovant.</li>
    <li>Opportunités de développement professionnel et de formation continue.</li>
    <li>Projets stimulants et variés.</li>
    <li>Horaires flexibles et possibilité de télétravail.</li>
  </ul>',
        'New York',
        45000,
        60000,
        2,
        2,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Full-Stack',
        '''<h6>À propos de nous :</h6>
  <p>
    Nous sommes une entreprise dynamique et en pleine croissance spécialisée dans le développement de solutions web
    innovantes. Notre équipe passionnée est composée de professionnels talentueux qui repoussent constamment les limites
    de la technologie pour offrir des produits de qualité supérieure à nos clients. Nous cherchons actuellement un
    développeur Web PHP Symfony motivé et compétent pour rejoindre notre équipe.
  </p>

  <h6>Responsabilités :</h6>
  <ul>
    <li>Participer activement au cycle de développement de nos projets web en utilisant le framework PHP Symfony.</li>
    <li>Analyser les spécifications fonctionnelles et techniques,
    et participer à la conception de solutions adaptées.</li>
    <li>Développer, tester et déployer des fonctionnalités en respectant les normes de qualité et les délais fixés.</li>
    <li>Collaborer étroitement avec les membres de l\'équipe, y compris les concepteurs UX/UI,
        les développeurs front-end et les testeurs.</li>
    <li>Résoudre les problèmes techniques et assurer la maintenance des applys existantes.</li>
    <li>Suivre les bonnes pratiques de développement, les normes de codage et les procédures internes.</li>
  </ul>

  <h6>Exigences :</h6>
  <ul>
    <li>Expérience professionnelle préalable en développement web PHP avec une maîtrise de Symfony.</li>
    <li>Solide connaissance des langages de programmation web tels que HTML, CSS et JavaScript.</li>
    <li>Expérience avec les bases de données relationnelles, notamment MySQL ou PostgreSQL.</li>
    <li>Compréhension des principes de développement orienté objet.</li>
    <li>Familiarité avec les outils de gestion de versions, tels que Git.</li>
    <li>Capacité à travailler en équipe, à communiquer efficacement
    et à s\'adapter à un environnement en évolution rapide.</li>
    <li>Attitude proactive et souci du détail pour fournir des solutions de haute qualité.</li>
  </ul>

  <h6>Avantages :</h6>
  <ul>
    <li>Salaire compétitif correspondant à l\'expérience et aux compétences.</li>
    <li>Environnement de travail collaboratif et innovant.</li>
    <li>Opportunités de développement professionnel et de formation continue.</li>
    <li>Projets stimulants et variés.</li>
    <li>Horaires flexibles et possibilité de télétravail.</li>
  </ul>',
        'San Francisco',
        55000,
        70000,
        3,
        3,
        6);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Ingénieur DevOps',
        'Présentation de l''entreprise : Entreprise de technologie spécialisée dans le cloud computing. Description du poste : Nous cherchons un ingénieur DevOps pour optimiser notre infrastructure cloud.',
        'Seattle',
        60000,
        80000,
        4,
        4,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Designer UX/UI',
        'Présentation de l''entreprise : Agence de design renommée. Description du poste : Nous recrutons un designer UX/UI pour créer des interfaces utilisateur intuitives et esthétiques.',
        'Londres',
        40000,
        55000,
        5,
        5,
        2);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Analyste de données',
        'Présentation de l''entreprise : Géant de la finance. Description du poste : Rejoignez notre équipe d''analystes de données pour analyser les tendances financières.',
        'Francfort',
        70000,
        90000,
        1,
        6,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Mobile (iOS)',
        'Présentation de l''entreprise : Startup en pleine croissance dans le domaine des applys mobiles. Description du poste : Vous serez responsable du développement d''applys iOS de qualité.',
        'Toronto',
        50000,
        65000,
        2,
        1,
        5);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Ingénieur en cybersécurité',
        'Présentation de l''entreprise : Leader de la sécurité informatique. Description du poste : Protégez nos systèmes contre les menaces en tant qu''ingénieur en cybersécurité.',
        'Singapour',
        65000,
        85000,
        3,
        2,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Chef de Projet Agile',
        'Présentation de l''entreprise : Entreprise de conseil en gestion. Description du poste : Dirigez des projets agiles et collaborez avec nos clients pour atteindre leurs objectifs.',
        'Paris',
        60000,
        80000,
        4,
        3,
        3);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Ingénieur en intelligence artificielle',
        'Présentation de l''entreprise : Société technologique axée sur l''IA. Description du poste : Vous travaillerez sur des projets d''intelligence artificielle passionnants.',
        'Pékin',
        70000,
        95000,
        5,
        4,
        2);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Web',
        'Titre du poste : Développeur Web


Présentation de l''entreprise :
Vous travaillerez pour la société AS Turing dans le pole déveoppement
Description du poste :
En tant que développeur web au sein de notre entreprise, vous rejoindrez une équipe dynamique et passionnée qui se consacre à la création de solutions web exceptionnelles. Vous participerez au développement, à la maintenance et à l''amélioration de nos applys web, contribuant ainsi à la croissance de notre entreprise.

Responsabilités principales :

Concevoir, développer, tester et mettre en œuvre des applys web de haute qualité.
Collaborer avec les équipes interfonctionnelles pour comprendre les besoins et les exigences du projet.
Résoudre les problèmes techniques et optimiser les performances des applys existantes.
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
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Front-End',
        'Présentation de l\'entreprise : Entreprise XYZ, leader dans le secteur de l\'e-commerce. Description du
           poste : Nous recherchons un développeur Front-End pour rejoindre notre équipe et contribuer au développement de notre site web.',
        'New York',
        45000,
        60000,
        2,
        2,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Full-Stack',
        'Présentation de l''entreprise : Startup innovante dans le domaine de la santé connectée. Description du poste : En tant que développeur Full-Stack, vous participerez à la création d''une apply de suivi de la santé.',
        'San Francisco',
        55000,
        70000,
        3,
        3,
        6);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Ingénieur DevOps',
        'Présentation de l''entreprise : Entreprise de technologie spécialisée dans le cloud computing. Description du poste : Nous cherchons un ingénieur DevOps pour optimiser notre infrastructure cloud.',
        'Seattle',
        60000,
        80000,
        4,
        4,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Designer UX/UI',
        'Présentation de l''entreprise : Agence de design renommée. Description du poste : Nous recrutons un designer UX/UI pour créer des interfaces utilisateur intuitives et esthétiques.',
        'Londres',
        40000,
        55000,
        5,
        5,
        2);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Analyste de données',
        'Présentation de l''entreprise : Géant de la finance. Description du poste : Rejoignez notre équipe d''analystes de données pour analyser les tendances financières.',
        'Francfort',
        70000,
        90000,
        1,
        6,
        1);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Développeur Mobile (iOS)',
        'Présentation de l''entreprise : Startup en pleine croissance dans le domaine des applys mobiles. Description du poste : Vous serez responsable du développement d''applys iOS de qualité.',
        'Toronto',
        50000,
        65000,
        2,
        1,
        5);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Ingénieur en cybersécurité',
        'Présentation de l''entreprise : Leader de la sécurité informatique. Description du poste : Protégez nos systèmes contre les menaces en tant qu''ingénieur en cybersécurité.',
        'Singapour',
        65000,
        85000,
        3,
        2,
        4);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Chef de Projet Agile',
        'Présentation de l''entreprise : Entreprise de conseil en gestion. Description du poste : Dirigez des projets agiles et collaborez avec nos clients pour atteindre leurs objectifs.',
        'Paris',
        60000,
        80000,
        4,
        3,
        3);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax, contractId, companyId, categoryId)
VALUES ('Ingénieur en intelligence artificielle',
        'Présentation de l''entreprise : Société technologique axée sur l''IA. Description du poste : Vous travaillerez sur des projets d''intelligence artificielle passionnants.',
        'Pékin',
        70000,
        95000,
        5,
        4,
        2);

# Create data technology
INSERT INTO technology (name, categoryId)
VALUES ('HTML', 1);
INSERT INTO technology (name, categoryId)
VALUES ('CSS', 1);
INSERT INTO technology (name, categoryId)
VALUES ('JavaScript', 1);
INSERT INTO technology (name, categoryId)
VALUES ('PHP', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Python', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Java', 1);
INSERT INTO technology (name, categoryId)
VALUES ('C#', 1);
INSERT INTO technology (name, categoryId)
VALUES ('C++', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Ruby', 1);
INSERT INTO technology (name, categoryId)
VALUES ('SQL', 1);
INSERT INTO technology (name, categoryId)
VALUES ('NoSQL', 1);
INSERT INTO technology (name, categoryId)
VALUES ('MongoDB', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Node.js', 1);
INSERT INTO technology (name, categoryId)
VALUES ('React', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Angular', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Vue.js', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Symfony', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Laravel', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Spring', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Django', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Flask', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Bootstrap', 1);
INSERT INTO technology (name, categoryId)
VALUES ('jQuery', 1);
INSERT INTO technology (name, categoryId)
VALUES ('WordPress', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Magento', 1);
INSERT INTO technology (name, categoryId)
VALUES ('Shopify', 1);
INSERT INTO technology (name, categoryId)
VALUES ('PrestaShop', 1);
INSERT INTO technology (name, categoryId)
VALUES ('iOS', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Android', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Swift', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Kotlin', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Objective-C', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Java', 2);
INSERT INTO technology (name, categoryId)
VALUES ('React Native', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Ionic', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Xamarin', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Flutter', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Cordova', 2);
INSERT INTO technology (name, categoryId)
VALUES ('PhoneGap', 2);
INSERT INTO technology (name, categoryId)
VALUES ('Ionic', 2);

# Create data favlist
INSERT INTO favlist (candidateId, jobofferId)
VALUES (1, 1);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (2, 2);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (4, 3);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (1, 4);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (6, 5);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (1, 6);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (2, 1);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (3, 2);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (2, 3);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (4, 4);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (2, 5);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (5, 6);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (6, 1);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (3, 2);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (3, 3);
INSERT INTO favlist (candidateId, jobofferId)
VALUES (3, 4);

# Create data resume
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Jean Dupont', 'cv_jean_dupont.pdf', 1);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Marie Martin', 'cv_marie_martin.pdf', 2);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Pierre Durand', 'cv_pierre_durand.pdf', 3);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Julie Dufour', 'cv_julie_dufour.pdf', 4);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Thomas Leroy', 'cv_thomas_leroy.pdf', 5);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Sophie Moreau', 'cv_sophie_moreau.pdf', 6);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Jean Dupont', 'cv_jean_dupont.pdf', 1);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Marie Martin', 'cv_marie_martin.pdf', 2);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Pierre Durand', 'cv_pierre_durand.pdf', 3);
INSERT INTO resume (title, filename, candidateId)
VALUES ('CV Julie Dufour', 'cv_julie_dufour.pdf', 4);

# Create data apply
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 1, 1);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 2, 1);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 3, 1);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 4, 1);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 5, 1);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 6, 1);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 1, 2);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 2, 2);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 3, 2);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 4, 2);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 5, 2);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 6, 2);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 1, 3);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 2, 3);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 3, 3);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 4, 3);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 5, 3);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 6, 3);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 1, 4);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 2, 4);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 3, 4);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 4, 4);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 5, 4);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 6, 4);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 1, 5);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 2, 5);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 3, 5);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 4, 5);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 5, 5);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 6, 5);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 1, 6);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 2, 6);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 3, 6);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 4, 6);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 5, 6);
INSERT INTO apply (status, candidateId, jobofferId)
VALUES ('En attente', 6, 6);

# Create data resume_technology
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 1);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 2);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 3);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 4);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 5);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 6);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 7);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 8);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 9);
INSERT INTO resume_technology (resumeId, technologyId)
VALUES (1, 10);
