SELECT
	p.id,
	p.created,
	p.name,
	p.description,
	p.price,
	p.stock,
	EXISTS (
		SELECT
			1
		FROM
			purchase
		WHERE
			userId = ?
			AND productId = p.id
			AND completed IS NULL
	) AS inCart
FROM
	product p;