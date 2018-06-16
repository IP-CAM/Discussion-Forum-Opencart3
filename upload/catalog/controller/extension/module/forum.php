<?php  
class ControllerExtensionModuleForum extends Controller {

	private $error = array(); 
	
	public function index() {
		$this->load->language('extension/module/forum');

		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->load->model('design/forum');
		$limit = 10;
		$filter_data_p = array(

			'start'              => 0,

			'limit'              => $limit

		);
		$data['forumdata'] = $this->model_design_forum->getForums($filter_data_p);
		
		$data['base'] = $this->config->get('config_ssl');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['code'] = str_replace('http', 'https', html_entity_decode($this->config->get('forum_code')));
		} else {
			$data['code'] = html_entity_decode($this->config->get('forum_text_field'));
		}
		
		$this->document->addStyle('catalog/view/theme/default/stylesheet/forum.css');
		return $this->load->view('extension/module/forum', $data);	

	}
	
	public function getForums() {
		$this->load->language('extension/module/forum');

		$this->load->model('design/forum');

		$this->load->model('tool/image'); 

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
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
		$limit = 20;
		$filter_data_p = array(

			'start'              => ($page - 1) * $limit,

			'limit'              => $limit

		);

		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_forum'),
			'href'      => $this->url->link('extension/module/forum/getForums'),
			'separator' => $this->language->get('text_separator')
		);
		if(isset($this->request->get['author_id'])) {
			$forum_info = $this->model_design_forum->getAuthorForums(base64_decode($this->request->get['author_id']),$filter_data_p);
			$forum_total = $this->model_design_forum->getForumsTotalAuthor(base64_decode($this->request->get['author_id']));
		}else {		
			$forum_info = $this->model_design_forum->getForums($filter_data_p);
			$forum_total = $this->model_design_forum->getForumsTotal();
		}
		
		$this->document->setTitle('Forums');
		



			
        $pagination = new Pagination();

		$pagination->total = $forum_total;

		$pagination->page = $page;

		$pagination->limit = 20;

		$pagination->url = $this->url->link('extension/module/forum/getForums', '&page={page}', true);

		$data['pagination'] = $pagination->render();
		
	    $data['results'] = sprintf($this->language->get('text_pagination'), ($forum_total) ? (($page - 1) * 20) + 1 : 0, ((($page - 1) * 20) > ($forum_total - 20)) ? $forum_total : ((($page - 1) * 20) + 20), $forum_total, ceil($forum_total / 20));
		
		$data['heading_title'] = 'Forums';

		$data['button_continue'] = $this->language->get('button_continue');
		
		$data['base'] = $this->config->get('config_ssl');

		$data['continue'] = $this->url->link('common/home');

		$data['forumdata'] = $forum_info;	
		
		$data['column_left'] = $this->load->controller('common/column_left');

		$data['column_right'] = $this->load->controller('common/column_right');

		$data['content_top'] = $this->load->controller('common/content_top');

		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		$data['footer'] = $this->load->controller('common/footer');

		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/module/forums', $data));
	}
	
	public function getForum() {
	
		$this->load->language('extension/module/forum');

		$this->load->model('design/forum');
		
		
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['post_reply'] = $this->language->get('post_reply');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');			

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_avatar'] = $this->language->get('entry_avatar');
		$data['entry_topic_title'] = $this->language->get('entry_topic_title');
		$data['entry_topic_reply'] = $this->language->get('entry_topic_reply');		

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_forum'] = $this->language->get('button_add_forum');
		$data['button_remove'] = $this->language->get('button_remove');
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}		
		
		if (isset($this->request->get['forum_id'])) {
			$forum_id = $this->request->get['forum_id'];
		} else {
			$forum_id = 0;
		} 
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
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

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}
		

		$this->load->model('account/customer');		
		$data['customer_id'] = $this->customer->getId();
		$data['login_url'] = $this->url->link('account/login');
		if($this->customer->getId()){
			$data['customer_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
			$data['customer_email'] = $this->customer->getEmail();
			$customer_data = $this->model_account_customer->getCustomer($this->customer->getId());
			
		}
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_forum'),
			'href'      => $this->url->link('extension/module/forum/getForums'),
			'separator' => $this->language->get('text_separator')
		);
		
		$get_forum_views = $this->model_design_forum->getForumViews($forum_id); 
				
		if(!empty($get_forum_views)) {
		
			$view = $get_forum_views[0]['views'] + 1;

			$forum_views = $this->model_design_forum->addForumViews($view,$forum_id); 			
		}
		
		$forum_info = $this->model_design_forum->getForum($forum_id);
		
		$forum_reply = $this->model_design_forum->getForumReply($forum_id);	

		if ($forum_info) {
			$this->document->setTitle($forum_info[0]['name']);
			

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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			
			$data['forum_id'] = $this->request->get['forum_id'];	
			
			if (isset($this->request->get['forum_id'])) { 
				$data['action'] = $this->url->link('extension/module/forum/reply', $url, 'SSL');
			}			
		
			$data['breadcrumbs'][] = array(
				'text'      => $forum_info[0]['name'],
				'href'      => $this->url->link('extension/module/forum/getForum', 'forum_id=' . $this->request->get['forum_id'] . $url),
				'separator' => $this->language->get('text_separator')
			);
			$data['heading_title'] = $forum_info[0]['name'];

			$data['button_continue'] = $this->language->get('button_continue');

			$data['description'] = html_entity_decode($forum_info[0]['description'], ENT_QUOTES, 'UTF-8');
			
			$data['base'] = $this->config->get('config_ssl');

			$data['continue'] = $this->url->link('common/home');

			$data['forumdata'] = array();
			foreach($forum_info as $formdatasingle){
				$data['forumdata'][] = 	array(
				'customer_id'=>$formdatasingle['customer_id'],
				'customer_id_encode'=>base64_encode($formdatasingle['customer_id']),
				'username'=>$formdatasingle['username'],
				'name'=>$formdatasingle['name'],
				'date'=>$formdatasingle['date'],
				'description'=>html_entity_decode($formdatasingle['description'], ENT_QUOTES, 'UTF-8')
				);	
			}
				
			
			$data['forumreplydata'] = array();	
			foreach($forum_reply as $formdatarepsingle){
				$data['forumreplydata'][] = 	array(
				'customer_id'=>$formdatarepsingle['customer_id'],
				'customer_id_encode'=>base64_encode($formdatarepsingle['customer_id']),
				'username'=>$formdatarepsingle['username'],
				'name'=>$formdatarepsingle['name'],
				'date'=>$formdatarepsingle['date'],
				'reply'=>html_entity_decode($formdatarepsingle['reply'], ENT_QUOTES, 'UTF-8')
				);	
			}
			
			
			$data['column_left'] = $this->load->controller('common/column_left');

			$data['column_right'] = $this->load->controller('common/column_right');

			$data['content_top'] = $this->load->controller('common/content_top');

			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$data['footer'] = $this->load->controller('common/footer');

			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/module/forumview', $data));		
			


		}		
	}
	public function addTopic() {
		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('extension/module/forum/addTopic', '', true);



			$this->response->redirect($this->url->link('account/login', '', true));

		}	
		
		$this->load->model('account/customer');
		$data['customer_id'] = $this->customer->getId();
		$data['customer_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
		$data['customer_email'] = $this->customer->getEmail();
		$customer_data = $this->model_account_customer->getCustomer($this->customer->getId());
		
		
		$this->load->language('extension/module/forum');
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['post_topic'] = $this->language->get('post_topic');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');			

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_avatar'] = $this->language->get('entry_avatar');
		$data['entry_topic_title'] = $this->language->get('entry_topic_title');
		$data['entry_topic_message'] = $this->language->get('entry_topic_message');		

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_forum'] = $this->language->get('button_add_forum');
		$data['button_remove'] = $this->language->get('button_remove');
		
		

		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->load->model('design/forum');
		
		$data['base'] = $this->config->get('config_ssl');
		
		$this->document->setTitle($this->language->get('topic'));
		
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
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
		
		if (!isset($this->request->get['forum_id'])) { 
			$data['action'] = $this->url->link('extension/module/forum/insert', $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('extension/module/forum/update', '&forum_id=' . $this->request->get['forum_id'] . $url, 'SSL');
		}
		
		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}	
		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}
		if (isset($this->error['avatar'])) {
			$data['error_avatar'] = $this->error['avatar'];
		} else {
			$data['error_avatar'] = '';
		}
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_forum'),
			'href'      => $this->url->link('extension/module/forum/getForums'),
			'separator' => $this->language->get('text_separator')
		);
		
	



		$data['column_left'] = $this->load->controller('common/column_left');

		$data['column_right'] = $this->load->controller('common/column_right');

		$data['content_top'] = $this->load->controller('common/content_top');

		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		$data['footer'] = $this->load->controller('common/footer');

		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/module/forumtopic', $data));
		

	}
	public function insert() {
		
		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('extension/module/forum/addTopic', '', true);



			$this->response->redirect($this->url->link('account/login', '', true));

		}
		$this->load->language('extension/module/forum');
		
		$this->load->model('design/forum');
				
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$post_data = $this->request->post;
			
			$addTopic = $this->model_design_forum->addTopic($post_data);
				
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
			
				$data['topic'] = $post_data['name'];
				$data['author_name'] = $post_data['username'];
				$data['content_data'] = 'New topic added in to the discussion forum. You can enable or delete from admin panel.';

				$mail = new Mail();

				$mail->protocol = $this->config->get('config_mail_protocol');

				$mail->parameter = $this->config->get('config_mail_parameter');

				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');

				$mail->smtp_username = $this->config->get('config_mail_smtp_username');

				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');

				$mail->smtp_port = $this->config->get('config_mail_smtp_port');

				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

	

				$mail->setTo($this->config->get('config_email'));

				$mail->setFrom($post_data['email']);

				$mail->setSender(html_entity_decode($post_data['username'], ENT_QUOTES, 'UTF-8'));

				$mail->setSubject(html_entity_decode($post_data['name'], ENT_QUOTES, 'UTF-8'));

				$mail->setHtml($this->load->view('mail/forum', $data));
				
				$mail->send();			
			
		}

		$this->addTopic();
	}
	
	public function validateForm() {
	
		if ((utf8_strlen($this->request->post['username']) < 1) || (utf8_strlen($this->request->post['username']) > 32)) {
			$this->error['username'] = $this->language->get('error_username');			
		}
		if ((utf8_strlen($this->request->post['email']) < 1) || (utf8_strlen($this->request->post['email']) > 32)) {
			$this->error['email'] = $this->language->get('error_email');			
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');			
		}
		if (empty($this->request->post['note_description'])) {
			$this->error['description'] = $this->language->get('error_description');
		}
		

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	

	
	public function reply() {
	
		$this->load->language('extension/module/forum');
		
		$this->load->model('design/forum');
		
		$post = $this->request->post;

		
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
		
						
			$addReply = $this->model_design_forum->addReply($post);		
			
			$addMail  = $this->model_design_forum->addMail($post['forum_id']);	
			
			$getPost  = $this->model_design_forum->getPost($post['forum_id']);	


			$this->session->data['success'] = $this->language->get('text_success_reply');
					
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
			
			$this->response->redirect($this->url->link('extension/module/forum/getForum&forum_id='.$post['forum_id'], '', true));
		
		
		}
	}
}
?>