-- COMP2245 Lab 4: the "catalogue" database.
--
-- Running this file deletes the catalogue database and builds it again from
-- scratch, so you can run it again at any time to reset it. Run it as root:
--
--   mysql -u root -p < catalogue.sql
--
-- The books are real, and each year is the year the book was first
-- published. The library and the number of copies are made up.

DROP DATABASE IF EXISTS catalogue;
CREATE DATABASE catalogue CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE catalogue;

CREATE TABLE subjects (
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE
);

INSERT INTO subjects (id, name) VALUES
  (1, 'Fiction'),
  (2, 'Poetry'),
  (3, 'History'),
  (4, 'Computing');

-- copies: how many copies the library owns
-- on_loan: how many of them are borrowed at the moment
CREATE TABLE books (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(200) NOT NULL,
  author     VARCHAR(120) NOT NULL,
  year       SMALLINT NOT NULL,
  subject_id INT UNSIGNED NOT NULL,
  copies     SMALLINT NOT NULL,
  on_loan    SMALLINT NOT NULL DEFAULT 0,
  FOREIGN KEY (subject_id) REFERENCES subjects(id),
  CHECK (on_loan BETWEEN 0 AND copies)
);

INSERT INTO books (title, author, year, subject_id, copies, on_loan) VALUES
  ('A Brief History of Seven Killings',     'Marlon James',       2014, 1, 3, 3),
  ('The Book of Night Women',               'Marlon James',       2009, 1, 2, 1),
  ('Wide Sargasso Sea',                     'Jean Rhys',          1966, 1, 3, 1),
  ('Miguel Street',                         'V. S. Naipaul',      1959, 1, 2, 0),
  ('A House for Mr Biswas',                 'V. S. Naipaul',      1961, 1, 2, 2),
  ('The Lonely Londoners',                  'Sam Selvon',         1956, 1, 2, 1),
  ('In the Castle of My Skin',              'George Lamming',     1953, 1, 1, 0),
  ('Annie John',                            'Jamaica Kincaid',    1985, 1, 2, 0),
  ('Breath, Eyes, Memory',                  'Edwidge Danticat',   1994, 1, 2, 1),
  ('The Brief Wondrous Life of Oscar Wao',  'Junot Díaz',         2007, 1, 2, 2),
  ('Here Comes the Sun',                    'Nicole Dennis-Benn', 2016, 1, 2, 1),
  ('The Dragon Can''t Dance',               'Earl Lovelace',      1979, 1, 1, 0),
  ('Crick Crack, Monkey',                   'Merle Hodge',        1970, 1, 3, 2),
  ('Augustown',                             'Kei Miller',         2016, 1, 2, 0),
  ('Small Island',                          'Andrea Levy',        2004, 1, 1, 1),
  ('Brother Man',                           'Roger Mais',         1954, 1, 1, 0),
  ('Myal',                                  'Erna Brodber',       1988, 1, 1, 0),
  ('Omeros',                                'Derek Walcott',      1990, 2, 2, 1),
  ('The Arrivants',                         'Kamau Brathwaite',   1973, 2, 1, 0),
  ('Jamaica Labrish',                       'Louise Bennett',     1966, 2, 2, 2),
  ('The Cartographer Tries to Map a Way to Zion', 'Kei Miller',   2014, 2, 1, 0),
  ('I Is a Long Memoried Woman',            'Grace Nichols',      1983, 2, 1, 1),
  ('The Black Jacobins',                    'C. L. R. James',     1938, 3, 2, 0),
  ('Capitalism and Slavery',                'Eric Williams',      1944, 3, 3, 1),
  ('How Europe Underdeveloped Africa',      'Walter Rodney',      1972, 3, 2, 2),
  ('Beyond a Boundary',                     'C. L. R. James',     1963, 3, 1, 0),
  ('The Wretched of the Earth',             'Frantz Fanon',       1961, 3, 2, 1),
  ('Eloquent JavaScript',                   'Marijn Haverbeke',   2011, 4, 4, 3),
  ('Don''t Make Me Think',                  'Steve Krug',         2000, 4, 2, 0),
  ('The Pragmatic Programmer',              'Andrew Hunt and David Thomas', 1999, 4, 2, 2),
  ('HTML & CSS: Design and Build Websites', 'Jon Duckett',        2011, 4, 3, 1),
  ('Inclusive Design Patterns',             'Heydon Pickering',   2016, 4, 1, 0),
  ('Designing Data-Intensive Applications', 'Martin Kleppmann',   2017, 4, 2, 1),
  ('Code: The Hidden Language of Computer Hardware and Software', 'Charles Petzold', 1999, 4, 1, 1);

-- ---------------------------------------------------------------------------
-- The MySQL account your PHP connects with. The page only reads, so this
-- account can only SELECT. If the account already exists from the Lecture 8
-- database, this keeps its password and adds the new permission.
-- ---------------------------------------------------------------------------

CREATE USER IF NOT EXISTS 'comp2245'@'localhost' IDENTIFIED BY 'comp2245-demo';
GRANT SELECT ON catalogue.* TO 'comp2245'@'localhost';
