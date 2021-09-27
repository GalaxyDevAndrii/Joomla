<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgGurupaymentOffline extends JPlugin{
	var $_db = null;

	function __construct(&$subject, $config){
		$this->_db = JFactory :: getDBO();
		parent :: __construct($subject, $config);
	}

	function onReceivePayment($post){
		if($post['processor'] != 'offline'){
			return 0;
		}
		
		$params = new JRegistry($post['params']);
		$default = $this->params;
        
		$out['sid'] = $post['sid'];
		$out['order_id'] = $post['order_id'];
		$out['processor'] = $post['processor'];
		
		if(isset($post['txn_id'])){
			$out['processor_id'] = JFactory::getApplication()->input->get("tx", $post['txn_id'], "raw");
		}
		else{
			$out['processor_id'] = "";
		}
		
		if(isset($post['custom'])){
			$out['customer_id'] = JFactory::getApplication()->input->get('cm', $post['custom'], 'raw');
		}
		else{
			$out['customer_id'] = "";
		}
		
		if(isset($post['mc_gross'])){
			$mc_amount1 = JFactory::getApplication()->input->get('mc_amount1', $post['mc_gross'], "raw");
			$mc_amount3 = JFactory::getApplication()->input->get('mc_amount3', $mc_amount1, "raw");
			$out['price'] = JFactory::getApplication()->input->get('amount', $mc_amount3, "raw");
		}
		else{
			$out['price'] = "";
		}
		$out['pay'] = $post['pay'];
		
		if(isset($post['email'])){
			$out['email'] = $post['email'];
		}
		else{
			$out['email'] = "";
		}
		
		$out["Itemid"] = $post["Itemid"];

		if($out['pay'] == 'ipn'){
			$s_info = jcsPPGetInfo($params, $post, $default);
			$database = JFactory::getDBO();

			if(isset($s_info['txn_type'])){
				switch($s_info['txn_type']){
					case "subscr_signup":
						break;
					case "send_money":
					case "web_accept":
					case "subscr_payment":
						switch ($s_info['payment_status']){
							case 'Processed':
							case 'Completed':
								break;
							case 'Refunded':
								return;
								break;
							case 'In-Progress':
							case 'Pending':
								$out['pay'] = 'fail';
								break;
							default:
								return;
						}
						break;

					case 'recurring_payment':
						break;
								
					case "subscr_failed":
							break;
					case "subscr_eot":
					case "subscr_cancel":
						return;
						break;
					case "new_case":
						return;
						break;
					case "adjustment":
						default: 
						break;
				}
			}
		}
		return $out;
	}

	function onSendPayment($post){
		$db = JFactory::getDBO();
	
		if($post['processor'] != 'offline'){
			return false;
		}	

		if($post['params']){
			$params = $post['params'];
		}
		else{
			$params = $this->params;
		}
		
		$params = json_decode($params);
		
		$lang = JFactory::getLanguage();
        $lang->load('plg_gurupayment_offline', JPATH_ADMINISTRATOR);
		
		$cancel_return = JURI::root().'index.php?option=com_guru&controller=guruBuy&processor='.$post['processor'].'&task='.$post['task'].'&sid='.$post['sid'].'&order_id='.$post['order_id'].'&pay=fail';
		
		$ok_return = JURI::root().'index.php?option=com_guru&controller=guruBuy&processor='.$post['processor'].'&task='.$post['task'].'&sid='.$post['sid'].'&order_id='.$post['order_id'].'&pay=wait';
		
		$form  = '<form name="offlineform" action="index.php" method="post">';
		
		if(trim($params->instructions) != ""){
			$params->instructions = nl2br($params->instructions);

			$params->instructions = str_replace("[ORDER_ID]", intval($post["order_id"]), $params->instructions);

			$form .= '<div class="alert alert-info">'.$params->instructions.'</div>';
		}
		
		$form .= '<input type="button" class="btn btn-primary" onclick="window.location=\''.$cancel_return.'\';" value="'.JText::_("PLG_GURUPAYMENT_OFFLINE_CANCEL").'" />';
		$form .= '&nbsp;&nbsp;';
		$form .= '<input type="button" class="btn btn-warning" onclick="window.location=\''.$ok_return.'\';" value="'.JText::_("PLG_GURUPAYMENT_OFFLINE_OK").'" />';
		$form .= '</form>';
		
		return $form;
	}
}
?>