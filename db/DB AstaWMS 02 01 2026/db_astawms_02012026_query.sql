SELECT
	*
FROM
	ppl_presence_detail
JOIN
	ppl_presence ON ppl_presence_detail.idppl_presence = ppl_presence.idppl_presence
WHERE
	ppl_presence.month = '12'
AND
	ppl_presence.year = '2025'
AND
	ppl_presence_detail.no_excel = '30'
LIMIT
	1;

SELECT
	*
FROM
	ppl_employee
WHERE
	ppl_employee.place = 'Gudang'
AND
	ppl_employee.no_excel = '30';
	
SELECT
	ppl_presence_detail.*, ppl_presence.place
FROM
	ppl_presence_detail
JOIN
	ppl_presence ON ppl_presence.idppl_presence = ppl_presence_detail.idppl_presence
WHERE
	ppl_presence_detail.date BETWEEN '2025-12-01' AND '2025-12-31'
AND
	ppl_presence.place = 'Gudang'
AND
	ppl_presence_detail.no_excel = '30';