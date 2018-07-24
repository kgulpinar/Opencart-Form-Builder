<?php
class ModelPagePageForm extends Model {
	public function addPageForm($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "page_form` SET show_guest = '" . (int)$data['show_guest'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', customer_email_status = '" . (int)$data['customer_email_status'] . "', admin_email_status = '" . (int)$data['admin_email_status'] . "', admin_email = '" . $this->db->escape($data['admin_email']) . "', top = '" . (isset($data['top']) ? (int)$data['top'] : '') . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : '') . "', captcha = '" . (int)$data['captcha'] . "', css = '" . $this->db->escape($data['css']) . "'");

		$page_form_id = $this->db->getLastId();

		foreach ($data['page_form_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_description SET page_form_id = '" . (int)$page_form_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', bottom_description = '" . $this->db->escape($value['bottom_description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', customer_subject = '" . $this->db->escape($value['customer_subject']) . "', customer_message = '" . $this->db->escape($value['customer_message']) . "', admin_subject = '" . $this->db->escape($value['admin_subject']) . "', admin_message = '" . $this->db->escape($value['admin_message']) . "', success_title = '" . $this->db->escape($value['success_title']) . "', success_description = '" . $this->db->escape($value['success_description']) . "', fieldset_title = '" . $this->db->escape($value['fieldset_title']) . "', submit_button = '" . $this->db->escape($value['submit_button']) . "'");
		}

		if (isset($data['page_form_store'])) {
			foreach ($data['page_form_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_store SET page_form_id = '" . (int)$page_form_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['page_form_information'])) {
			foreach ($data['page_form_information'] as $information_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_information SET page_form_id = '" . (int)$page_form_id . "', information_id = '" . (int)$information_id . "'");
			}
		}

		if (isset($data['page_form_customer_group'])) {
			foreach ($data['page_form_customer_group'] as $customer_group_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_customer_group SET page_form_id = '" . (int)$page_form_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
			}
		}

		if (isset($data['page_form_field'])) {
			foreach ($data['page_form_field'] as $page_form_field) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option SET page_form_id = '" . (int)$page_form_id . "', required = '" . (int)$page_form_field['required'] . "', status = '" . (int)$page_form_field['status'] . "', type = '" . $this->db->escape($page_form_field['type']) . "', sort_order = '" . $this->db->escape($page_form_field['sort_order']) . "'");

				$page_form_option_id = $this->db->getLastId();

				if(isset($page_form_field['description'])) {
					foreach ($page_form_field['description'] as $language_id => $page_form_option_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option_description SET page_form_option_id = '" . (int)$page_form_option_id . "', page_form_id = '" . (int)$page_form_id . "', language_id = '" . (int)$language_id . "', field_name = '" . $this->db->escape($page_form_option_description['field_name']) . "', field_help = '" . $this->db->escape($page_form_option_description['field_help']) . "', field_error = '" . $this->db->escape($page_form_option_description['field_error']) . "', field_placeholder = '" . $this->db->escape($page_form_option_description['field_placeholder']) . "'");
					}
				}

				if (isset($page_form_field['option_value'])) {
					if ($page_form_field['type'] == 'select' || $page_form_field['type'] == 'radio' || $page_form_field['type'] == 'checkbox') {
						foreach ($page_form_field['option_value'] as $page_form_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option_value SET page_form_option_id = '" . (int)$page_form_option_id . "', page_form_id = '" . (int)$page_form_id . "', sort_order = '" . (int)$page_form_option_value['sort_order'] . "'");

							$page_form_option_value_id = $this->db->getLastId();

							if(isset($page_form_option_value['page_form_option_value_description'])) {

								foreach ($page_form_option_value['page_form_option_value_description'] as $language_id => $page_form_option_value_description) {
									$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option_value_description SET page_form_option_value_id = '" . (int)$page_form_option_value_id . "', page_form_option_id = '" . (int)$page_form_option_id . "', page_form_id = '" . (int)$page_form_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($page_form_option_value_description['name']) . "'");
								}
							}
						}
					}
				}
			}
		}

		if (isset($data['page_form_seo_url'])) {
			foreach ($data['page_form_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'page_form_id=" . (int)$page_form_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		$page_form_id = $this->db->getLastId();

		return $page_form_id;
	}

	public function editPageForm($page_form_id, $data) {
		// print_r($data); die();

		$this->db->query("UPDATE `" . DB_PREFIX . "page_form` SET show_guest = '" . (int)$data['show_guest'] . "', status = '" . $this->db->escape($data['status']) . "', sort_order = '" . (int)$data['sort_order'] . "', customer_email_status = '" . (int)$data['customer_email_status'] . "', admin_email_status = '" . (int)$data['admin_email_status'] . "', admin_email = '" . $this->db->escape($data['admin_email']) . "', top = '" . (isset($data['top']) ? (int)$data['top'] : '') . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : '') . "', captcha = '" . (int)$data['captcha'] . "', css = '" . $this->db->escape($data['css']) . "' WHERE page_form_id = '" . (int)$page_form_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "page_form_description WHERE page_form_id = '" . (int)$page_form_id . "'");

		foreach ($data['page_form_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_description SET page_form_id = '" . (int)$page_form_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', bottom_description = '" . $this->db->escape($value['bottom_description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', customer_subject = '" . $this->db->escape($value['customer_subject']) . "', customer_message = '" . $this->db->escape($value['customer_message']) . "', admin_subject = '" . $this->db->escape($value['admin_subject']) . "', admin_message = '" . $this->db->escape($value['admin_message']) . "', success_title = '" . $this->db->escape($value['success_title']) . "', success_description = '" . $this->db->escape($value['success_description']) . "', fieldset_title = '" . $this->db->escape($value['fieldset_title']) . "', submit_button = '" . $this->db->escape($value['submit_button']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "page_form_store WHERE page_form_id = '" . (int)$page_form_id . "'");

		if (isset($data['page_form_store'])) {
			foreach ($data['page_form_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_store SET page_form_id = '" . (int)$page_form_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "page_form_information WHERE page_form_id = '" . (int)$page_form_id . "'");
		
		if (isset($data['page_form_information'])) {
			foreach ($data['page_form_information'] as $information_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_information SET page_form_id = '" . (int)$page_form_id . "', information_id = '" . (int)$information_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "page_form_customer_group WHERE page_form_id = '" . (int)$page_form_id . "'");

		if (isset($data['page_form_customer_group'])) {
			foreach ($data['page_form_customer_group'] as $customer_group_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_customer_group SET page_form_id = '" . (int)$page_form_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
			}
		}


		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option` WHERE page_form_id = '" . (int)$page_form_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option_description` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option_value` WHERE page_form_id = '" . (int)$page_form_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option_value_description` WHERE page_form_id = '" . (int)$page_form_id . "'");

		if (isset($data['page_form_field'])) {
			foreach ($data['page_form_field'] as $page_form_field) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option SET page_form_option_id = '". (int)$page_form_field['page_form_option_id'] ."', page_form_id = '" . (int)$page_form_id . "', required = '" . (int)$page_form_field['required'] . "', status = '" . (int)$page_form_field['status'] . "', type = '" . $this->db->escape($page_form_field['type']) . "', sort_order = '" . $this->db->escape($page_form_field['sort_order']) . "'");

				$page_form_option_id = $this->db->getLastId();

				if(isset($page_form_field['description'])) {
					foreach ($page_form_field['description'] as $language_id => $page_form_option_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option_description SET page_form_option_id = '" . (int)$page_form_option_id . "', page_form_id = '" . (int)$page_form_id . "', language_id = '" . (int)$language_id . "', field_name = '" . $this->db->escape($page_form_option_description['field_name']) . "', field_help = '" . $this->db->escape($page_form_option_description['field_help']) . "', field_error = '" . $this->db->escape($page_form_option_description['field_error']) . "', field_placeholder = '" . $this->db->escape($page_form_option_description['field_placeholder']) . "'");
					}
				}

				if (isset($page_form_field['option_value'])) {
					if ($page_form_field['type'] == 'select' || $page_form_field['type'] == 'radio' || $page_form_field['type'] == 'checkbox') {
						foreach ($page_form_field['option_value'] as $page_form_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option_value SET page_form_option_value_id = '". (int)$page_form_option_value['page_form_option_value_id'] ."', page_form_option_id = '" . (int)$page_form_option_id . "', page_form_id = '" . (int)$page_form_id . "', sort_order = '" . (int)$page_form_option_value['sort_order'] . "'");

							$page_form_option_value_id = $this->db->getLastId();

							if(isset($page_form_option_value['page_form_option_value_description'])) {

								foreach ($page_form_option_value['page_form_option_value_description'] as $language_id => $page_form_option_value_description) {
									$this->db->query("INSERT INTO " . DB_PREFIX . "page_form_option_value_description SET page_form_option_value_id = '" . (int)$page_form_option_value_id . "', page_form_option_id = '" . (int)$page_form_option_id . "', page_form_id = '" . (int)$page_form_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($page_form_option_value_description['name']) . "'");
								}
							}
						}
					}
				}
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'page_form_id=" . (int)$page_form_id . "'");
		
		if (isset($data['page_form_seo_url'])) {
			foreach ($data['page_form_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'page_form_id=" . (int)$page_form_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
	}

	public function deletePageForm($page_form_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_customer_group` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_description` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option_description` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option_value` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_option_value_description` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'page_form_id=" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_store` WHERE page_form_id = '" . (int)$page_form_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "page_form_information` WHERE page_form_id = '" . (int)$page_form_id . "'");
	}

	public function getPageForm($page_form_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "page_form` o WHERE o.page_form_id = '" . (int)$page_form_id . "'");

		return $query->row;
	}

	public function getPageForms($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "page_form` p LEFT JOIN " . DB_PREFIX . "page_form_description pd ON (p.page_form_id = pd.page_form_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_title'])) {
			$sql .= " AND pd.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
		}

		$sort_data = array(
			'pd.title',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.title";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getPageFormDescriptions($page_form_id) {
		$page_form_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "page_form_description WHERE page_form_id = '" . (int)$page_form_id . "'");

		foreach ($query->rows as $result) {
			$page_form_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'description'      => $result['description'],
				'bottom_description'      => $result['bottom_description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'admin_subject'     	=> $result['admin_subject'],
				'admin_message'     	=> $result['admin_message'],
				'customer_subject'    	=> $result['customer_subject'],
				'customer_message'     	=> $result['customer_message'],
				'success_title'     	=> $result['success_title'],
				'success_description' 	=> $result['success_description'],
				'fieldset_title' 	=> $result['fieldset_title'],
				'submit_button' 	=> $result['submit_button'],
			);
		}

		return $page_form_description_data;
	}

	public function getPageFormSeoUrls($page_form_id) {
		$page_form_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'page_form_id=" . (int)$page_form_id . "'");

		foreach ($query->rows as $result) {
			$page_form_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $page_form_seo_url_data;
	}

	public function getTotalPageForms() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "page_form`");

		return $query->row['total'];
	}

	public function getPageFormStores($page_form_id) {
		$page_form_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "page_form_store WHERE page_form_id = '" . (int)$page_form_id . "'");

		foreach ($query->rows as $result) {
			$page_form_store_data[] = $result['store_id'];
		}

		return $page_form_store_data;
	}

	public function getPageFormInformations($page_form_id) {
		$page_form_information_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "page_form_information WHERE page_form_id = '" . (int)$page_form_id . "'");

		foreach ($query->rows as $result) {
			$page_form_information_data[] = $result['information_id'];
		}

		return $page_form_information_data;
	}

	public function getPageFormCustomerGroups($page_form_id) {
		$page_form_customer_group_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "page_form_customer_group WHERE page_form_id = '" . (int)$page_form_id . "'");

		foreach ($query->rows as $result) {
			$page_form_customer_group_data[] = $result['customer_group_id'];
		}

		return $page_form_customer_group_data;
	}

	public function getPageFormOptions($page_form_id) {
		$page_form_option_data = array();

		$page_form_option_query = $this->db->query("SELECT *, pfo.sort_order as sort_order FROM `" . DB_PREFIX . "page_form_option` pfo LEFT JOIN `" . DB_PREFIX . "page_form_option_description` pfod ON (pfo.page_form_option_id = pfod.page_form_option_id) WHERE pfo.page_form_id = '" . (int)$page_form_id . "' AND pfod.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pfo.sort_order ASC");

		foreach ($page_form_option_query->rows as $page_form_option) {
			$page_form_description_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "page_form_option_description` WHERE page_form_id = '" . (int)$page_form_option['page_form_id'] . "' AND  page_form_option_id = '". (int)$page_form_option['page_form_option_id']  ."'");

			$page_form_description_data = array();
			foreach ($page_form_description_query->rows as $page_form_description_value) {
				$page_form_description_data[$page_form_description_value['language_id']] = array(
					'field_name' 	 	=> $page_form_description_value['field_name'],
					'field_help' 	 	=> $page_form_description_value['field_help'],
					'field_placeholder' => $page_form_description_value['field_placeholder'],
					'field_error'    	=> $page_form_description_value['field_error'],
				);
			}

			$page_form_option_value_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "page_form_option_value` pfov WHERE pfov.page_form_id = '" . (int)$page_form_id . "' AND pfov.page_form_option_id = '". (int)$page_form_option['page_form_option_id'] ."' ORDER BY pfov.sort_order ASC");

			$page_form_option_values = array();
			$page_form_option_values = array();
			foreach ($page_form_option_value_query->rows as $page_form_option_value) {
				$page_form_option_value_description_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "page_form_option_value_description` WHERE page_form_id = '" . (int)$page_form_id . "' AND page_form_option_id = '". (int)$page_form_option_value['page_form_option_id'] ."' AND page_form_option_value_id = '". (int)$page_form_option_value['page_form_option_value_id'] ."'");

				$page_form_option_value_description_data = array();
				foreach ($page_form_option_value_description_query->rows as $page_form_option_value_description_value) {
					$page_form_option_value_description_data[$page_form_option_value_description_value['language_id']] = array(
						'name'    			 => $page_form_option_value_description_value['name'],
					);

				}

				$page_form_option_values[] = array(
					'page_form_option_value_id'  => $page_form_option_value['page_form_option_value_id'],
					'page_form_option_id'  		 => $page_form_option_value['page_form_option_id'],
					'sort_order'    			 => $page_form_option_value['sort_order'],
					'page_form_option_value_description'		 		 => $page_form_option_value_description_data,
				);
			}

			$page_form_option_data[] = array(
				'page_form_option_id'  => $page_form_option['page_form_option_id'],
				'type'                 => $page_form_option['type'],				
				'required'             => $page_form_option['required'],
				'status'               => $page_form_option['status'],
				'sort_order'           => $page_form_option['sort_order'],
				'field_name'           => $page_form_option['field_name'],
				'field_help'           => $page_form_option['field_help'],
				'description'		   => $page_form_description_data,
				'option_value'	=> $page_form_option_values,
			);
		}

		return $page_form_option_data;
	}
}