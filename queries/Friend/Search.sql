SELECT
	u.id AS user_id,
	NULL AS external_friend_id,
	u.username AS username,
	u.first_name AS first_name,
	u.last_name AS last_name
FROM user u
    INNER JOIN friend f ON
		(u.id = f.user1_id AND f.user2_id = :user_id) OR
		(u.id = f.user2_id AND f.user1_id = :user_id)
UNION
SELECT
	NULL AS user_id,
	ef.id AS external_friend_id,
	NULL AS username,
	ef.first_name AS first_name,
	ef.last_name AS last_name
FROM external_friend ef
WHERE ef.owner_id = :user_id
ORDER BY first_name, last_name
