<?php
class ControllerPagePageRequest extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('page/page_request');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('page/page_buildtable');
		$this->model_page_page_buildtable->Buildtable();

		$this->load->model('page/page_request');

		$this->getList();
	}

	public function delete() {
		$this->load->language('page/page_request');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('page/page_request');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $page_request_id) {
				$this->model_page_page_request->deletePageRequest($page_request_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_page_form_title'])) {
				$url .= '&filter_page_form_title=' . urlencode(html_entity_decode($this->request->get['filter_page_form_title'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_page_form_title'])) {
			$filter_page_form_title = $this->request->get['filter_page_form_title'];
		} else {
			$filter_page_form_title = '';
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = $this->request->get['filter_ip'];
		} else {
			$filter_ip = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pg.date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['filter_page_form_title'])) {
			$url .= '&filter_page_form_title=' . urlencode(html_entity_decode($this->request->get['filter_page_form_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('page/page_request/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('page/page_request/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['page_requests'] = array();

		$filter_data = array(
			'filter_page_form_title'  => $filter_page_form_title,
			'filter_customer'  => $filter_customer,
			'filter_ip'  => $filter_ip,
			'filter_date_added'  => $filter_date_added,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_request_total = $this->model_page_page_request->getTotalPageRequests();

		$results = $this->model_page_page_request->getPageRequests($filter_data);

		$this->load->model('setting/store');
		foreach ($results as $result) {
			$data['page_requests'][] = array(
				'page_request_id' 	=> $result['page_request_id'],
				'page_form_title' 	=> $result['page_form_title'],
				'customer'         	=> $result['customer'],
				'ip'          		=> $result['ip'],
				'date_added'        => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'view'           	=> $this->url->link('page/page_request/info', 'user_token=' . $this->session->data['user_token'] . '&page_request_id=' . $result['page_request_id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_page_form_title'])) {
			$url .= '&filter_page_form_title=' . urlencode(html_entity_decode($this->request->get['filter_page_form_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . '&sort=pg.page_form_title' . $url, true);
		$data['sort_customer'] = $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_ip'] = $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . '&sort=pg.ip' . $url, true);
		$data['sort_date_added'] = $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . '&sort=pg.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_page_form_title'])) {
			$url .= '&filter_page_form_title=' . urlencode(html_entity_decode($this->request->get['filter_page_form_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . urlencode(html_entity_decode($this->request->get['filter_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $page_request_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($page_request_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($page_request_total - $this->config->get('config_limit_admin'))) ? $page_request_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $page_request_total, ceil($page_request_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_page_form_title'] = $filter_page_form_title;
		$data['filter_ip'] = $filter_ip;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_customer'] = $filter_customer;

		$data['user_token'] = $this->session->data['user_token'];

		if(VERSION > '2.0.3.1') {
			$data['customer_action'] = str_replace('&amp;', '&', $this->url->link('customer/customer', 'user_token='. $this->session->data['user_token'], true));
		} else{
			$data['customer_action'] = str_replace('&amp;', '&', $this->url->link('sale/customer', 'user_token='. $this->session->data['user_token'], true));
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('page/page_request_list', $data));
	}

	public function info() {
		$this->load->model('page/page_request');
		$this->load->model('setting/store');
		$this->load->model('localisation/language');

		if (isset($this->request->get['page_request_id'])) {
			$page_request_id = $this->request->get['page_request_id'];
		} else {
			$page_request_id = 0;
		}

		$page_request_info = $this->model_page_page_request->getPageRequest($page_request_id);

		if ($page_request_info) {
			$this->load->language('page/page_request');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_page_detail'] = $this->language->get('text_page_detail');
			$data['text_customer_detail'] = $this->language->get('text_customer_detail');
			$data['text_store'] = $this->language->get('text_store');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_customer'] = $this->language->get('text_customer');
			$data['text_customer_group'] = $this->language->get('text_customer_group');
			$data['text_ip'] = $this->language->get('text_ip');
			$data['text_user_agent'] = $this->language->get('text_user_agent');
			$data['text_page_form_title'] = $this->language->get('text_page_form_title');
			$data['text_language_name'] = $this->language->get('text_language_name');
			$data['text_fields'] = $this->language->get('text_fields');
			$data['text_field_name'] = $this->language->get('text_field_name');
			$data['text_field_value'] = $this->language->get('text_field_value');
			
			$data['button_back'] = $this->language->get('button_back');

			$url = '';

			if (isset($this->request->get['filter_page_request_id'])) {
				$url .= '&filter_page_request_id=' . $this->request->get['filter_page_request_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			$data['back'] = $this->url->link('page/page_request', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['user_token'] = $this->session->data['user_token'];

			$store_info = $this->model_setting_store->getStore($page_request_info['store_id']);
			if($store_info) {
				$data['store_name'] = $store_info['name'];
			} else{
				$data['store_name'] = $this->language->get('text_default');
			}

			$language_info = $this->model_localisation_language->getLanguage($page_request_info['language_id']);
			if($language_info) {
				$data['language_name'] = $language_info['name'];
			} else{
				$data['language_name'] = '';
			}

			$data['date_added'] = date($this->language->get('datetime_format'), strtotime($page_request_info['date_added']));

			$data['page_form_title'] = $page_request_info['page_form_title'];
			$data['ip'] = $page_request_info['ip'];
			$data['user_agent'] = $page_request_info['user_agent'];
			$data['firstname'] = $page_request_info['firstname'];
			$data['lastname'] = $page_request_info['lastname'];

			if ($page_request_info['customer_id']) {
				$data['customer'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $page_request_info['customer_id'], true);
			} else {
				$data['customer'] = '';
			}

			if ($page_request_info['page_form_id']) {
				$data['page_form_href'] = $this->url->link('page/page_form/edit', 'user_token=' . $this->session->data['user_token'] . '&page_form_id=' . $page_request_info['page_form_id'], true);
			} else {
				$data['page_form_href'] = '';
			}

			$data['store_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

			$this->load->model('customer/customer_group');
			$customer_group_info = $this->model_customer_customer_group->getCustomerGroup($page_request_info['customer_group_id']);

			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}

			// Uploaded files
			$this->load->model('tool/upload');

			$data['page_request_id'] = $this->request->get['page_request_id'];

			$page_request_options = $this->model_page_page_request->getPageRequestOptions($page_request_id);

			$data['page_request_options'] = array();
			foreach($page_request_options as $page_request_option) {
				if($page_request_option['type'] == 'password' || $page_request_option['type'] == 'confirm_password') {
					$page_request_option['value'] = unserialize(base64_decode($page_request_option['value']));
				}

				if ($page_request_option['type'] != 'file') {
					$data['page_request_options'][] = array(
						'name'		=> $page_request_option['name'],
						'value'		=> nl2br($page_request_option['value']),
						'type'		=> $page_request_option['type'],
					);
				} else{
					$upload_info = $this->model_tool_upload->getUploadByCode($page_request_option['value']);
					if ($upload_info) {
						$data['page_request_options'][] = array(
							'name'  => $page_request_option['name'],
							'value' => $upload_info['name'],
							'type'  => $page_request_option['type'],
							'href'  => $this->url->link('tool/upload/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'], true)
						);
					}
				}
			}

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('page/page_request_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'page/page_request')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}