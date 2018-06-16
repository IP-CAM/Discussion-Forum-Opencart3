<?php

class ControllerExtensionModuleForum extends Controller {

	private $error = array();

	public function install() {

        $this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."forum (
			`forum_id` int(11) NOT NULL auto_increment,
			`name` varchar(64) NOT NULL,
			`description` TEXT NOT NULL,
			`customer_id` int(11) NOT NULL,
			`username` varchar(50) NOT NULL,
			`email` varchar(50) NOT NULL,
			`avatar` varchar(100) NOT NULL,
			`status` tinyint(1) NOT NULL,
			`views` int(5) NOT NULL,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (`forum_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
 
        $this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."forum_image (
			`forum_image_id` int(11) NOT NULL auto_increment,
			`forum_id` int(11) NOT NULL,
			`image` varchar(255) NOT NULL,
			PRIMARY KEY  (`forum_image_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."forum_image_description (
			`forum_image_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`forum_id` int(11) NOT NULL,
			`title` varchar(64) NOT NULL,
			PRIMARY KEY  (`forum_image_id`,`language_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."forum_reply (
			`id` int(11) NOT NULL auto_increment,
			`forum_id` int(11) NOT NULL,
			`customer_id` int(11) NOT NULL,
			`username` varchar(50) NOT NULL,
			`email` varchar(50) NOT NULL,
			`avatar` varchar(100) NOT NULL,
			`reply` varchar(500) NOT NULL,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`status` tinyint(1) NOT NULL,
			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
			
			/* Start Email Function on Installation */
			$query = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . "module` WHERE `code` = 'forum'");
			$countforumstatus = $query->row['total'];
			if($countforumstatus<1 && (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())){
				$store_name = $this->config->get('config_name');
				
				$store_url = HTTP_CATALOG ;	

				$message  = "It is default email. Sender has installed Simple Opencart Forum extension on the below store.";
				$message .= "\n\n";
				$message .= 'Store URL- '.$store_url . "\n\n";
				$message .= 'Store Email- '.$this->config->get('config_email') . "\n\n";
				$message .= 'Store Name- '.html_entity_decode($store_name, ENT_QUOTES, 'UTF-8');

				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo('bdm@synapseindia.com');
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
				$mail->setSubject('Simple Opencart Forum extension installed');
				$mail->setText($message);
				$mail->send();
			}
			/* End Email Function on Installation */			
	
    }

	public function index() {
		
		$this->install();
		 
		$this->load->language('extension/module/forum');



		$this->document->setTitle($this->language->get('heading_title'));

        

		$this->load->model('setting/module');
        


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if (!isset($this->request->get['module_id'])) {

				$this->model_setting_module->addModule('forum', $this->request->post);

			} else {

				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

			}



			$this->session->data['success'] = $this->language->get('text_success');



			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

		}



		$data['heading_title'] = $this->language->get('heading_title');



		$data['text_edit'] = $this->language->get('text_edit');

		$data['text_enabled'] = $this->language->get('text_enabled');

		$data['text_disabled'] = $this->language->get('text_disabled');



		$data['entry_name'] = $this->language->get('entry_name');


		$data['entry_status'] = $this->language->get('entry_status');


		$data['button_save'] = $this->language->get('button_save');

		$data['button_cancel'] = $this->language->get('button_cancel');



		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];

		} else {

			$data['error_warning'] = '';

		}



		if (isset($this->error['name'])) {

			$data['error_name'] = $this->error['name'];

		} else {

			$data['error_name'] = '';

		}
     





		$data['breadcrumbs'] = array();



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_home'),

			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)

		);

      

		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_extension'),

			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)

		);



		if (!isset($this->request->get['module_id'])) {

			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('heading_title'),

				'href' => $this->url->link('extension/module/forum', 'user_token=' . $this->session->data['user_token'], true)

			);

		} else {

			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('heading_title'),

				'href' => $this->url->link('extension/module/forum', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)

			);

		}



		if (!isset($this->request->get['module_id'])) {

			$data['action'] = $this->url->link('extension/module/forum', 'user_token=' . $this->session->data['user_token'], true);

		} else {

			$data['action'] = $this->url->link('extension/module/forum', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);

		}

     

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);



		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);

		}



		$data['user_token'] = $this->session->data['user_token'];



		if (isset($this->request->post['name'])) {

			$data['name'] = $this->request->post['name'];

		} elseif (!empty($module_info)) {

			$data['name'] = $module_info['name'];

		} else {

			$data['name'] = '';

		}





		if (isset($this->request->post['status'])) {

			$data['status'] = $this->request->post['status'];

		} elseif (!empty($module_info)) {

			$data['status'] = $module_info['status'];

		} else {

			$data['status'] = '';

		}



		$data['header'] = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer'] = $this->load->controller('common/footer');


		$this->response->setOutput($this->load->view('extension/module/forum', $data));

	}



	protected function validate() {

		if (!$this->user->hasPermission('modify', 'extension/module/forum')) {

			$this->error['warning'] = $this->language->get('error_permission');

		}


		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {

			$this->error['name'] = $this->language->get('error_name');

		}


		return !$this->error;

	}

}

