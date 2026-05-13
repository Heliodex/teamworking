INSERT INTO
	product (name, description, price, stock)
VALUES
	(?, ?, ?, ?) RETURNING id;