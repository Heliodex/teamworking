DELETE FROM purchase
WHERE
	userId = ?
	AND productId = ?
	AND completed = false;