INSERT INTO inventories (name, quantity) VALUES
("A", 2),
("B", 3),
("C", 1),
("D", 0),
("E", 0);

UPDATE inventories SET quantity = 2 WHERE name = "A";
UPDATE inventories SET quantity = 3 WHERE name = "B";
UPDATE inventories SET quantity = 1 WHERE name = "C";
UPDATE inventories SET quantity = 0 WHERE name = "D";
UPDATE inventories SET quantity = 0 WHERE name = "E";
