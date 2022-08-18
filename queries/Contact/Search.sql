SELECT u.id, 'user' AS type, u.first_name, u.last_name
FROM user_internal_contact uic
	     INNER JOIN user u ON uic.user_target = u.id
WHERE uic.user_source = :user_id
UNION ALL
SELECT ec.id, 'external_contact' AS type, ec.first_name, ec.last_name
FROM user_external_contact uec
	     INNER JOIN external_contact ec ON uec.external_contact_id = ec.id
WHERE uec.user_id = :user_id
ORDER BY first_name, last_name
