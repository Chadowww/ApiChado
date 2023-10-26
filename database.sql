DROP DATABASE IF EXISTS APICHADO;

CREATE DATABASE APICHADO;
USE APICHADO;

CREATE TABLE `joboffer` (
                            `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                            `title` VARCHAR(255) NULL,
                            `description` TEXT NULL,
                            `city` VARCHAR(255) NULL,
                            `salaryMin` INT NULL,
                            `salaryMax` INT NULL
);

INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
        'Développeur Web',
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
        40000
);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
        'Développeur Front-End',
        'Présentation de l\'entreprise : Entreprise XYZ, leader dans le secteur de l\'e-commerce. Description du
           poste : Nous recherchons un développeur Front-End pour rejoindre notre équipe et contribuer au développement de notre site web.',
        'New York',
        45000,
        60000
);
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Développeur Full-Stack',
           'Présentation de l''entreprise : Startup innovante dans le domaine de la santé connectée. Description du poste : En tant que développeur Full-Stack, vous participerez à la création d''une application de suivi de la santé.',
           'San Francisco',
           55000,
           70000
       );
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Ingénieur DevOps',
           'Présentation de l''entreprise : Entreprise de technologie spécialisée dans le cloud computing. Description du poste : Nous cherchons un ingénieur DevOps pour optimiser notre infrastructure cloud.',
           'Seattle',
           60000,
           80000
       );
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Designer UX/UI',
           'Présentation de l''entreprise : Agence de design renommée. Description du poste : Nous recrutons un designer UX/UI pour créer des interfaces utilisateur intuitives et esthétiques.',
           'Londres',
           40000,
           55000
       );
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Analyste de données',
           'Présentation de l''entreprise : Géant de la finance. Description du poste : Rejoignez notre équipe d''analystes de données pour analyser les tendances financières.',
           'Francfort',
           70000,
           90000
       );
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Développeur Mobile (iOS)',
           'Présentation de l''entreprise : Startup en pleine croissance dans le domaine des applications mobiles. Description du poste : Vous serez responsable du développement d''applications iOS de qualité.',
           'Toronto',
           50000,
           65000
       );
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Ingénieur en cybersécurité',
           'Présentation de l''entreprise : Leader de la sécurité informatique. Description du poste : Protégez nos systèmes contre les menaces en tant qu''ingénieur en cybersécurité.',
           'Singapour',
           65000,
           85000
       );
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Chef de Projet Agile',
           'Présentation de l''entreprise : Entreprise de conseil en gestion. Description du poste : Dirigez des projets agiles et collaborez avec nos clients pour atteindre leurs objectifs.',
           'Paris',
           60000,
           80000
       );
INSERT INTO joboffer (title, description, city, salaryMin, salaryMax)
VALUES (
           'Ingénieur en intelligence artificielle',
           'Présentation de l''entreprise : Société technologique axée sur l''IA. Description du poste : Vous travaillerez sur des projets d''intelligence artificielle passionnants.',
           'Pékin',
           70000,
           95000
       );
