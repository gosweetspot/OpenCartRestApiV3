<?php

class ModelAccountGssOrder extends Model {

	public function getOrderByStatusId($data) {
		$sql = "SELECT  * FROM `" . DB_PREFIX . "order` ";

		if (!empty($data['statusids'])) {
		$sql .= "WHERE find_in_set(order_status_id, '" . $data['statusids'] . "') ";
	} else {
		$sql .= "WHERE 1 = 1 ";
	}

		if (!empty($data['DateTime'])) {
			$sql .= " AND DATE(date_modified) > DATE('" . $this->db->escape($data['DateTime']) . "')";
		}


		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}



		$order_query = $this->db->query($sql);


		if ($order_query->num_rows) {

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int) $order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int) $order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			foreach ($order_query->rows as $key => $value) {
				$order_query->rows[$key]['shipping_iso_code_2'] = $shipping_iso_code_2;
				$order_query->rows[$key]['shipping_iso_code_3'] = $shipping_iso_code_3;
				$order_query->rows[$key]['shipping_zone_code'] = $shipping_zone_code;
				
				$order_query->rows[$key]['order_products'] = $this->getOrderProducts($order_query->rows[$key]['order_id']);
			}   //->
			return $order_query->rows;
		} else {
			return false;
		}
	}
	
	    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
        return $query->rows;//$query->rows;
    }

}

?>
