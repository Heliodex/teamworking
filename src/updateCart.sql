UPDATE purchase
SET
	quantity = ?
WHERE
	userId = ?
	AND productId = ?
	AND completed = 0;