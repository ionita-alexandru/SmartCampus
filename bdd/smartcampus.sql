DROP DATABASE IF EXISTS smartcampusdb;
CREATE DATABASE smartcampusdb;
USE smartcampusdb;

CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('etudiant','enseignant','admin') NOT NULL,
    actif TINYINT(1) DEFAULT 1
);

CREATE TABLE promotions (
    id_promotion INT AUTO_INCREMENT PRIMARY KEY,
    nom_promotion VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE amphis (
    id_amphi INT AUTO_INCREMENT PRIMARY KEY,
    nom_amphi VARCHAR(50) NOT NULL,
    id_promotion INT NOT NULL,
    capacite INT NOT NULL DEFAULT 90,
    FOREIGN KEY (id_promotion) REFERENCES promotions(id_promotion)
);

CREATE TABLE groupes (
    id_groupe INT AUTO_INCREMENT PRIMARY KEY,
    nom_groupe VARCHAR(50) NOT NULL,
    id_promotion INT NOT NULL,
    id_amphi INT NOT NULL,
    FOREIGN KEY (id_promotion) REFERENCES promotions(id_promotion),
    FOREIGN KEY (id_amphi) REFERENCES amphis(id_amphi)
);

CREATE TABLE enseignants (
    id_enseignant INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    grade VARCHAR(100),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);

CREATE TABLE etudiants (
    id_etudiant INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    numero_etudiant VARCHAR(50) NOT NULL UNIQUE,
    id_promotion INT NOT NULL,
    id_groupe INT NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (id_promotion) REFERENCES promotions(id_promotion),
    FOREIGN KEY (id_groupe) REFERENCES groupes(id_groupe)
);

CREATE TABLE salles (
    id_salle INT AUTO_INCREMENT PRIMARY KEY,
    nom_salle VARCHAR(100) NOT NULL,
    type_salle ENUM('TD','AMPHI') NOT NULL,
    capacite INT NOT NULL
);

CREATE TABLE cours (
    id_cours INT AUTO_INCREMENT PRIMARY KEY,
    code_cours VARCHAR(30) NOT NULL UNIQUE,
    nom_cours VARCHAR(150) NOT NULL,
    type_cours ENUM('CM','TD','OPTIONNEL') NOT NULL,
    id_promotion INT NOT NULL,
    ects INT NOT NULL,
    coefficient FLOAT NOT NULL,
    capacite_max INT NOT NULL,
    id_enseignant INT NOT NULL,
    FOREIGN KEY (id_promotion) REFERENCES promotions(id_promotion),
    FOREIGN KEY (id_enseignant) REFERENCES enseignants(id_enseignant)
);

CREATE TABLE inscriptions (
    id_inscription INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(id_etudiant, id_cours),
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id_etudiant),
    FOREIGN KEY (id_cours) REFERENCES cours(id_cours)
);

CREATE TABLE seances (
    id_seance INT AUTO_INCREMENT PRIMARY KEY,
    id_cours INT NOT NULL,
    id_salle INT NOT NULL,
    id_groupe INT NULL,
    id_amphi INT NULL,
    date_seance DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    code_presence VARCHAR(12) NOT NULL,
    FOREIGN KEY (id_cours) REFERENCES cours(id_cours),
    FOREIGN KEY (id_salle) REFERENCES salles(id_salle),
    FOREIGN KEY (id_groupe) REFERENCES groupes(id_groupe),
    FOREIGN KEY (id_amphi) REFERENCES amphis(id_amphi)
);

CREATE TABLE presences (
    id_presence INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_seance INT NOT NULL,
    statut ENUM('present','absent','retard') DEFAULT 'absent',
    code_saisi VARCHAR(12),
    date_validation TIMESTAMP NULL,
    UNIQUE(id_etudiant, id_seance),
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id_etudiant),
    FOREIGN KEY (id_seance) REFERENCES seances(id_seance)
);

CREATE TABLE notes (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    note FLOAT NOT NULL,
    verrouille TINYINT(1) DEFAULT 0,
    type_evaluation ENUM('CC','EXAMEN') DEFAULT 'CC',
    coefficient_note FLOAT DEFAULT 1,
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id_etudiant),
    FOREIGN KEY (id_cours) REFERENCES cours(id_cours)
);

