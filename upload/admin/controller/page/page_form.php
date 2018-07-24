<?php
class ControllerPagePageForm extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('page/page_form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('page/page_buildtable');
		$this->model_page_page_buildtable->Buildtable();

		$this->load->model('page/page_form');

		$this->getList();
	}

	public function add() {
		$this->load->language('page/page_form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('page/page_form');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_page_page_form->addPageForm($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('page/page_form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('page/page_form');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_page_page_form->editPageForm($this->request->get['page_form_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('page/page_form');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('page/page_form');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $page_form_id) {
				$this->model_page_page_form->deletePageForm($page_form_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('page/page_form/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('page/page_form/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['page_forms'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$page_form_total = $this->model_page_page_form->getTotalPageForms();

		$results = $this->model_page_page_form->getPageForms($filter_data);

		$data['column_link'] = $this->language->get('column_link');
		$catalog_url = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

		foreach ($results as $result) {
			$data['page_forms'][] = array(
				'page_form_id'  => $result['page_form_id'],
				'title' 		=> $result['title'],
				'link' 			=> $catalog_url .'index.php?route=page/form&page_form_id='. $result['page_form_id'],
				'status' 		=> ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'sort_order' 	=> $result['sort_order'],
				'edit'       	=> $this->url->link('page/page_form/edit', 'user_token=' . $this->session->data['user_token'] . '&page_form_id=' . $result['page_form_id'] . $url, true)
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . '&sort=o.title' . $url, true);
		$data['sort_sort_order'] = $this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . '&sort=o.sort_order' . $url, true);
		$data['sort_status'] = $this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . '&sort=o.status' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $page_form_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($page_form_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($page_form_total - $this->config->get('config_limit_admin'))) ? $page_form_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $page_form_total, ceil($page_form_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('page/page_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['page_form_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = '';
		}

		if (isset($this->error['success_title'])) {
			$data['error_success_title'] = $this->error['success_title'];
		} else {
			$data['error_success_title'] = '';
		}

		if (isset($this->error['customer_subject'])) {
			$data['error_customer_subject'] = $this->error['customer_subject'];
		} else {
			$data['error_customer_subject'] = '';
		}

		if (isset($this->error['customer_message'])) {
			$data['error_customer_message'] = $this->error['customer_message'];
		} else {
			$data['error_customer_message'] = '';
		}

		if (isset($this->error['admin_email'])) {
			$data['error_admin_email'] = $this->error['admin_email'];
		} else {
			$data['error_admin_email'] = '';
		}

		if (isset($this->error['admin_subject'])) {
			$data['error_admin_subject'] = $this->error['admin_subject'];
		} else {
			$data['error_admin_subject'] = '';
		}

		if (isset($this->error['admin_message'])) {
			$data['error_admin_message'] = $this->error['admin_message'];
		} else {
			$data['error_admin_message'] = '';
		}

		if (isset($this->error['field_name'])) {
			$data['error_field_name'] = $this->error['field_name'];
		} else {
			$data['error_field_name'] = array();
		}

		if (isset($this->error['value_name'])) {
			$data['error_value_name'] = $this->error['value_name'];
		} else {
			$data['error_value_name'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['page_form_id'])) {
			$data['action'] = $this->url->link('page/page_form/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('page/page_form/edit', 'user_token=' . $this->session->data['user_token'] . '&page_form_id=' . $this->request->get['page_form_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('page/page_form', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['page_form_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$page_form_info = $this->model_page_page_form->getPageForm($this->request->get['page_form_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($page_form_info)) {
			$data['top'] = $page_form_info['top'];
		} else {
			$data['top'] = '1';
		}

		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($page_form_info)) {
			$data['bottom'] = $page_form_info['bottom'];
		} else {
			$data['bottom'] = '1';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($page_form_info)) {
			$data['sort_order'] = $page_form_info['sort_order'];
		} else {
			$data['sort_order'] = '0';
		}

		if (isset($this->request->post['css'])) {
			$data['css'] = $this->request->post['css'];
		} elseif (!empty($page_form_info)) {
			$data['css'] = $page_form_info['css'];
		} else {
			$data['css'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($page_form_info)) {
			$data['status'] = $page_form_info['status'];
		} else {
			$data['status'] = '1';
		}


		if (isset($this->request->post['customer_email_status'])) {
			$data['customer_email_status'] = $this->request->post['customer_email_status'];
		} elseif (!empty($page_form_info)) {
			$data['customer_email_status'] = $page_form_info['customer_email_status'];
		} else {
			$data['customer_email_status'] = '';
		}

		if (isset($this->request->post['admin_email_status'])) {
			$data['admin_email_status'] = $this->request->post['admin_email_status'];
		} elseif (!empty($page_form_info)) {
			$data['admin_email_status'] = $page_form_info['admin_email_status'];
		} else {
			$data['admin_email_status'] = '';
		}

		if (isset($this->request->post['admin_email'])) {
			$data['admin_email'] = $this->request->post['admin_email'];
		} elseif (!empty($page_form_info)) {
			$data['admin_email'] = $page_form_info['admin_email'];
		} else {
			$data['admin_email'] = $this->config->get('config_email');
		}

		if (isset($this->request->post['show_guest'])) {
			$data['show_guest'] = $this->request->post['show_guest'];
		} elseif (!empty($page_form_info)) {
			$data['show_guest'] = $page_form_info['show_guest'];
		} else {
			$data['show_guest'] = '1';
		}

		if (isset($this->request->post['captcha'])) {
			$data['captcha'] = $this->request->post['captcha'];
		} elseif (!empty($page_form_info)) {
			$data['captcha'] = $page_form_info['captcha'];
		} else {
			$data['captcha'] = '';
		}

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['page_form_store'])) {
			$data['page_form_store'] = $this->request->post['page_form_store'];
		} elseif (isset($this->request->get['page_form_id'])) {
			$data['page_form_store'] = $this->model_page_page_form->getPageFormStores($this->request->get['page_form_id']);
		} else {
			$data['page_form_store'] = array(0);
		}

		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
	
		if (isset($this->request->post['page_form_customer_group'])) {
			$data['page_form_customer_group'] = $this->request->post['page_form_customer_group'];
		} elseif (isset($this->request->get['page_form_id'])) {
			$data['page_form_customer_group'] = $this->model_page_page_form->getPageFormCustomerGroups($this->request->get['page_form_id']);
		} else {
			$data['page_form_customer_group'] = array($this->config->get('config_customer_group_id'));
		}

		if (isset($this->request->post['page_form_description'])) {
			$data['page_form_description'] = $this->request->post['page_form_description'];
		} elseif (isset($this->request->get['page_form_id'])) {
			$data['page_form_description'] = $this->model_page_page_form->getPageFormDescriptions($this->request->get['page_form_id']);
		} else {
			$data['page_form_description'] = array();
		}

		if (isset($this->request->post['page_form_seo_url'])) {
			$data['page_form_seo_url'] = $this->request->post['page_form_seo_url'];
		} elseif (isset($this->request->get['page_form_id'])) {
			$data['page_form_seo_url'] = $this->model_page_page_form->getPageFormSeoUrls($this->request->get['page_form_id']);
		} else {
			$data['page_form_seo_url'] = array();
		}
			
		if (isset($this->request->post['page_form_field'])) {
			$data['fields'] = $this->request->post['page_form_field'];
		} elseif (isset($this->request->get['page_form_id'])) {
			$data['fields'] = $this->model_page_page_form->getPageFormOptions($this->request->get['page_form_id']);
		} else {
			$data['fields'] = array();
		}

		$this->load->model('catalog/information');
		$data['informations'] = $this->model_catalog_information->getInformations();

		if (isset($this->request->post['page_form_information'])) {
			$data['page_form_information'] = $this->request->post['page_form_information'];
		} elseif (isset($this->request->get['page_form_id'])) {
			$data['page_form_information'] = $this->model_page_page_form->getPageFormInformations($this->request->get['page_form_id']);
		} else {
			$data['page_form_information'] = array(0);
		}

		$data['config_language_id'] = $this->config->get('config_language_id');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('page/page_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'page/page_form')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['page_form_description'] as $language_id => $page_form_value) {
			if ((utf8_strlen($page_form_value['title']) < 2) || (utf8_strlen($page_form_value['title']) > 255)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if ((utf8_strlen($page_form_value['meta_title']) < 3) || (utf8_strlen($page_form_value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
			
			if ((utf8_strlen($page_form_value['success_title']) < 2) || (utf8_strlen($page_form_value['success_title']) > 255)) {
				$this->error['success_title'][$language_id] = $this->language->get('error_success_title');
			}

			if(!empty($this->request->post['customer_email_status'])) {
				if ((utf8_strlen($page_form_value['customer_subject']) < 2) || (utf8_strlen($page_form_value['customer_subject']) > 255)) {
					$this->error['customer_subject'][$language_id] = $this->language->get('error_customer_subject');
				}

				$page_form_value['customer_message'] = str_replace('&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', $page_form_value['customer_message']);
				if ((utf8_strlen($page_form_value['customer_message']) < 25)) {
					$this->error['customer_message'][$language_id] = $this->language->get('error_customer_message');
				}
			}

			if(!empty($this->request->post['admin_email_status'])) {
				if(empty($this->request->post['admin_email'])) {
					$this->error['admin_email'] = $this->language->get('error_admin_email');
					$this->error['warning'] = $this->language->get('error_admin_email');
				}
				
				if ((utf8_strlen($page_form_value['admin_subject']) < 2) || (utf8_strlen($page_form_value['admin_subject']) > 255)) {
					$this->error['admin_subject'][$language_id] = $this->language->get('error_admin_subject');
				}

				$page_form_value['admin_message'] = str_replace('&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', $page_form_value['admin_message']);
				if ((utf8_strlen($page_form_value['admin_message']) < 25)) {
					$this->error['admin_message'][$language_id] = $this->language->get('error_admin_message');
				}
			}
		}

		if (isset($this->request->post['page_form_field'])) {
			foreach ($this->request->post['page_form_field'] as $row => $description) {
				if(isset($description['description'])) {
					foreach ($description['description'] as $language_id => $value) {
						if ((utf8_strlen($value['field_name']) < 1) || (utf8_strlen($value['field_name']) > 128)) {
							$this->error['field_name'][$row][$language_id] = $this->language->get('error_field_name');
						}
					}
				}

				if(isset($description['option_value']) && !in_array($description['type'], array('select', 'radio', 'checkbox')) ) {
					unset($this->request->post['page_form_field'][$row]['option_value']);
					unset($description['option_value']);
				}

				if(isset($description['option_value'])) {
					foreach ($description['option_value'] as $option_value_row => $option_value) {
						foreach ($option_value['page_form_option_value_description'] as $language_id => $option_value_description) {
							if ((utf8_strlen($option_value_description['name']) < 1) || (utf8_strlen($option_value_description['name']) > 128)) {
								$this->error['value_name'][$row][$option_value_row][$language_id] = $this->language->get('error_value_name');
							}
						}
					}
				}
			}
		}

		if ($this->request->post['page_form_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['page_form_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}						
						
						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
						
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['page_form_id']) || (($seo_url['query'] != 'page_form_id=' . $this->request->get['page_form_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
								
								break;
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

		protected function validateDelete() {
			if (!$this->user->hasPermission('modify', 'page/page_form')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}	

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_title'])) {
			if (isset($this->request->get['filter_title'])) {
				$filter_title = $this->request->get['filter_title'];
			} else {
				$filter_title = '';
			}

			$this->load->model('page/page_form');

			$filter_data = array(
				'filter_title' => $filter_title,
				'start'        => 0,
				'limit'        => 5
			);

			$results = $this->model_page_page_form->getPageForms($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'page_form_id'       => $result['page_form_id'],
					'title'              => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['title'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}