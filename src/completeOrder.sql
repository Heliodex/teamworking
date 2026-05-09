UPDATE
	purchase
SET
	completed = true
WHERE
	userId = ?
	AND completed = false;