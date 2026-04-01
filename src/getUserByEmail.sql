SELECT
	id,
	created,
	forename,
	surname,
	street,
	town,
	postcode,
	category,
	password
FROM
	user
WHERE
	email = ?;