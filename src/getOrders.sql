SELECT
	p.id,
	p.created,
	p.name,
	p.description,
	p.price,
	p.stock,
	pu.quantity,
	pu.completed,
	pu.discount
FROM
	product p
	INNER JOIN purchase pu ON p.id = pu.productId
WHERE
	pu.userId = ?
	AND pu.completed IS NOT NULL;