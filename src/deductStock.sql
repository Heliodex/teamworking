UPDATE product
SET
	stock = stock - (
		SELECT
			IFNULL (SUM(quantity), 0)
		FROM
			purchase
		WHERE
			productId = product.id
			AND userId = ?
			AND completed IS NULL
	);