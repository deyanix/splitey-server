SELECT sm.id AS memberId, IFNULL(s1.sum, 0) - IFNULL(s2.sum, 0) as balance
FROM settlement_member sm
         LEFT JOIN (
    SELECT t.paying_member_id AS member_id, SUM(td.amount) AS sum
    FROM transfer t
             LEFT JOIN transfer_division td on t.id = td.transfer_id
    GROUP BY t.paying_member_id
) s1 on sm.id = s1.member_id
         LEFT JOIN (
    SELECT td.member_id AS member_id, SUM(td.amount) AS sum
    FROM transfer_division td
    GROUP BY td.member_id
) s2 on sm.id = s2.member_id
WHERE sm.settlement_id = :settlement_id;
