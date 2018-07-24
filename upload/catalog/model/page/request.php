<?php
class ModelPageRequest extends Model {
	public function addPageRequest($data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "page_request SET page_form_id = '". (int)$data['page_form_id'] ."', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', store_id = '" . (int)$data['store_id'] . "', language_id = '" . (int)$data['language_id'] . "', page_form_title = '" . $this->db->escape($data['page_form_title']) . "',  firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', ip = '" . $this->db->escape($data['ip']) . "', user_agent = '" . $this->db->escape($this->request->server['HTTP_USER_AGENT']) . "', date_added = NOW()");

		$page_request_id = $this->db->getLastId();

		// Page Request Options - (Fields) //// $field_data ////
		if (isset($data['field_data'])) {
			foreach ($data['field_data'] as $field_data) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "page_request_option SET page_request_id = '" . (int)$page_request_id . "', page_form_id = '" . (int)$data['page_form_id'] . "', name = '" . $this->db->escape($field_data['name']) . "', value = '" . $this->db->escape($field_data['value']) . "', type = '" . $this->db->escape($field_data['type']) . "'");
			}
		}

		// Send Email System
		$this->load->model('page/form');
		$this->load->model('tool/upload');
		$page_form_info = $this->model_page_form->getPageForm($data['page_form_id']);

		if($page_form_info) {
			if(!empty($page_form_info['customer_email_status'])) {
				$page_request_info = $this->getPageRequestEmail($page_form_info['page_form_id'], $page_request_id);

				if($page_request_info) {
					$customer_email = $page_request_info['value'];
				} else{
					$customer_email = $this->customer->getEmail();
				}
				if($customer_email) {
					if(!empty($page_form_info['customer_subject'])) {
						$subject = $page_form_info['customer_subject'];
					} else{
						$subject = '';
					}

					if(!empty($page_form_info['customer_message'])) {
						$message = $page_form_info['customer_message'];
					} else{
						$message = '';
					}

					if ($this->request->server['HTTPS']) {
						$server = $this->config->get('config_ssl');
					} else {
						$server = $this->config->get('config_url');
					}

					if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
						$logo = $server . 'image/' . $this->config->get('config_logo');
					} else {
						$logo = '';
					}
					
					$home_href = $this->url->link('common/home', '', true);
					
					$information_data = array();
					$upload_data = array();
					if (isset($data['field_data'])) {
						foreach ($data['field_data'] as $field_data) {
							if($field_data) {
								if($field_data['type'] == 'password' || $field_data['type'] == 'confirm_password') {
									$field_data['value'] = unserialize(base64_decode($field_data['value']));
									
								}

								if ($field_data['type'] != 'file') {
									$information_data[] = $field_data['name']. ': '. nl2br($field_data['value']);
								} else{
									$upload_info = $this->model_tool_upload->getUploadByCode($field_data['value']);
									if ($upload_info) {
										$information_data[] = $field_data['name']. ': '. $upload_info['name'];

										$orgname = DIR_UPLOAD . $upload_info['filename'];
										$temp_name = DIR_UPLOAD . $upload_info['name'];
										copy($orgname, $temp_name);
										$upload_data[] = $temp_name;
									}

								}
							}
							
						}
					}

					if($information_data) {
						$information_fields =  implode('<br />', $information_data);
					} else{
						$information_fields = '';
					}

					$find = array(
						'{STORE_NAME}',
						'{STORE_LINK}',
						'{LOGO}',
						'{INFORMATION}',
					);
				
					$replace = array(
						'STORE_NAME'					=> $this->config->get('config_name'),
						'STORE_LINK'					=> $home_href,
						'LOGO'							=> '<img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" />',
						'INFORMATION'					=> $information_fields,
					);
					
					if(!empty($subject)) {
						$subject = str_replace($find, $replace, $subject);
					}else{
						$subject = '';
					}
					
					if(!empty($message)) {
						$message = str_replace($find, $replace, $message);
					}else{
						$message = '';
					}


					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

					$mail->setTo($customer_email);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

					if(!empty($upload_data)) {
						foreach ($upload_data as $upload_file_name) {
							$mail->addAttachment($upload_file_name);
						}
					}

					$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
					$mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));

					$mail->send();

					
				}
			}

			if(!empty($page_form_info['admin_email_status'])) {
				if(!empty($page_form_info['admin_email'])) {
					$admin_email = $page_form_info['admin_email'];
				} else{
					$admin_email = $this->config->get('config_email');
				}

				if(!empty($page_form_info['admin_subject'])) {
					$subject = $page_form_info['admin_subject'];
				} else{
					$subject = '';
				}

				if(!empty($page_form_info['admin_message'])) {
					$message = $page_form_info['admin_message'];
				} else{
					$message = '';
				}

				if ($this->request->server['HTTPS']) {
					$server = $this->config->get('config_ssl');
				} else {
					$server = $this->config->get('config_url');
				}

				if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
					$logo = $server . 'image/' . $this->config->get('config_logo');
				} else {
					$logo = '';
				}
				
				$home_href = $this->url->link('common/home', '', true);
				
				$information_data = array();
				$upload_data = array();
				if (isset($data['field_data'])) {
					foreach ($data['field_data'] as $field_data) {
						if($field_data) {
							if($field_data['type'] == 'password' || $field_data['type'] == 'confirm_password') {
								$field_data['value'] = unserialize(base64_decode($field_data['value']));
								
							}

							if ($field_data['type'] != 'file') {
								$information_data[] = $field_data['name']. ': '. nl2br($field_data['value']);
							} else{
								$upload_info = $this->model_tool_upload->getUploadByCode($field_data['value']);
								if ($upload_info) {
									$information_data[] = $field_data['name']. ': '. $upload_info['name'];

									$orgname = DIR_UPLOAD . $upload_info['filename'];
									$temp_name = DIR_UPLOAD . $upload_info['name'];
									copy($orgname, $temp_name);
									$upload_data[] = $temp_name;
								}

							}
						}
						
					}
				}

				if($information_data) {
					$information_fields =  implode('<br />', $information_data);
				} else{
					$information_fields = '';
				}

				$find = array(
					'{STORE_NAME}',
					'{STORE_LINK}',
					'{LOGO}',
					'{INFORMATION}',
				);
			
				$replace = array(
					'STORE_NAME'					=> $this->config->get('config_name'),
					'STORE_LINK'					=> $home_href,
					'LOGO'							=> '<img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" />',
					'INFORMATION'					=> $information_fields,
				);
				
				if(!empty($subject)) {
					$subject = str_replace($find, $replace, $subject);
				}else{
					$subject = '';
				}
				
				if(!empty($message)) {
					$message = str_replace($find, $replace, $message);
				}else{
					$message = '';
				}

				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($admin_email);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

				if(!empty($upload_data)) {
					foreach ($upload_data as $upload_file_name) {
						$mail->addAttachment($upload_file_name);
					}
				}
				
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));

				$mail->send();

				// Send to additional alert emails if new account email is enabled
				$emails = explode(',', $this->config->get('config_alert_email'));

				foreach ($emails as $email) {
					if (utf8_strlen($email) > 0 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$mail->setTo($email);
						$mail->send();
					}
				}
			}
		}

		if(!empty($upload_data)) {
			foreach ($upload_data as $upload_file_name) {
				unlink( $upload_file_name );
			}
		}
	}

	public function getPageRequestEmail($page_form_id, $page_request_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "page_request_option` WHERE  `page_form_id` = '" . (int)$page_form_id . "' AND (`type` = 'email' OR `type` = 'email_exists') AND page_request_id='". (int)$page_request_id ."'");

		return $query->row;
	}
}