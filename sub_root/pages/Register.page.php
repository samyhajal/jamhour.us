<?php
class Register extends page
{
	function  DisplayBody()
	{
		$this->xtemp->assign('ERROR_MESSAGE', $_SESSION['error']);
		$cc			= new CC_Utility();
		$countries	= $cc->getCountries();
		foreach ( $countries as $country_code=>$country )
		{
			$selected	= ( $country_code == 'us' ) ? 'selected="selected"' : '';
			$this->xtemp->assign('SELECTED', $selected);
			$this->xtemp->assign('COUNTRY_CODE', $country_code);
			$this->xtemp->assign('COUNTRY', $country);
			$this->xtemp->parse('MAIN.COUNTRIES');
		}
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
		unset($_SESSION['error']);
	}

	function Load()
	{
		$this->addCSS('style.css');
		$this->addCSS('register.css');
		$this->addJS('dojo.js');
		$this->addJS('register.js');
		$this->cur_pg	= 'register';
	}

	function Process($method='')
	{
		$cc_contact							= new CC_Contact();
		$params								= $_POST;
		$reg_tix							= ( empty($_POST['individual_tix']) ) ? 0 : $_POST['individual_tix'];
		$sen_tix							= ( empty($_POST['senior_tix']) ) ? 0 : $_POST['senior_tix'];
		$stu_tix							= ( empty($_POST['student_tix']) ) ? 0 : $_POST['student_tix'];
		$total								= ($reg_tix*250) + ($sen_tix*150) + ($stu_tix*75);
		$_SESSION['register']['total']		= $total;

		$guest_first_name		= $params['guest_first_name'];
		$guest_last_name		= $params['guest_last_name'];
		$guest_middle_initial	= $params['guest_middle_initial'];
		$guest_email			= $params['guest_email'];
		$guest_custom_field2	= $params['guest_custom_fields'];
		unset($params['individual_tix']);
		unset($params['senior_tix']);
		unset($params['student_tix']);
		unset($params['guest_first_name']);
		unset($params['guest_last_name']);
		unset($params['guest_middle_initial']);
		unset($params['guest_email']);
		unset($params['guest_custom_fields']);
		if ( $cc_contact->subscriberExists($params['email_address']) == true )
		{
			$details	= $cc_contact->getSubscriberDetails($params['email_address']);
			$id			= explode('/', $details['id'][0]);
			$contact	= $cc_contact->getContact($id[7]);
			$params['home_phone']	= ( $_POST['residence'] == 0 ) ? $params['phone'] : (string)$details['home_number'];
			$params['work_phone']	= ( $_POST['residence'] == 1 ) ? $params['phone'] : (string)$details['work_number'];
			$params['Addr1']		= $params['address_line_1'];
			$params['City']			= $params['city_name'];
			$params['PostalCode']	= $params['zip_code'];
			unset($params['address_line_1']);
			unset($params['city_name']);
			unset($params['zip_code']);
			unset($params['phone']);
			unset($params['residence']);
			foreach ( $params as $key=>$val )
			{
				if ( !is_array($val) )
				{
					$tmp	= explode('_', $key);
					foreach ( $tmp as $i=>$k )
					{
						$tmp[$i]	= ucfirst($k);
					}
					$new_key	= implode('', $tmp);
					$params[$new_key]	= $val;
					if ( sizeof($tmp) > 1)
					{
						unset($params[$key]);
					}
				}
			}
			foreach ( $params['custom_fields'] as $key=>$val )
			{
				$field	= 'CustomField'. $key;
				$params[$field]	= $val;
				unset($params['custom_fields']);
			}
			$countries	= $cc_contact->getCountries();
			$states		= $cc_contact->getStates();
			$ccl		= new CC_List();
			$found		= false;
			if ( isset($contact->content->Contact->ContactLists->ContactList) )
			{
				foreach ( $contact->content->Contact->ContactLists->ContactList as $contact_list )
				{
					/**
					 * @todo: replace this url with appropriate Constant Contact list ID
					 * List of CC List IDs can be seen on /test.php
					 */
					if ( $contact_list['id'] == 'http://api.constantcontact.com/ws/customers/JamhourUS/lists/42' )
					{
						$found	= true;
					}
				}
			}
			if ( $found == false )
			{
				$count			= sizeof($contact->content->Contact->ContactLists->ContactList);
				/**
				 * @todo: replace this url with appropriate Constant Contact list ID
				 * List of CC List IDs can be seen on /test.php
				 */
				$contact->content->Contact->ContactLists->ContactList[$count]['id']	= 'http://api.constantcontact.com/ws/customers/JamhourUS/lists/42';
			}
			$contact->content->Contact->LastName		= ( !empty($params['LastName']) ) ? $params['LastName'] : $contact->content->Contact->LastName;
			$contact->content->Contact->FirstName		= ( !empty($params['FirstName']) ) ? $params['FirstName'] : $contact->content->Contact->FirstName;
			$contact->content->Contact->MiddleName		= ( !empty($params['MiddleName']) ) ? $params['MiddleName'] : $contact->content->Contact->MiddleName;
			$contact->content->Contact->CompanyName		= ( !empty($params['CompanyName']) ) ? $params['CompanyName'] : $contact->content->Contact->CompanyName;
			/*
			$contact->content->Contact->StateCode	= ( !empty($params['StateCode']) ) ? strtoupper($params['StateCode']) : $contact->content->Contact->StateCode;
			$contact->content->Contact->StateName	= $states[(string)$contact->content->Contact->StateCode];
			$contact->content->Contact->CountryCode	= $params['CountryCode'];
			$contact->content->Contact->CountryName	= $countries[$params['CountryCode']];
			 * 
			 */
			$contact->content->Contact->Addr1			= ( !empty($params['Addr1']) ) ? $params['Addr1'] : $contact->content->Contact->Addr1;
			$contact->content->Contact->City			= ( !empty($params['City']) ) ? $params['City'] : $contact->content->Contact->City;
			$contact->content->Contact->PostalCode		= ( !empty($params['PostalCode']) ) ? $params['PostalCode'] : $contact->content->Contact->PostalCode;
			$contact->content->Contact->HomePhone		= ( !empty($params['HomePhone']) ) ? $params['HomePhone'] : $contact->content->Contact->HomePhone;
			$contact->content->Contact->WorkPhone		= ( !empty($params['WorkPhone']) ) ? $params['WorkPhone'] : $contact->content->Contact->WorkPhone;
			$contact->content->Contact->CustomField2	= ( !empty($params['CustomField2']) ) ? $params['CustomField2'] : $contact->content->Contact->CustomField2;
			$contact->content->Contact->CustomField7	= ( !empty($params['CustomField7']) ) ? $params['CustomField7'] : $contact->content->Contact->CustomField7;
			$new	= $contact->asXML();
			$add	= $cc_contact->editSubscriber($details['id'][0], $contact->asXML());
		}
		else
		{
			$params	= $_POST;
			$params['status']	= '';
			$cc_list	= new CC_List();
			$lists		= $cc_list->getLists();
			foreach ( $lists as $list )
			{
				if ( $list['title'] == 'Website Testing' )
				{
					$params['lists']	= array($list['id']);
					break;
				}
			}
			if ( $_POST['residence'] == 0 )
			{
				$params['home_number']	= $_POST['phone'];
				$params['work_number']	= '';
			}
			else
			{
				$params['home_number']	= '';
				$params['work_number']	= $_POST['phone'];
			}
			$xml	= $cc_contact->createContactXML(null, $params);

			$add	= $cc_contact->addSubscriber($xml);
		}
		$this->AddGuests();
		if ( $method == 'ByCheck')
		{
			header("Location:http://".$_SERVER['HTTP_HOST']."/Register/Confirmation/ByCheck/");
		}
		else
		{
			$this->PayPalRedir();
		}
	}

