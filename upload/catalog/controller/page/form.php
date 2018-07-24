<?php
class ControllerPageForm extends Controller {
	public function index() {
		$this->load->language('page/form');

		$this->load->model('page/form');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['page_form_id'])) {
			$page_form_id = (int)$this->request->get['page_form_id'];
		} else {
			$page_form_id = 0;
		}

		$page_form_info = $this->model_page_form->getPageForm($page_form_id);

		if ($page_form_info) {
			
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			$this->document->setTitle($page_form_info['meta_title']);
			$this->document->setDescription($page_form_info['meta_description']);
			$this->document->setKeywords($page_form_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $page_form_info['title'],
				'href' => $this->url->link('page/form', 'page_form_id=' .  $page_form_id)
			);

			$data['page_form_id'] = $page_form_info['page_form_id'];

			$data['text_select'] = $this->language->get('text_select');
			$data['button_upload'] = $this->language->get('button_upload');
			$data['text_loading'] = $this->language->get('text_loading');
			$data['text_none'] = $this->language->get('text_none');

			$data['heading_title'] = $page_form_info['title'];
			$data['css'] = $page_form_info['css'];
			
			$data['description'] = html_entity_decode($page_form_info['description'], ENT_QUOTES, 'UTF-8');
			$data['bottom_description'] = html_entity_decode($page_form_info['bottom_description'], ENT_QUOTES, 'UTF-8');

			$data['fieldset_title'] = $page_form_info['fieldset_title'];
			$data['button_continue'] = ($page_form_info['submit_button']) ? $page_form_info['submit_button'] :  $this->language->get('button_continue');


			// Page Form Options
			$data['page_form_options'] = $this->model_page_form->getPageFormOptions($page_form_id);
			$data['country_exists'] = $this->model_page_form->getPageFormOptionsCountry($page_form_id);

			$this->load->model('localisation/country');
			$data['countries'] = $this->model_localisation_country->getCountries();


			$this->load->model('localisation/zone');
			$data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->config->get('config_country_id'));

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $page_form_info['captcha']) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('page/form', $data));

		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('page/form', 'page_form_id=' . $page_form_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function add() {
		if(VERSION < '2.2.0.0') {
			require_once(DIR_SYSTEM.'library/pageform.php');
			global $registry;

			$this->pageform = new pageform($registry); 
		} else{
			$this->load->library('pageform');
		}

		$this->load->language('page/form');

		$this->load->model('page/form');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');

		$json = array();

		if (isset($this->request->get['page_form_id'])) {
			$page_form_id = (int)$this->request->get['page_form_id'];
		} else {
			$page_form_id = 0;
		}

		$page_form_info = $this->model_page_form->getPageForm($page_form_id);

		if($page_form_info) {
			if (isset($this->request->post['field'])) {
				$field = $this->request->post['field'];
			} else {
				$field = array();
			}


			// Page Form Options
			$page_form_options = $this->model_page_form->getPageFormOptions($page_form_id);

			$password = '';
			foreach ($page_form_options as $page_form_option) {
				// Text
				if ($page_form_option['required'] && $page_form_option['type'] == 'text' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateText($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Textarea
				if ($page_form_option['required'] && $page_form_option['type'] == 'textarea' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateTextarea($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Number
				if ($page_form_option['required'] && $page_form_option['type'] == 'number' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateNumber($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}


				// Telephone
				if ($page_form_option['required'] && $page_form_option['type'] == 'telephone' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateTelephone($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Email
				if ($page_form_option['required'] && ($page_form_option['type'] == 'email' || $page_form_option['type'] == 'email_exists') && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateEmail($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : $this->language->get('error_email');
				}

				// Email Exists
				if ($page_form_option['required'] && $page_form_option['type'] == 'email_exists' && isset($field[$page_form_option['page_form_option_id']]) && $this->model_page_form->getPageRequestEmailByPageFormID($field[$page_form_option['page_form_option_id']], $page_form_id)) {
						
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : $this->language->get('error_exists');
						
						$json['error']['warning'] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : $this->language->get('error_exists');
				}

				// Password
				if ($page_form_option['required'] && $page_form_option['type'] == 'password' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validatePassword($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : $this->language->get('error_password');
				}

				// Get First Passowrd
				if ($page_form_option['required'] && $page_form_option['type'] == 'password' && isset($field[$page_form_option['page_form_option_id']])) {
					$password = $field[$page_form_option['page_form_option_id']];
				}
				
				// Confirm Passowrd
				if ($page_form_option['required'] && $page_form_option['type'] == 'confirm_password' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateConfirmPassword($field[$page_form_option['page_form_option_id']], $password)) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : $this->language->get('error_confirm');
				}

				// File
				if ($page_form_option['required'] && $page_form_option['type'] == 'file' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateFile($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Date
				if ($page_form_option['required'] && $page_form_option['type'] == 'date' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateDate($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Time
				if ($page_form_option['required'] && $page_form_option['type'] == 'time' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateTime($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// DateTime
				if ($page_form_option['required'] && $page_form_option['type'] == 'datetime' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateDateTime($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Country
				if ($page_form_option['required'] && $page_form_option['type'] == 'country' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateCountry($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Zone
				if ($page_form_option['required'] && $page_form_option['type'] == 'zone' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateZone($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Postcode
				if ($page_form_option['required'] && $page_form_option['type'] == 'postcode' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validatePostcode($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Address
				if ($page_form_option['required'] && $page_form_option['type'] == 'address' && isset($field[$page_form_option['page_form_option_id']]) && $this->pageform->validateAddress($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Select
				if ($page_form_option['required'] && $page_form_option['type'] == 'select' && empty($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// Radio
				if ($page_form_option['required'] && $page_form_option['type'] == 'radio' && empty($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}

				// checkbox
				if ($page_form_option['required'] && $page_form_option['type'] == 'checkbox' && empty($field[$page_form_option['page_form_option_id']])) {
						$json['error']['field'][$page_form_option['page_form_option_id']] = ($page_form_option['field_error']) ? $page_form_option['field_error'] : sprintf($this->language->get('error_required'), $page_form_option['field_name']);
				}
			}
 
			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $page_form_info['captcha']) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error']['captcha'] = $captcha;
					$json['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $json['error']);
					$json['error']['warning'] = $captcha;
				}
			}
		} else{
			$json['error']['warning'] = $this->language->get('error_not_found');
		}

		if (isset($json['error']) && !isset($json['error']['warning'])) {
			$json['error']['warning'] = $this->language->get('error_warning1');
		}

		if(!$json) {
			$form_data = array();
			$form_data['page_form_id'] = $page_form_id;
			$form_data['customer_id'] = $this->customer->getId();
			$form_data['customer_group_id'] = $this->customer->getGroupId();			
			$form_data['firstname'] = ($this->customer->getId()) ? $this->customer->getFirstName() .' '. $this->customer->getLastName() : 'Guest';
			$form_data['lastname'] = ($this->customer->getId()) ? $this->customer->getLastName() : '';
			$form_data['store_id'] = $this->config->get('config_store_id');
			$form_data['language_id'] = $this->config->get('config_language_id');
			$form_data['ip'] = $this->request->server['REMOTE_ADDR'];
			$form_data['page_form_title'] = isset($page_form_info['title']) ? $page_form_info['title'] : '';
			
			// Fields
			$field_data = array();
			if(isset($field)) {
				foreach ($field as $page_form_option_id => $value) {
					$page_form_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "page_form_option pfo LEFT JOIN " . DB_PREFIX . "page_form_option_description pfod ON (pfo.page_form_option_id = pfod.page_form_option_id) WHERE pfo.page_form_option_id = '" . (int)$page_form_option_id . "' AND pfo.page_form_id = '" . (int)$page_form_id . "' AND pfod.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($page_form_option_query->num_rows) {
						if ($page_form_option_query->row['type'] == 'select' || $page_form_option_query->row['type'] == 'radio') {
							$page_form_option_value_query = $this->db->query("SELECT pfovd.name FROM " . DB_PREFIX . "page_form_option_value pfov LEFT JOIN " . DB_PREFIX . "page_form_option_value_description pfovd ON (pfov.page_form_option_value_id = pfovd.page_form_option_value_id) WHERE pfov.page_form_option_value_id = '" . (int)$value . "' AND pfov.page_form_option_id = '" . (int)$page_form_option_id . "' AND pfovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($page_form_option_value_query->num_rows) {
								$field_data[] = array(
									'name'                    => $page_form_option_query->row['field_name'],
									'value'                   => $page_form_option_value_query->row['name'],
									'type'                    => $page_form_option_query->row['type'],
								);
							}
						} elseif ($page_form_option_query->row['type'] == 'checkbox' && is_array($value)) {
							$checkbox_value = array();
							foreach ($value as $page_form_option_value_id) {
								$page_form_option_value_query = $this->db->query("SELECT pfovd.name FROM " . DB_PREFIX . "page_form_option_value pfov LEFT JOIN " . DB_PREFIX . "page_form_option_value_description pfovd ON (pfov.page_form_option_value_id = pfovd.page_form_option_value_id) WHERE pfov.page_form_option_value_id = '" . (int)$page_form_option_value_id . "' AND pfov.page_form_option_id = '" . (int)$page_form_option_id . "' AND pfovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($page_form_option_value_query->num_rows) {
									$checkbox_value[] = $page_form_option_value_query->row['name'];
								}
							}

							if((array)$checkbox_value) {
								$field_data[] = array(
									'name'                    => $page_form_option_query->row['field_name'],
									'value'                   => implode(', ', $checkbox_value),
									'type'                    => $page_form_option_query->row['type'],
								);
							}

						} else if ($page_form_option_query->row['type'] == 'country') {
							$country_info = $this->model_localisation_country->getCountry($value);
							if($country_info) {
								$field_data[] = array(
									'name'                    => $page_form_option_query->row['field_name'],
									'value'                   => $country_info['name'],
									'type'                    => $page_form_option_query->row['type'],
								);
							}

						} else if ($page_form_option_query->row['type'] == 'zone') {
							$zone_info = $this->model_localisation_zone->getZone($value);
							if($zone_info) {
								$field_data[] = array(
									'name'                    => $page_form_option_query->row['field_name'],
									'value'                   => $zone_info['name'],
									'type'                    => $page_form_option_query->row['type'],
								);
							}
						} else if ($page_form_option_query->row['type'] == 'password' || $page_form_option_query->row['type'] == 'confirm_password') {
							$field_data[] = array(
								'name'                    => $page_form_option_query->row['field_name'],
								'value'                   => base64_encode(serialize($value)),
								'type'                    => $page_form_option_query->row['type'],
							);
						} else {
							$field_data[] = array(
								'name'                    => $page_form_option_query->row['field_name'],
								'value'                   => $value,
								'type'                    => $page_form_option_query->row['type'],
							);
						}
					}
				}
			}

			$form_data['field_data'] = $field_data;
			
			// Page Request
			$this->load->model('page/request');
			$this->model_page_request->addPageRequest($form_data);

			
			$json['success_title'] = html_entity_decode($page_form_info['success_title'], ENT_QUOTES, 'UTF-8');
			$json['success_description'] = html_entity_decode($page_form_info['success_description'], ENT_QUOTES, 'UTF-8');

			$json['success'] = str_replace('&amp;', '&', $this->url->link('page/success', 'id='. $page_form_id, true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}