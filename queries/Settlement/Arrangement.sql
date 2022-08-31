SELECT t.paying_member_id AS creditor_id, td.member_id as debtor_id, SUM(td.amount) AS amount
FROM transfer t
	     INNER JOIN settlement_member sm on t.paying_member_id = sm.id
	     LEFT JOIN transfer_division td on t.id = td.transfer_id
WHERE sm.settlement_id = :settlement_id AND t.paying_member_id != td.member_id
GROUP BY t.paying_member_id, td.member_id
