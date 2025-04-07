#book
INSERT INTO book (isbn, Title, Publisher, Description, Release_year, Page_nr, Series_ID, Image_URL) VALUES ('9780553805444','The World of Ice & Fire','Random House Worlds',NULL,2014,326,NULL,'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1400360220i/17345242.jpg');

#author
INSERT INTO author (name) SELECT 'George R.R. Martin' AS name WHERE NOT EXISTS (SELECT 1 FROM author a WHERE a.name = 'George R.R. Martin');
INSERT INTO author (name) SELECT 'Elio M. García Jr.' AS name WHERE NOT EXISTS (SELECT 1 FROM author a WHERE a.name = 'Elio M. García Jr.');
INSERT INTO author (name) SELECT 'Linda Antonsson' AS name WHERE NOT EXISTS (SELECT 1 FROM author a WHERE a.name = 'Linda Antonsson');

#category
INSERT INTO category (name) SELECT 'Fiction' AS name WHERE NOT EXISTS (SELECT 1 FROM category c WHERE c.name = 'Fiction');
INSERT INTO category (name) SELECT 'Dragons' AS name WHERE NOT EXISTS (SELECT 1 FROM category c WHERE c.name = 'Dragons');
INSERT INTO category (name) SELECT 'High Fantasy' AS name WHERE NOT EXISTS (SELECT 1 FROM category c WHERE c.name = 'High Fantasy');
INSERT INTO category (name) SELECT 'Fantasy' AS name WHERE NOT EXISTS (SELECT 1 FROM category c WHERE c.name = 'Fantasy');
INSERT INTO category (name) SELECT 'Audiobook' AS name WHERE NOT EXISTS (SELECT 1 FROM category c WHERE c.name = 'Audiobook');
INSERT INTO category (name) SELECT 'Epic Fantasy' AS name WHERE NOT EXISTS (SELECT 1 FROM category c WHERE c.name = 'Epic Fantasy');
INSERT INTO category (name) SELECT 'Reference' AS name WHERE NOT EXISTS (SELECT 1 FROM category c WHERE c.name = 'Reference');

#belongs
INSERT INTO belongs (isbn, category_id) SELECT b.isbn, c.category_id FROM book b JOIN category c ON c.name IN ('Fantasy', 'Fiction', 'Epic Fantasy', 'High Fantasy', 'Audiobook', 'Dragons', 'Reference') WHERE b.title = 'The World of Ice & Fire';

#series
INSERT INTO book_series (name) SELECT 'A Song of Ice and Fire' AS name WHERE NOT EXISTS (SELECT 1 FROM book_series bs WHERE bs.name = 'A Song of Ice and Fire');

#book update
UPDATE book SET series_id = (SELECT series_id FROM book_series WHERE name = 'A Song of Ice and Fire') WHERE title = 'The World of Ice & Fire';

#wrote
INSERT INTO wrote (isbn, author_id) SELECT b.isbn, a.author_id FROM book b JOIN author a ON a.name = 'George R.R. Martin' WHERE b.title = 'The World of Ice & Fire';
INSERT INTO wrote (isbn, author_id) SELECT b.isbn, a.author_id FROM book b JOIN author a ON a.name = 'Elio M. García Jr.' WHERE b.title = 'The World of Ice & Fire';
INSERT INTO wrote (isbn, author_id) SELECT b.isbn, a.author_id FROM book b JOIN author a ON a.name = 'Linda Antonsson' WHERE b.title = 'The World of Ice & Fire';

#copy
INSERT INTO copy (Copy_Condition, Shelf_Position, ISBN) VALUES ('Good', '4-6', '9780553805444');
INSERT INTO copy (Copy_Condition, Shelf_Position, ISBN) VALUES ('New', '4-6', '9780553805444');
INSERT INTO copy (Copy_Condition, Shelf_Position, ISBN) VALUES ('Fair', '4-6', '9780553805444');
