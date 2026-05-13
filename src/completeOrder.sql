UPDATE purchase
SET
	completed = current_timestamp,
	discount = ?
WHERE
	userId = ?
	AND completed IS NULL;