CREATE TABLE messages (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_expediteur INT NOT NULL,
    id_destinataire INT NOT NULL,
    sujet VARCHAR(150),
    contenu TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_expediteur) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (id_destinataire) REFERENCES utilisateurs(id_utilisateur)
);


INSERT INTO promotions (nom_promotion) VALUES
('ING1'), ('ING2'), ('ING3'), ('ING4'), ('ING5');

INSERT INTO amphis (nom_amphi, id_promotion, capacite)
SELECT CONCAT(nom_promotion, ' - Amphi A'), id_promotion, 90 FROM promotions
UNION ALL
SELECT CONCAT(nom_promotion, ' - Amphi B'), id_promotion, 90 FROM promotions
UNION ALL
SELECT CONCAT(nom_promotion, ' - Amphi C'), id_promotion, 90 FROM promotions;

DELIMITER //

CREATE PROCEDURE generer_groupes()
BEGIN
    DECLARE p INT DEFAULT 1;
    DECLARE g INT;
    DECLARE amphi_id INT;

    WHILE p <= 5 DO
        SET g = 1;

        WHILE g <= 9 DO
            IF g <= 3 THEN
                SELECT id_amphi INTO amphi_id
                FROM amphis
                WHERE id_promotion = p AND nom_amphi LIKE '%Amphi A';
            ELSEIF g <= 6 THEN
                SELECT id_amphi INTO amphi_id
                FROM amphis
                WHERE id_promotion = p AND nom_amphi LIKE '%Amphi B';
            ELSE
                SELECT id_amphi INTO amphi_id
                FROM amphis
                WHERE id_promotion = p AND nom_amphi LIKE '%Amphi C';
            END IF;

            INSERT INTO groupes (nom_groupe, id_promotion, id_amphi)
            VALUES (CONCAT('Groupe ', g), p, amphi_id);

            SET g = g + 1;
        END WHILE;

        SET p = p + 1;
    END WHILE;
END//

DELIMITER ;

CALL generer_groupes();
DROP PROCEDURE generer_groupes;

INSERT INTO salles (nom_salle, type_salle, capacite) VALUES
('TD101', 'TD', 30),
('TD102', 'TD', 30),
('TD103', 'TD', 30),
('TD104', 'TD', 30),
('TD105', 'TD', 30),
('TD106', 'TD', 30),
('TD107', 'TD', 30),
('TD108', 'TD', 30),
('TD109', 'TD', 30),
('TD110', 'TD', 30),
('TD111', 'TD', 30),
('TD112', 'TD', 30),
('TD113', 'TD', 30),
('TD114', 'TD', 30),
('TD115', 'TD', 30),
('AMPHI-A', 'AMPHI', 90),
('AMPHI-B', 'AMPHI', 90),
('AMPHI-C', 'AMPHI', 90),
('AMPHI-D', 'AMPHI', 90),
('AMPHI-E', 'AMPHI', 90);

INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'SmartCampus', 'admin@smartcampus.fr', '1234', 'admin');

DELIMITER //

CREATE PROCEDURE generer_enseignants()
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE uid INT;

    WHILE i <= 40 DO
        INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role)
        VALUES (
            CONCAT('Enseignant', i),
            CONCAT('PrenomProf', i),
            CONCAT('enseignant', i, '@smartcampus.fr'),
            '1234',
            'enseignant'
        );

        SET uid = LAST_INSERT_ID();

        INSERT INTO enseignants (id_utilisateur, grade)
        VALUES (uid, 'Enseignant');

        SET i = i + 1;
    END WHILE;
END//

DELIMITER ;

CALL generer_enseignants();
DROP PROCEDURE generer_enseignants;

DELIMITER //

