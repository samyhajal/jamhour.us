<?php
class JoinUs extends page
{
	function Load()
	{
		$this->addCSS('style.css');
		$this->addCSS('joinus.css');
		$this->addJS('dojo.js');
		$this->addJS('joinus.js');

		$this->cur_pg	= 'joinus';
	}
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

	function Process()
	{
		$cc_contact				= new CC_Contact();
		$params					= $_POST;
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
		$_SESSION['email_to']	= ( isset($params['email_address']) ) ? $params['email_address'] : $params['EmailAddress'];
		header("Location:http://".$_SERVER['HTTP_HOST']."/JoinUs/Confirmation/");
	}

	function Confirmation()
	{
		$to	= $_SESSION['email_to'];
		unset($_SESSION['email_to']);
		$this->Load();
		$this->DisplayMeta();
		$this->DisplayHeader();
		$this->xtemp->parse('CONFIRMATION');
		$this->xtemp->out('CONFIRMATION');
		$this->DisplayFooter();
		$xtemp		= new XTemplate('email.html', 'html');
		$emailer	= new emailer('html', $xtemp);
		/**
		 * @todo: Change this email address to the appropriate sending out address
		 */
		$emailer->send($to, 'test@test.com', 'Test');
	}
}
?>