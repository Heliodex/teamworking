UPDATE purchase
SET
	completed = CURRENT_TIMESTAMP
WHERE
	userId = ?
	AND completed IS NULL;