CREATE PROCEDURE generer_cours()
BEGIN
    DECLARE p INT DEFAULT 1;
    DECLARE promo VARCHAR(20);

    WHILE p <= 5 DO
        SELECT nom_promotion INTO promo
        FROM promotions
        WHERE id_promotion = p;

        INSERT INTO cours (
            code_cours,
            nom_cours,
            type_cours,
            id_promotion,
            ects,
            coefficient,
            capacite_max,
            id_enseignant
        ) VALUES
        (CONCAT(promo, '-WEB-CM'), CONCAT(promo, ' Web Dynamique CM'), 'CM', p, 6, 2, 90, 1),
        (CONCAT(promo, '-BDD-CM'), CONCAT(promo, ' Base de données CM'), 'CM', p, 5, 2, 90, 2),
        (CONCAT(promo, '-PHP-CM'), CONCAT(promo, ' PHP/MySQL CM'), 'CM', p, 5, 2, 90, 3),
        (CONCAT(promo, '-JS-CM'), CONCAT(promo, ' JavaScript CM'), 'CM', p, 4, 1.5, 90, 4),
        (CONCAT(promo, '-MATH-CM'), CONCAT(promo, ' Mathématiques CM'), 'CM', p, 5, 2, 90, 5),
        (CONCAT(promo, '-ANG-CM'), CONCAT(promo, ' Anglais CM'), 'CM', p, 3, 1, 90, 6),
        (CONCAT(promo, '-PHY-CM'), CONCAT(promo, ' Physique CM'), 'CM', p, 5, 2, 90, 7),
        (CONCAT(promo, '-ELEC-CM'), CONCAT(promo, ' Électronique CM'), 'CM', p, 4, 1.5, 90, 8),

        (CONCAT(promo, '-WEB-TD'), CONCAT(promo, ' Web Dynamique TD'), 'TD', p, 6, 2, 30, 9),
        (CONCAT(promo, '-BDD-TD'), CONCAT(promo, ' Base de données TD'), 'TD', p, 5, 2, 30, 10),
        (CONCAT(promo, '-PHP-TD'), CONCAT(promo, ' PHP/MySQL TD'), 'TD', p, 5, 2, 30, 11),
        (CONCAT(promo, '-JS-TD'), CONCAT(promo, ' JavaScript TD'), 'TD', p, 4, 1.5, 30, 12),
        (CONCAT(promo, '-MATH-TD'), CONCAT(promo, ' Mathématiques TD'), 'TD', p, 5, 2, 30, 13),
        (CONCAT(promo, '-ANG-TD'), CONCAT(promo, ' Anglais TD'), 'TD', p, 3, 1, 30, 14),
        (CONCAT(promo, '-PHY-TD'), CONCAT(promo, ' Physique TD'), 'TD', p, 5, 2, 30, 15),
        (CONCAT(promo, '-ELEC-TD'), CONCAT(promo, ' Électronique TD'), 'TD', p, 4, 1.5, 30, 16),

        (CONCAT(promo, '-IA-OPT'), CONCAT(promo, ' Intelligence artificielle optionnelle'), 'OPTIONNEL', p, 3, 1, 40, 17),
        (CONCAT(promo, '-CYBER-OPT'), CONCAT(promo, ' Cybersécurité web optionnelle'), 'OPTIONNEL', p, 3, 1, 40, 18),
        (CONCAT(promo, '-UX-OPT'), CONCAT(promo, ' Design UX/UI optionnel'), 'OPTIONNEL', p, 3, 1, 40, 19);

        SET p = p + 1;
    END WHILE;
END//

DELIMITER ;

CALL generer_cours();
DROP PROCEDURE generer_cours;

DELIMITER //

CREATE PROCEDURE generer_etudiants()
BEGIN
    DECLARE p INT DEFAULT 1;
    DECLARE g INT;
    DECLARE i INT;
    DECLARE compteur INT;
    DECLARE uid INT;
    DECLARE groupe_id INT;
    DECLARE promo VARCHAR(20);

    WHILE p <= 5 DO

        SELECT nom_promotion INTO promo
        FROM promotions
        WHERE id_promotion = p;

        SET g = 1;
        SET compteur = 1;

        WHILE g <= 9 DO

            SELECT id_groupe INTO groupe_id
            FROM groupes
            WHERE id_promotion = p
            AND nom_groupe = CONCAT('Groupe ', g);

            SET i = 1;

            WHILE i <= 30 DO

                INSERT INTO utilisateurs (
                    nom,
                    prenom,
                    email,
                    mot_de_passe,
                    role
                ) VALUES (
                    CONCAT('Etudiant', promo, compteur),
                    CONCAT('Prenom', compteur),
                    CONCAT(LOWER(promo), '_etu', LPAD(compteur, 3, '0'), '@smartcampus.fr'),
                    '1234',
                    'etudiant'
                );

                SET uid = LAST_INSERT_ID();

                INSERT INTO etudiants (
                    id_utilisateur,
                    numero_etudiant,
                    id_promotion,
                    id_groupe
                ) VALUES (
                    uid,
                    CONCAT(promo, '-ETU', LPAD(compteur, 3, '0')),
                    p,
                    groupe_id
                );

                SET compteur = compteur + 1;
                SET i = i + 1;

            END WHILE;

            SET g = g + 1;

        END WHILE;

        SET p = p + 1;

    END WHILE;
