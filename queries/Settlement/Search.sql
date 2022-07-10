SELECT s.id, s.name, COUNT(*) OVER() AS total
FROM settlement s
	     INNER JOIN settlement_member sm ON s.id = sm.settlement_id
WHERE sm.user_id = :user_id
LIMIT :offset,:length
