<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('number_pending_verification')) {
	function number_pending_verification()
	{
		$CI = &get_instance();
		$query = "
	SELECT 
		'INSTOCK' AS tipe,
		i.instock_code AS kode_transaksi,
		i.tgl_terima AS tanggal,
		i.jam_terima AS jam,
		i.kategori,
		i.user,
		g.nama_gudang,
		i.status_verification
	FROM instock i
	LEFT JOIN gudang g ON g.idgudang = i.idgudang
	WHERE i.status_verification = 0

	UNION ALL

	SELECT 
		'OUTSTOCK' AS tipe,
		o.outstock_code AS kode_transaksi,
		o.tgl_keluar AS tanggal,
		o.jam_keluar AS jam,
		o.kategori,
		o.user,
		g.nama_gudang,
		o.status_verification
	FROM outstock o
	LEFT JOIN gudang g ON g.idgudang = o.idgudang
	WHERE o.status_verification = 0

	ORDER BY tanggal DESC, jam DESC
";
		return $CI->db->query($query)->num_rows();
	}
}

if (!function_exists('number_pending_verification_delivery_note')) {
	function number_pending_verification_delivery_note()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 1);
		$CI->db->where('delivery_note_log.progress', 1);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

if (!function_exists('number_pending_verification_delivery_manual')) {
	function number_pending_verification_delivery_manual()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 2);
		$CI->db->where('delivery_note_log.progress', 1);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

if (!function_exists('number_pending_verification_delivery_mutasi')) {
	function number_pending_verification_delivery_mutasi()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 3);
		$CI->db->where('delivery_note_log.progress', 1);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

if (!function_exists('number_pending_validasi_delivery_note')) {
	function number_pending_validasi_delivery_note()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 1);
		$CI->db->where('delivery_note_log.progress', 2);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

if (!function_exists('number_pending_validasi_delivery_manual')) {
	function number_pending_validasi_delivery_manual()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 2);
		$CI->db->where('delivery_note_log.progress', 2);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

if (!function_exists('number_pending_validasi_delivery_mutasi')) {
	function number_pending_validasi_delivery_mutasi()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 3);
		$CI->db->where('delivery_note_log.progress', 2);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

if (!function_exists('number_pending_final_delivery_note')) {
	function number_pending_final_delivery_note()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 1);
		$CI->db->where('delivery_note_log.progress', 3);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

if (!function_exists('number_pending_final_delivery_manual')) {
	function number_pending_final_delivery_manual()
	{
		$CI = &get_instance();

		$CI->db->select('
            delivery_note.iddelivery_note,
            delivery_note.no_manual,
            delivery_note.send_date,
            user_input.full_name as user_input,
            delivery_note.created_date,
            delivery_note_log.progress,
            delivery_note.foto
        ');
		$CI->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

		$CI->db->join(
			'(SELECT t1.* FROM delivery_note_log t1
              JOIN (
                SELECT iddelivery_note, MAX(created_date) as max_date
                FROM delivery_note_log
                GROUP BY iddelivery_note
              ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
            ) delivery_note_log',
			'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
			'left'
		);

		$CI->db->where('delivery_note.status', 1);
		$CI->db->where('delivery_note.kategori', 2);
		$CI->db->where('delivery_note_log.progress', 3);

		$delivery = $CI->db->get('delivery_note')->result();

		return count($delivery);
	}
}

function total_pending_delivery()
{
	return number_pending_verification_delivery_note() + number_pending_validasi_delivery_note() + number_pending_final_delivery_note() + number_pending_verification_delivery_manual() + number_pending_validasi_delivery_manual() + number_pending_final_delivery_manual() + number_pending_verification_delivery_mutasi() + number_pending_validasi_delivery_mutasi();
}

function total_pending_verification_all()
{
	return number_pending_verification() + number_pending_verification_delivery_note() + number_pending_validasi_delivery_note();
}