END//

DELIMITER ;

CALL generer_etudiants();
DROP PROCEDURE generer_etudiants;

INSERT INTO inscriptions (id_etudiant, id_cours)
SELECT e.id_etudiant, c.id_cours
FROM etudiants e
JOIN cours c
ON c.id_promotion = e.id_promotion
WHERE c.type_cours IN ('CM', 'TD');

INSERT INTO inscriptions (id_etudiant, id_cours)
SELECT e.id_etudiant, c.id_cours
FROM etudiants e
JOIN cours c
ON c.id_promotion = e.id_promotion
WHERE c.type_cours = 'OPTIONNEL'
AND MOD(e.id_etudiant, 3) = MOD(c.id_cours, 3);

DELIMITER //

CREATE PROCEDURE generer_edt()
BEGIN

    DECLARE p INT DEFAULT 1;
    DECLARE semaine INT;
    DECLARE g INT;

    DECLARE base_date DATE;
    DECLARE groupe_id INT;

    DECLARE cours_web_cm INT;
    DECLARE cours_bdd_cm INT;
    DECLARE cours_php_cm INT;
    DECLARE cours_js_cm INT;
    DECLARE cours_math_cm INT;
    DECLARE cours_ang_cm INT;
    DECLARE cours_phy_cm INT;
    DECLARE cours_elec_cm INT;

    DECLARE cours_web_td INT;
    DECLARE cours_bdd_td INT;
    DECLARE cours_php_td INT;
    DECLARE cours_js_td INT;
    DECLARE cours_math_td INT;
    DECLARE cours_ang_td INT;
    DECLARE cours_phy_td INT;
    DECLARE cours_elec_td INT;

    WHILE p <= 5 DO

        SELECT id_cours INTO cours_web_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%WEB-CM') LIMIT 1;
        SELECT id_cours INTO cours_bdd_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%BDD-CM') LIMIT 1;
        SELECT id_cours INTO cours_php_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%PHP-CM') LIMIT 1;
        SELECT id_cours INTO cours_js_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%JS-CM') LIMIT 1;
        SELECT id_cours INTO cours_math_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%MATH-CM') LIMIT 1;
        SELECT id_cours INTO cours_ang_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%ANG-CM') LIMIT 1;
        SELECT id_cours INTO cours_phy_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%PHY-CM') LIMIT 1;
        SELECT id_cours INTO cours_elec_cm FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%ELEC-CM') LIMIT 1;

        SELECT id_cours INTO cours_web_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%WEB-TD') LIMIT 1;
        SELECT id_cours INTO cours_bdd_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%BDD-TD') LIMIT 1;
        SELECT id_cours INTO cours_php_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%PHP-TD') LIMIT 1;
        SELECT id_cours INTO cours_js_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%JS-TD') LIMIT 1;
        SELECT id_cours INTO cours_math_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%MATH-TD') LIMIT 1;
        SELECT id_cours INTO cours_ang_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%ANG-TD') LIMIT 1;
        SELECT id_cours INTO cours_phy_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%PHY-TD') LIMIT 1;
        SELECT id_cours INTO cours_elec_td FROM cours WHERE code_cours LIKE CONCAT('%ING', p, '%ELEC-TD') LIMIT 1;

        SET semaine = 0;

        WHILE semaine < 4 DO

            SET base_date = DATE_ADD('2026-06-01', INTERVAL semaine WEEK);

            INSERT INTO seances (id_cours, id_salle, id_amphi, date_seance, heure_debut, heure_fin, code_presence)
            SELECT cours_web_cm, 16, id_amphi,
            DATE_ADD(base_date, INTERVAL (p - 1) DAY),
            '08:00:00',
            '10:00:00',
            CONCAT('WEB', p, semaine, id_amphi)
            FROM amphis
            WHERE id_promotion = p;

            INSERT INTO seances (id_cours, id_salle, id_amphi, date_seance, heure_debut, heure_fin, code_presence)
            SELECT cours_bdd_cm, 17, id_amphi,
            DATE_ADD(base_date, INTERVAL (p - 1) DAY),
            '10:00:00',
            '12:00:00',
            CONCAT('BDD', p, semaine, id_amphi)
            FROM amphis
            WHERE id_promotion = p;

            INSERT INTO seances (id_cours, id_salle, id_amphi, date_seance, heure_debut, heure_fin, code_presence)
            SELECT cours_php_cm, 18, id_amphi,
            DATE_ADD(base_date, INTERVAL (p - 1) DAY),
            '14:00:00',
            '16:00:00',
            CONCAT('PHP', p, semaine, id_amphi)
            FROM amphis
            WHERE id_promotion = p;

            INSERT INTO seances (id_cours, id_salle, id_amphi, date_seance, heure_debut, heure_fin, code_presence)
            SELECT cours_js_cm, 19, id_amphi,
            DATE_ADD(base_date, INTERVAL (p - 1) DAY),
            '16:00:00',
            '18:00:00',
            CONCAT('JS', p, semaine, id_amphi)
            FROM amphis
            WHERE id_promotion = p;

            SET g = 1;

            WHILE g <= 9 DO

                SELECT id_groupe INTO groupe_id
                FROM groupes
                WHERE id_promotion = p
                AND nom_groupe = CONCAT('Groupe ', g);

                INSERT INTO seances (
                    id_cours,
                    id_salle,
                    id_groupe,
                    date_seance,
                    heure_debut,
                    heure_fin,
                    code_presence
                ) VALUES

                (cours_web_td, g, groupe_id, DATE_ADD(base_date, INTERVAL 1 DAY), '08:00:00', '10:00:00', CONCAT('TDW', p, semaine, g)),
                (cours_bdd_td, g + 1, groupe_id, DATE_ADD(base_date, INTERVAL 2 DAY), '10:00:00', '12:00:00', CONCAT('TDB', p, semaine, g)),
                (cours_php_td, g + 2, groupe_id, DATE_ADD(base_date, INTERVAL 3 DAY), '14:00:00', '16:00:00', CONCAT('TDP', p, semaine, g)),
                (cours_js_td, g + 3, groupe_id, DATE_ADD(base_date, INTERVAL 4 DAY), '16:00:00', '18:00:00', CONCAT('TDJ', p, semaine, g)),
                (cours_math_td, g + 4, groupe_id, DATE_ADD(base_date, INTERVAL 1 DAY), '10:00:00', '12:00:00', CONCAT('TDM', p, semaine, g)),
                (cours_ang_td, g + 5, groupe_id, DATE_ADD(base_date, INTERVAL 2 DAY), '14:00:00', '16:00:00', CONCAT('TDA', p, semaine, g)),
                (cours_phy_td, g + 6, groupe_id, DATE_ADD(base_date, INTERVAL 3 DAY), '08:00:00', '10:00:00', CONCAT('TDPH', p, semaine, g)),
                (cours_elec_td, g + 7, groupe_id, DATE_ADD(base_date, INTERVAL 4 DAY), '10:00:00', '12:00:00', CONCAT('TDE', p, semaine, g));

                SET g = g + 1;

            END WHILE;

            SET semaine = semaine + 1;

        END WHILE;

        SET p = p + 1;

    END WHILE;

