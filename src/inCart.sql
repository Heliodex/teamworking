SELECT
	1
FROM
	purchase
WHERE
	userId = ?
	AND productId = ?
	AND completed IS NULL;