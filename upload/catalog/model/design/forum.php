<?php
class ModelDesignForum extends Model {	
	public function getForum($forum_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "forum WHERE forum_id=".$forum_id);
		return $query->rows;
	}
	
	public function getForums($data) {
		$query = "SELECT a.*,COUNT(CASE WHEN b.forum_id = '' THEN 0 ELSE b.forum_id END) AS reply,a.views FROM ".DB_PREFIX."forum AS a LEFT JOIN ".DB_PREFIX."forum_reply AS b ON a.forum_id = b.forum_id WHERE a.status=1 GROUP BY a.forum_id ORDER BY a.forum_id DESC ";
		
		if (isset($data['start']) || isset($data['limit'])) {

			if ($data['start'] < 0) {

				$data['start'] = 0;

			}



			if ($data['limit'] < 1) {

				$data['limit'] = 20;

			}



			$query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];

		}
		
		$query = $this->db->query($query);
		return $query->rows;
	}
	
	public function getForumsTotal() {
		$query = "SELECT COUNT(*) as total FROM ".DB_PREFIX."forum";	
		$query = $this->db->query($query);
		return $query->row['total'];
	}
	
	public function getForumsTotalAuthor($author) {
		$query = "SELECT COUNT(*) as total FROM ".DB_PREFIX."forum WHERE customer_id='".$author."' AND status='1'";	
		$query = $this->db->query($query);
		return $query->row['total'];
	}
	
	public function getAuthorForums($author,$data) {
		$query = "SELECT a.*,COUNT(CASE WHEN b.forum_id = '' THEN 0 ELSE b.forum_id END) AS reply,a.views FROM ".DB_PREFIX."forum AS a LEFT JOIN ".DB_PREFIX."forum_reply AS b ON a.forum_id = b.forum_id WHERE a.customer_id='".$author."' AND a.status='1' GROUP BY a.forum_id";
		
		if (isset($data['start']) || isset($data['limit'])) {

			if ($data['start'] < 0) {

				$data['start'] = 0;

			}



			if ($data['limit'] < 1) {

				$data['limit'] = 20;

			}



			$query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];

		}
		
		$query = $this->db->query($query);
		return $query->rows;
	}
	
	public function addTopic($data) {
		$query = $this->db->query("INSERT INTO " . DB_PREFIX . "forum SET name = '" . $this->db->escape($data['name']) . "',description = '" . $this->db->escape($data['note_description']) . "', username = '" . $this->db->escape($data['username']) . "', customer_id = '" . (int)$this->db->escape($data['customer_id']) . "', email = '" . $this->db->escape($data['email']) . "', avatar = '" . $this->db->escape($data['avatar']) . "',  status = 0");
	}
	
	public function addReply($data) {
		$query = $this->db->query("INSERT INTO " . DB_PREFIX . "forum_reply SET forum_id = '" . $this->db->escape($data['forum_id']) . "', username = '" . $this->db->escape($data['username']) . "', customer_id = '" . (int)$this->db->escape($data['customer_id']) . "',email = '" . $this->db->escape($data['email']) . "', avatar = '" . $this->db->escape($data['avatar']) . "', reply = '" . $this->db->escape($data['reply']) . "', status = 1");
	}
	
	public function getForumReply($forum_id) {
		$query = $this->db->query("SELECT a.*,b.name FROM ".DB_PREFIX."forum_reply AS a LEFT JOIN ".DB_PREFIX."forum AS b ON a.forum_id = b.forum_id WHERE a.status = 1 AND a.forum_id =".$forum_id);
		return $query->rows;
	}
	
	public function getForumViews($forum_id) {
		$query = $this->db->query("SELECT views FROM ".DB_PREFIX."forum WHERE forum_id=".$forum_id." LIMIT 0,1");
		return $query->rows;
	}
	
	public function addForumViews($view,$forum_id) {
		$query = $this->db->query("UPDATE ".DB_PREFIX."forum SET views=".$view." WHERE forum_id=".$forum_id);
	}
	public function addMail($forum_id) {
		$query = $this->db->query("SELECT username,email FROM ".DB_PREFIX."forum_reply WHERE forum_id = ".$forum_id." UNION SELECT username,email FROM ".DB_PREFIX."forum WHERE forum_id = ".$forum_id);
		return $query->rows;
	}
	public function getPost($forum_id) {
		$query = $this->db->query("SELECT name FROM ".DB_PREFIX."forum WHERE forum_id = ".$forum_id." LIMIT 0,1");
		return $query->rows;
	}
}
?>