END//

DELIMITER ;

CALL generer_edt();
DROP PROCEDURE generer_edt;

INSERT INTO presences (
    id_etudiant,
    id_seance,
    statut
)
SELECT
    e.id_etudiant,
    s.id_seance,
    'absent'
FROM etudiants e
JOIN groupes g
ON e.id_groupe = g.id_groupe
JOIN seances s
ON s.id_groupe = g.id_groupe
OR s.id_amphi = g.id_amphi;

INSERT INTO notes (
    id_etudiant,
    id_cours,
    note,
    verrouille,
    type_evaluation,
    coefficient_note
)
SELECT
    e.id_etudiant,
    c.id_cours,
    ROUND(8 + RAND() * 10, 1),
    0,
    'CC',
    0.25
FROM etudiants e
JOIN cours c
ON c.id_promotion = e.id_promotion
WHERE c.type_cours = 'CM';

INSERT INTO notes (
    id_etudiant,
    id_cours,
    note,
    verrouille,
    type_evaluation,
    coefficient_note
)
SELECT
    e.id_etudiant,
    c.id_cours,
    ROUND(7 + RAND() * 11, 1),
    0,
    'EXAMEN',
    0.75
FROM etudiants e
JOIN cours c
ON c.id_promotion = e.id_promotion
WHERE c.type_cours = 'CM';