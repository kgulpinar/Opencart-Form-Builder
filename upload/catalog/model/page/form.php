<?php
class ModelPageForm extends Model {
	public function getPageForm($page_form_id) {
		$forms_data = array();
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "page_form p LEFT JOIN " . DB_PREFIX . "page_form_description pd ON (p.page_form_id = pd.page_form_id) LEFT JOIN " . DB_PREFIX . "page_form_store p2s ON (p.page_form_id = p2s.page_form_id) WHERE p.page_form_id = '" . (int)$page_form_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.status = '1'";

		if(!$this->customer->isLogged()) {
			$sql .= " AND p.show_guest = '1'";
		}

		$row = $this->db->query($sql)->row;

		if($row) {
			// Customer Group
			$find_mygroup = false;
			if($this->customer->isLogged()) {
				// This is Customer
				$customer_group_id = $this->customer->getGroupId();
				$customer_group_query = $this->db->query("SELECT * FROM ". DB_PREFIX ."page_form_customer_group WHERE page_form_id = '". (int)$row['page_form_id'] ."' AND customer_group_id = '". (int)$customer_group_id ."'");

				if($customer_group_query->num_rows) {
					$find_mygroup = true;
				}
			} else{
				// This is Guest
				$find_mygroup = true;
			}

			if($find_mygroup) {
				$forms_data = $row;
			}
		}

		return $forms_data;
	}

	public function getPageForms() {
		$forms_data = array();
		$sql = "SELECT * FROM " . DB_PREFIX . "page_form p LEFT JOIN " . DB_PREFIX . "page_form_description pd ON (p.page_form_id = pd.page_form_id) LEFT JOIN " . DB_PREFIX . "page_form_store p2s ON (p.page_form_id = p2s.page_form_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.status = '1'";

		if(!$this->customer->isLogged()) {
			$sql .= " AND p.show_guest = '1'";
		}

		 $sql .= " ORDER BY p.sort_order, LCASE(pd.title) ASC";

		$query = $this->db->query($sql);

		foreach($query->rows as $row) {
			// Customer Group
			$find_mygroup = false;
			if($this->customer->isLogged()) {
				// This is Customer
				$customer_group_id = $this->customer->getGroupId();
				$customer_group_query = $this->db->query("SELECT * FROM ". DB_PREFIX ."page_form_customer_group WHERE page_form_id = '". (int)$row['page_form_id'] ."' AND customer_group_id = '". (int)$customer_group_id ."'");

				if($customer_group_query->num_rows) {
					$find_mygroup = true;
				}
			} else{
				// This is Guest
				$find_mygroup = true;
			}

			if($find_mygroup) {
				$forms_data[] = $row;
			}

		}

		return $forms_data;
	}

	public function getPageFormOptions($page_form_id) {
		$page_form_option_data = array();

		$page_form_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "page_form_option pfo LEFT JOIN " . DB_PREFIX . "page_form_option_description pfod ON (pfo.page_form_option_id = pfod.page_form_option_id) WHERE pfo.page_form_id = '" . (int)$page_form_id . "' AND pfod.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pfo.status ORDER BY pfo.sort_order ASC");

		foreach ($page_form_option_query->rows as $page_form_option) {
			
			$page_form_option_value_data = array();

			$page_form_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "page_form_option_value pfov LEFT JOIN " . DB_PREFIX . "page_form_option_value_description pfovd ON (pfov.page_form_option_value_id = pfovd.page_form_option_value_id) WHERE pfov.page_form_option_id = '" . (int)$page_form_option['page_form_option_id'] . "' AND pfovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pfov.sort_order ASC");

			foreach ($page_form_option_value_query->rows as $page_form_option_value) {
				$page_form_option_value_data[] = array(
					'page_form_option_value_id'      => $page_form_option_value['page_form_option_value_id'],
					'name'                    	=> $page_form_option_value['name'],
				);
			}
	
			$page_form_option_data[] = array(
				'page_form_option_id'    => $page_form_option['page_form_option_id'],
				'page_form_option_value' => $page_form_option_value_data,
				'field_name'             => $page_form_option['field_name'],
				'field_help'             => $page_form_option['field_help'],
				'type'                	 => $page_form_option['type'],
				'field_value'            => $page_form_option['field_value'],
				'field_placeholder'      => $page_form_option['field_placeholder'],
				'field_error'      		 => $page_form_option['field_error'],
				'required'             	 => $page_form_option['required']
			);
		}

		return $page_form_option_data;
	}

	public function getPageFormOptionsCountry($page_form_id) {
		$query = $this->db->query("SELECT count(*) as total_country_exists FROM " . DB_PREFIX . "page_form_option pfo WHERE pfo.page_form_id = '" . (int)$page_form_id . "' AND pfo.type = 'country'");

		return $query->row['total_country_exists'];
	}

	public function getPageRequestEmailByPageFormID($email, $page_form_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "page_request_option` WHERE LOWER(`value`) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND `page_form_id` = '" . (int)$page_form_id . "' AND (`type` = 'email' OR `type` = 'email_exists')");

		return $query->row;
	}

	public function getPageFormByInformation($information_id) {
		$forms_data = array();
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "page_form p LEFT JOIN " . DB_PREFIX . "page_form_description pd ON (p.page_form_id = pd.page_form_id) LEFT JOIN " . DB_PREFIX . "page_form_store p2s ON (p.page_form_id = p2s.page_form_id) LEFT JOIN " . DB_PREFIX . "page_form_information p2i ON (p.page_form_id = p2i.page_form_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2i.information_id = '" . (int)$information_id . "' AND p.status = '1'";

		if(!$this->customer->isLogged()) {
			$sql .= " AND p.show_guest = '1'";
		}

		$row = $this->db->query($sql)->row;

		if($row) {
			// Customer Group
			$find_mygroup = false;
			if($this->customer->isLogged()) {
				// This is Customer
				$customer_group_id = $this->customer->getGroupId();
				$customer_group_query = $this->db->query("SELECT * FROM ". DB_PREFIX ."page_form_customer_group WHERE page_form_id = '". (int)$row['page_form_id'] ."' AND customer_group_id = '". (int)$customer_group_id ."'");

				if($customer_group_query->num_rows) {
					$find_mygroup = true;
				}
			} else{
				// This is Guest
				$find_mygroup = true;
			}

			if($find_mygroup) {
				$forms_data = $row;
			}
		}

		return $forms_data;
	}
}