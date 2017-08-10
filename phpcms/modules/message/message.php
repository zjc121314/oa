<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class message extends admin {
	function __construct() {
		parent::__construct();
		pc_base::load_app_func('global');//���ô���
		$this->db = pc_base::load_model('message_model');
		$this->group_db = pc_base::load_model('message_group_model');
		$this->info_db = pc_base::load_model('message_info_model');
		$this->detail_db = pc_base::load_model('member_detail_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->_username = param::get_cookie('admin_username');
		$this->_userid = param::get_cookie('userid');
		pc_base::load_sys_class('form');
 		foreach(L('select') as $key=>$value) {
			$trade_status[$key] = $value;
		}
		$this->trade_status = $trade_status;
	} 
	
	public function init() {

		$sql = "select d.*,GROUP_CONCAT(e.recipientname) as recipientname,GROUP_CONCAT(e.recipientid) as recipientid FROM v9_message_info as e,v9_message as d WHERE e.messageid=d.messageid ";
		$sql= $sql." GROUP BY d.messageid order by d.messageid desc"; 
		$search = pc_base::load_model('get_model');
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $search->multi_listinfo($sql,$page); //���ز�ѯ���
		$pages = $search->pages;//���ط�ҳ
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=message&c=message&a=message_send\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
		$trade_status = $this->trade_status;
		include $this->admin_tpl('message_list');
	}
	
	/**
 	 * Ⱥ����Ϣ����  ...
	 */
	public function message_group_manage() {
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->group_db->listinfo($where,$order = 'id DESC',$page, $pages = '12');
		$pages = $this->db->pages;
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=message&c=message&a=message_send\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
 		include $this->admin_tpl('message_group_list');
	}
	 /*
	 *�ж��û����Ƿ���� 
	 */
	 public function public_name() {
		$tousername = isset($_GET['tousername']) && trim($_GET['tousername']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['tousername'])) : trim($_GET['tousername'])) : exit('0');
	 	//���ܷ����Լ�
		if($tousername == $this->_username){
				exit('0');
			}
		//�ж��û����Ƿ����
		$member_interface = pc_base::load_app_class('member_interface', 'member');
		if ($tousername) {
			$data = $member_interface->get_member_info($tousername, 2);
			if ($data!='-1') {
				exit('1');
			} else {
				exit('0');
			}
		} else {
			exit('0');
		}
	 }
	
	/**
	 * ɾ������Ϣ 
	 * @param	intval	$sid	����ϢID���ݹ�ɾ��
	 */
	public function delete() {
		if((!isset($_GET['messageid']) || empty($_GET['messageid'])) && (!isset($_POST['messageid']) || empty($_POST['messageid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
				
			if(is_array($_POST['messageid'])){
				foreach($_POST['messageid'] as $messageid_arr) {
					//����ɾ����������
					$this->info_db->delete(array('messageid'=>$messageid_arr));
					$this->db->delete(array('messageid'=>$messageid_arr));
				}
				showmessage(L('operation_success'),'?m=message&c=message');
			}else{
				$messageid = intval($_GET['messageid']);
				if($messageid < 1) return false;
				//ɾ������Ϣ
				$deleteinfo = $this->info_db->delete(array('messageid'=>$messageid));
				$result = $this->db->delete(array('messageid'=>$messageid));
				if($result || $deleteinfo)
				{
					showmessage(L('operation_success'),'?m=message&c=message');
				}else {
					showmessage(L("operation_failure"),'?m=message&c=message');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}

	
	/**
	 * ɾ��ϵͳ ����Ϣ 
	 * @param	intval	$sid	Ⱥ������ϢID���ݹ�ɾ��
	 */
	public function delete_group() {
		if((!isset($_GET['message_group_id']) || empty($_GET['message_group_id'])) && (!isset($_POST['message_group_id']) || empty($_POST['message_group_id']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
				
			if(is_array($_POST['message_group_id'])){
				foreach($_POST['message_group_id'] as $messageid_arr) {
					//����ɾ��ϵͳ��Ϣ
					$this->group_db->delete(array('id'=>$messageid_arr));
				}
				showmessage(L('operation_success'),'?m=message&c=message&a=message_group_manage');
			}else{
				$group_id = intval($_GET['message_group_id']);
				if($group_id < 1) return false;
				//ɾ������Ϣ
				$result = $this->group_db->delete(array('id'=>$group_id));
				if($result){
					showmessage(L('operation_success'),'?m=message&c=message&a=message_group_manage');
				} else {
					showmessage(L("operation_failure"),'?m=message&c=message&a=message_group_manage');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	
	 /**
	 * ����������ɫ Ⱥ����Ϣ
	 */
	public function message_send() {
		if(isset($_POST['dosubmit'])) {
			//����Ⱥ����
			$group_message = array ();
			if(empty($_POST['info']['subject'])||empty($_POST['info']['content'])) return false;
			$group_message['subject'] = $_POST['info']['subject'];
			$group_message['content'] = $_POST['info']['content'];
			$group_message['typeid']    = $_POST['info']['type'];
			$group_message['inputtime']    = SYS_TIME;
			if($group_message['typeid']==1){
				$group_message['groupid'] = $_POST['info']['groupid'];
			}else {
				$group_message['groupid'] = $_POST['info']['roleid'];
			}
 			$result_id = $this->group_db->insert($group_message,true);
 			if(!$result_id){
 				showmessage(L('mass_failure'),HTTP_REFERER);
 			}
  			showmessage(L('operation_success'),HTTP_REFERER,'', 'add');
 		} else {
			$show_validator = $show_scroll = $show_header = true;
			//LOAD ��Ա��ģ��
			$member_group = pc_base::load_model('member_group_model');
			$member_group_infos = $member_group->select('','*','',$order = 'groupid ASC');
			//LOAD ����Ա��ɫģ��
			$role = pc_base::load_model('admin_role_model');
			$role_infos = $role->select('','*','',$order = 'roleid ASC');
			include $this->admin_tpl('message_send');
		}

	} 



	/**
 * ����Ϣ
 */
	public function send_one() {

	if(isset($_POST['dosubmit'])) {
		$username= $this->_username;
		$send_to_id =$_POST['info']['send_to_id'];
		$useridarr = $_POST['info']['send_to_id'];
		$userid_arr = explode(',',$useridarr);
		$num=0;//ͳ�Ʒ����˶��ٸ�
		$subject = $_POST['info']['subject'];
		$content = $_POST['info']['content'];
		
		$files = $_POST[downfiles_fileurl];
		$files_alt = $_POST[downfiles_filename];
		$array = $temp = array();
		if(!empty($files)) {
			foreach($files as $key=>$file) {
					$temp['fileurl'] = $file;
					$temp['filename'] = $files_alt[$key];
					$array[$key] = $temp;
			}
		}
		$downfilesarray = array2string($array);
		//ar_dump($downfilesarray);

		$message_time = SYS_TIME;
		$messageid=$this->db->add_message($send_to_id,$username,$message_time,$subject,$content,$downfilesarray,true);
		//echo $messageid;
			if($messageid){
				foreach ($userid_arr as $_k) {
					if(is_numeric($_k)) {
					$member_db = pc_base::load_model('member_model');
					$memberinfo = $member_db->get_one(array('userid'=>$_k));
					//echo $memberinfo['username'];
						if(isset($memberinfo['username'])){
						$tousernameid=$memberinfo['userid'];	
						$tousername=$memberinfo['username'];
						$tousernick=$memberinfo['nickname'];
						$receivetype='to';
						$this->info_db->add_message_info($messageid,$message_time,$tousername,$tousernameid,$tousernick,$receivetype,true);
						$num=$num+1; //�ɹ�����һ������ô��1
						}
					}
				}
			}
	showmessage(L('�ɹ�����'.$num.'���ʼ���'),HTTP_REFERER);
	
	//$userid =$_POST['userid'];
	//$memberinfo=get_memberinfo_all($userid);
	//$tousername= $memberinfo['username'];
	//$subject = $_POST['info']['subject'];
	//$content = $_POST['info']['content'];
	//$this->db->add_message($tousername,$username,$subject,$content,true);
	//showmessage(L('operation_success'),HTTP_REFERER);
	} else {
	$show_validator = $show_scroll =  true;
	$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=message&c=message&a=message_send\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));

	include $this->admin_tpl('message_send_one');
	}
	}

	
	/**
	 * �ռ��� 
	 */
	public function my_inbox() {
		$where = array('send_to_id'=>$this->_username,'folder'=>'inbox');
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db->listinfo($where,$order = 'messageid DESC',$page, $pages = '12');
		$pages = $this->db->pages;
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=message&c=message&a=message_send\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
		$trade_status = $this->trade_status;
		include $this->admin_tpl('message_inbox_list');
		
	}
	
	/**
	 * ɾ��-�ռ������Ϣ 
	 * @param	intval	$sid	����ϢID���ݹ�ɾ��
	 */
	public function delete_inbox() {
		if((!isset($_GET['messageid']) || empty($_GET['messageid'])) && (!isset($_POST['messageid']) || empty($_POST['messageid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
				
			if(is_array($_POST['messageid'])){
				foreach($_POST['messageid'] as $messageid_arr) {
					//����ɾ������Ϣ
					$this->db->update(array('folder'=>'outbox'),array('messageid'=>$messageid_arr,'send_to_id'=>$this->_username));
				}
				showmessage(L('operation_success'), HTTP_REFERER);
			}else{
				$messageid = intval($_GET['messageid']);
				if($messageid < 1) return false;
				//ɾ����������Ϣ
				$result = $this->db->update(array('folder'=>'outbox'),array('messageid'=>$messageid,'send_to_id'=>$this->_username));
				showmessage(L('operation_success'), HTTP_REFERER);
			}
			
		}
	}
	
	/**
	 * ������ 
	 */
	public function my_outbox() {
		
		$where = array('send_from_id'=>$this->_username,'del_type'=>'0');
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db->listinfo($where,$order = 'messageid DESC',$page, $pages = '12');
		$pages = $this->db->pages;
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=message&c=message&a=message_send\', title:\''.L('all_send_message').'\', width:\'550\', height:\'350\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
		$trade_status = $this->trade_status;
		include $this->admin_tpl('message_outbox_list');
	}
	
	/**
	 * ɾ��-���������Ϣ 
	 * @param	intval	$sid	����ϢID���ݹ�ɾ��
	 */
	public function delete_outbox() {
		if((!isset($_GET['messageid']) || empty($_GET['messageid'])) && (!isset($_POST['messageid']) || empty($_POST['messageid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
				
			if(is_array($_POST['messageid'])){
				foreach($_POST['messageid'] as $messageid_arr) {
					//����ɾ������Ϣ
					$this->db->update(array('del_type'=>'1'),array('messageid'=>$messageid_arr,'send_from_id'=>$this->_username));
				}
				showmessage(L('operation_success'), HTTP_REFERER);
			}else{
				$messageid = intval($_GET['messageid']);
				if($messageid < 1) return false;
				//ɾ����������Ϣ
				$result = $this->db->update(array('del_type'=>'1'),array('messageid'=>$messageid,'send_from_id'=>$this->_username));
				showmessage(L('operation_success'), HTTP_REFERER);
			}
			
		}
	}
	
	/**
	 * ����Ϣ����
	 */
	public function search_message() {
		if(isset($_POST['dosubmit'])){
				$where = '';
				extract($_POST['search']);
				if(!$username && !$start_time && !$end_time){
					$where = "";
				}
				if($username){
					//�ж��ǲ�ѯ����,�ռ����Ƿ�����¼
					if($status==""){
						$where .= $where ?  " AND send_from_id='$username' or send_to_id='$username'" : " send_from_id='$username' or send_to_id='$username'";
					} else {
						$where .= $where ?  " AND $status='$username'" : " $status='$username'";
					}
 				}
				if($start_time && $end_time) {
					$start = strtotime($start_time);
					$end = strtotime($end_time);
					//$where .= "AND `message_time` >= '$start' AND `message_time` <= '$end' ";
					$where .= $where ? "AND `message_time` >= '$start' AND `message_time` <= '$end' " : " `message_time` >= '$start' AND `message_time` <= '$end' ";
				}
  		} 
  		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db->listinfo($where,$order = 'messageid DESC',$page, $pages = '12');
		$pages = $this->db->pages;
 		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=message&c=message&a=message_send\', title:\''.L('all_send_message').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
		$trade_status = $this->trade_status;
 		include $this->admin_tpl('message_search_list');
	}
	
	
}
?>