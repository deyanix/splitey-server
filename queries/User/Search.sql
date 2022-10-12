SELECT u.*
FROM user AS u
WHERE
    u.id != :currentUserId AND (
		CONCAT(u.first_name, ' ', u.last_name) LIKE :name OR
		CONCAT(u.last_name, ' ', u.first_name) LIKE :name OR
		u.first_name LIKE :name OR
		u.last_name LIKE :name OR
		u.username LIKE :name
	)
LIMIT 5

