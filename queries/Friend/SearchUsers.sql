SELECT
    u.*,
    (f.user1_id IS NOT NULL OR f.user2_id IS NOT NULL) AS is_friend,
    (fi.recipient_id IS NOT NULL AND fi.recipient_id = :currentUserId) AS is_received_invitation,
    (fi.sender_id IS NOT NULL AND fi.sender_id = :currentUserId) AS is_sent_invitation
FROM user AS u
LEFT JOIN friend f
    ON (u.id = f.user1_id AND f.user2_id = :currentUserId) OR
       (u.id = f.user2_id AND f.user1_id = :currentUserId)
LEFT JOIN friend_invitation fi
      ON ((u.id = fi.sender_id AND fi.recipient_id = :currentUserId) OR
          (u.id = fi.recipient_id AND fi.sender_id = :currentUserId)) AND
         fi.active = 1
WHERE
	u.id != :currentUserId AND (
		CONCAT(u.first_name, ' ', u.last_name) LIKE :name OR
		CONCAT(u.last_name, ' ', u.first_name) LIKE :name OR
		u.first_name LIKE :name OR
		u.last_name LIKE :name OR
		u.username LIKE :name
	)
LIMIT 5