	function AddGuests()
	{

		foreach ( $_POST['guest_email'] as $i=>$email )
		{
			if ( !empty($email) )
			{
				$cc_contact	= new CC_Contact();
				if ( $cc_contact->subscriberExists($email) == true )
				{
					$details	= $cc_contact->getSubscriberDetails($email);
					$id			= explode('/', $details['id'][0]);
					$contact	= $cc_contact->getContact($id[7]);
					$ccl		= new CC_List();
					$found		= false;
					foreach ( $contact->content->Contact->ContactLists->ContactList as $contact_list )
					{
						/**
						 * @todo: replace this url with appropriate Constant Contact list ID
						 * List of CC List IDs can be seen on /test.php
						 */
						if ( $contact_list['id'] == 'http://api.constantcontact.com/ws/customers/JamhourUS/lists/42' )
						{
							$found	= true;
						}
					}
					if ( $found == false )
					{
						$count			= sizeof($contact->content->Contact->ContactLists->ContactList);
						/**
						 * @todo: replace this url with appropriate Constant Contact list ID
						 * List of CC List IDs can be seen on /test.php
						 */
						$contact->content->Contact->ContactLists->ContactList[$count]['id']	= 'http://api.constantcontact.com/ws/customers/JamhourUS/lists/42';
					}
					$found	= false;
					foreach ( $contact->content->Contact->ContactLists->ContactList as $contact_list )
					{
						/**
						 * @todo: replace this url with appropriate Constant Contact list ID
						 * List of CC List IDs can be seen on /test.php
						 */
						if ( $contact_list['id'] == 'http://api.constantcontact.com/ws/customers/JamhourUS/lists/25' )
						{
							$found	= true;
						}
					}
					if ( $found == false )
					{
						$count			= sizeof($contact->content->Contact->ContactLists->ContactList);
						/**
						 * @todo: replace this url with appropriate Constant Contact list ID
						 * List of CC List IDs can be seen on /test.php
						 */
						$contact->content->Contact->ContactLists->ContactList[$count]['id']	= 'http://api.constantcontact.com/ws/customers/JamhourUS/lists/25';
					}
					$new	= $contact->asXML();
					$add	= $cc_contact->editSubscriber($details['id'][0], $contact->asXML());
				}
				else
				{
					$params						= array();
					$params['status']			= '';
					$params['email_address']	= $email;
					$params['first_name']		= $_POST['guest_first_name'][$i];
					$params['last_name']		= $_POST['guest_last_name'][$i];
					$params['middle_name']		= $_POST['guest_middle_initial'][$i];
					$params['company_name']		= '';
					$params['home_number']		= '';
					$params['work_number']		= '';
					$params['address_line_1']	= '';
					$params['city_name']		= '';
					$params['state_code']		= '';
					$params['country_code']		= '';
					$params['zip_code']			= '';
					$params['custom_fields'][2]	= $_POST['guest_custom_fields'][$i];
					
					$cc_list	= new CC_List();
					$lists		= $cc_list->getLists();
					foreach ( $lists as $list )
					{
						if ( $list['title'] == 'Website Testing' )
						{
							$params['lists']	= array($list['id']);
							break;
						}
					}
					$xml	= $cc_contact->createContactXML(null, $params);
					$add	= $cc_contact->addSubscriber($xml);
				}
			}
		}
	}
	function PayPalRedir()
	{
		$this->addCSS('style.css');
		$this->addCSS('register.css');
		$this->cur_pg	= 'register';
		$this->DisplayMeta();
		$this->DisplayHeader();
		$reg_tix	= ( empty($_POST['individual_tix']) ) ? 0 : $_POST['individual_tix'];
		$sen_tix	= ( empty($_POST['senior_tix']) ) ? 0 : $_POST['senior_tix'];
		$stu_tix	= ( empty($_POST['student_tix']) ) ? 0 : $_POST['student_tix'];
		$this->xtemp->assign('REGULAR_TIX_COUNT', $reg_tix);
		$this->xtemp->assign('SENIOR_TIX_COUNT', $sen_tix);
		$this->xtemp->assign('STUDENT_TIX_COUNT', $stu_tix);
		$item_i	= 0;
		if ( $reg_tix > 0 )
		{
			$item_i++;
			$this->xtemp->assign('ITEM_I', $item_i);
			$this->xtemp->parse('PAYPAL_REDIR.INDIVIDUAL_TICKET');
		}
		if ( $sen_tix > 0 )
		{
			$item_i++;
			$this->xtemp->assign('ITEM_I', $item_i);
			$this->xtemp->parse('PAYPAL_REDIR.SENIOR_TICKET');
		}
		if ( $stu_tix > 0 )
		{
			$item_i++;
			$this->xtemp->assign('ITEM_I', $item_i);
			$this->xtemp->parse('PAYPAL_REDIR.STUDENT_TICKET');
		}
		$this->xtemp->assign('SCRIPT_PATH', __PATH_JAVASCRIPT__);
		$this->xtemp->parse('PAYPAL_REDIR');
		$this->xtemp->out('PAYPAL_REDIR');
		$this->DisplayFooter();
	}

	function Confirmation($method = '')
	{
		$this->Load();
		$this->DisplayMeta();
		$this->DisplayHeader();
		if ( $method == 'ByCheck' )
		{
			$this->xtemp->assign('TOTAL', $_SESSION['register']['total']);
			$this->xtemp->parse('CONFIRMATION_CHECK');
			$this->xtemp->out('CONFIRMATION_CHECK');
		}
		else
		{
			$this->xtemp->parse('CONFIRMATION_PAYPAL');
			$this->xtemp->out('CONFIRMATION_PAYPAL');
		}
		$this->DisplayFooter();
	}
}
?>
