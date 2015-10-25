<?php
/**
    * Class that is used for ConstantConact CRUD management
    */
	class CC_Contact extends CC_Utility {

    /**
     * Method that checks if a subscriber already exist
     * @param string $email
     * @return subscriber`s id if it exists or false if it doesn't
     */
	 public	function subscriberExists($email = '') {
		 $call = $this->apiPath.'/contacts?email='.$email;
		 $return = $this->doServerCall($call);
		 $xml = simplexml_load_string($return);
		 $id = $xml->entry->id;
		 if($id){ return $id; }
		 else { return false; }
	 }
	 public function getContact($id)
	 {
		$call			= $this->apiPath.'/contacts/'. $id;
		$return			= $this->doServerCall($call);
		$parsedReturn	= simplexml_load_string($return);
		return $parsedReturn;
	 }
	 /**
     * Method that retrieves from Constant Contact a collection with all the Subscribers
     * If email parameter is mentioned then only mentioned contact is retrieved.
     * @param string $email
     * @return Bi-Dimenstional array with information about contacts.
     */
	 public	function getSubscribers($email = '', $page = '') {
			$contacts = array();
			$contacts['items'] = array();

			if (! empty($email)) {
				$call = $this->apiPath.'/contacts?email='.$email;
			} else {
				if (! empty($page)) {
					$call = $this->apiPath.$page;
				} else {
					$call = $this->apiPath.'/contacts';
				}
			}

			$return = $this->doServerCall($call);
			$parsedReturn = simplexml_load_string($return);
			// We parse here the link array to establish which are the next page and previous page
			foreach ($parsedReturn->link as $item) {
				$attributes = $item->Attributes();

				if (! empty($attributes['rel']) && $attributes['rel'] == 'next') {
					$tmp = explode($this->login, $attributes['href']);
					$contacts['next'] = $tmp[1];
				}
				if (! empty($attributes['rel']) && $attributes['rel'] == 'first') {
					$tmp = explode($this->login, $attributes['href']);
					$contacts['first'] = $tmp[1];
				}
				if (! empty($attributes['rel']) && $attributes['rel'] == 'current') {
					$tmp = explode($this->login, $attributes['href']);
					$contacts['current'] = $tmp[1];
				}
			}

			foreach ($parsedReturn->entry as $item) {
				$tmp = array();
				$tmp['id'] = (string) $item->id;
				$tmp['title'] = (string) $item->title;
				$tmp['status'] = (string) $item->content->Contact->Status;
				$tmp['EmailAddress'] = (string) $item->content->Contact->EmailAddress;
				$tmp['EmailType'] = (string) $item->content->Contact->EmailType;
				$tmp['Name'] = (string) $item->content->Contact->Name;
				$contacts['items'][] = $tmp;
			}

			return $contacts;
		}

	 /**
     * Retrieves all the details for a specific contact identified by $email.
     * @param string $email
     * @return array with all information about the contact.
     */
	 public	function getSubscriberDetails($email) {
			$contact = $this->getSubscribers($email);
			$fullContact = array();
			$call = str_replace('http://', 'https://', $contact['items'][0]['id']);
			// Convert id URI to BASIC compliant
			$return = $this->doServerCall($call);
			$parsedReturn = simplexml_load_string($return);
			$fullContact['id'] = $parsedReturn->id;
			$fullContact['email_address'] = $parsedReturn->content->Contact->EmailAddress;
			$fullContact['first_name'] = $parsedReturn->content->Contact->FirstName;
			$fullContact['last_name'] = $parsedReturn->content->Contact->LastName;
			$fullContact['middle_name'] = $parsedReturn->content->Contact->MiddleName;
			$fullContact['company_name'] = $parsedReturn->content->Contact->CompanyName;
			$fullContact['job_title'] = $parsedReturn->content->Contact->JobTitle;
			$fullContact['home_number'] = $parsedReturn->content->Contact->HomePhone;
			$fullContact['work_number'] = $parsedReturn->content->Contact->WorkPhone;
			$fullContact['address_line_1'] = $parsedReturn->content->Contact->Addr1;
			$fullContact['address_line_2'] = $parsedReturn->content->Contact->Addr2;
			$fullContact['address_line_3'] = $parsedReturn->content->Contact->Addr3;
			$fullContact['city_name'] = (string) $parsedReturn->content->Contact->City;
			$fullContact['state_code'] = (string) $parsedReturn->content->Contact->StateCode;
			$fullContact['state_name'] = (string) $parsedReturn->content->Contact->StateName;
			$fullContact['country_code'] = $parsedReturn->content->Contact->CountryCode;
			$fullContact['zip_code'] = $parsedReturn->content->Contact->PostalCode;
			$fullContact['sub_zip_code'] = $parsedReturn->content->Contact->SubPostalCode;
			$fullContact['custom_field_1'] = $parsedReturn->content->Contact->CustomField1;
			$fullContact['custom_field_2'] = $parsedReturn->content->Contact->CustomField2;
			$fullContact['custom_field_3'] = $parsedReturn->content->Contact->CustomField3;
			$fullContact['custom_field_4'] = $parsedReturn->content->Contact->CustomField4;
			$fullContact['custom_field_5'] = $parsedReturn->content->Contact->CustomField5;
			$fullContact['custom_field_6'] = $parsedReturn->content->Contact->CustomField6;
			$fullContact['custom_field_7'] = $parsedReturn->content->Contact->CustomField7;
			$fullContact['custom_field_8'] = $parsedReturn->content->Contact->CustomField8;
			$fullContact['custom_field_9'] = $parsedReturn->content->Contact->CustomField9;
			$fullContact['custom_field_10'] = $parsedReturn->content->Contact->CustomField10;
			$fullContact['custom_field_11'] = $parsedReturn->content->Contact->CustomField11;
			$fullContact['custom_field_12'] = $parsedReturn->content->Contact->CustomField12;
			$fullContact['custom_field_13'] = $parsedReturn->content->Contact->CustomField13;
			$fullContact['custom_field_14'] = $parsedReturn->content->Contact->CustomField14;
			$fullContact['custom_field_15'] = $parsedReturn->content->Contact->CustomField15;
			$fullContact['notes'] = $parsedReturn->content->Contact->Note;
			$fullContact['mail_type'] = $parsedReturn->content->Contact->EmailType;
			$fullContact['status'] = $parsedReturn->content->Contact->Status;
			$fullContact['lists'] = array();

			if ($parsedReturn->content->Contact->ContactLists->ContactList) {
				foreach ($parsedReturn->content->Contact->ContactLists->ContactList as $item) {
					$fullContact['lists'][] = trim((string) $item->Attributes());
				}
			}

			return $fullContact;
		}

	 /**
     * Method that modifies a contact State to DO NOT MAIL
     * @param string $email - contact email address
     * @return TRUE in case of success or FALSE otherwise
     */
	 public	function deleteSubscriber($email) {
			if ( empty($email)) {  return false;   }
			$contact = $this->getSubscribers($email);
			$id = $contact['items'][0]['id'];
			$return = $this->doServerCall($id, '', 'DELETE');
			if (! empty($return)) {  return false;  }
			return true;
		}

	 /**
     * Method that modifies a contact State to REMOVED
     * @param string $email - contact email address
     * @return TRUE in case of success or FALSE otherwise
     */
	 public	function removeSubscriber($email) {
			$contact = $this->getSubscriberDetails($email);
			$contact['lists'] = array();
			$xml = $this->createContactXML($contact['id'], $contact);

			if ($this->editSubscriber($contact['id'], $xml)) {
				return true;
			} else {
				return false;
			}
		}

	 /**
     * Upload a new contact to Constant Contact server
     * @param strong $contactXML - formatted XML with contact information
     * @return TRUE in case of success or FALSE otherwise
     */
	 public	function addSubscriber($contactXML) {
			$call = $this->apiPath.'/contacts';
			$return = $this->doServerCall($call, $contactXML, 'POST');
			$parsedReturn = simplexml_load_string($return);
			if ($return) {
				return true;
			} else {
				$xml = simplexml_load_string($contactXML);
				$emailAddress = $xml->content->Contact->EmailAddress;
				if ($this->subscriberExists($emailAddress)){
				$this->lastError = 'This contact already exists. <a href="edit_contact.php?email='.$emailAddress.'">Click here</a> to edit the contact details.';
				} else { $this->lastError = 'An Error Occurred'; }
				return false;
			}
		}

	 /**
     * Modifies a contact
     * @param string $contactUrl - identifies the url for the modified contact
     * @param string $contactXML - formed XML with contact information
     * @return TRUE in case of success or FALSE otherwise
     */
	 public	function editSubscriber($contactUrl, $contactXML) {
			$return = $this->doServerCall($contactUrl, $contactXML, 'PUT');
			if (! empty($return)) {
				if (strpos($return, '<') !== false) {
					$parsedReturn = simplexml_load_string($return);
					if (! empty($parsedReturn->message)) {
						$this->lastError = $parsedReturn->message;
					}
				} else {
					$this->lastError = $parsedReturn->message;
				}
				return false;
			}
			return true;
		}

	 /**
     * Method that compose the needed XML format for a contact
     * @param string $id
     * @param array $params
     * @return Formed XML
     */
	 public	function createContactXML($id, $params = array()) {
			if ( empty($id)) {
				$id = "urn:uuid:E8553C09F4xcvxCCC53F481214230867087";
			}

			$update_date = date("Y-m-d").'T'.date("H:i:s").'+01:00';
			$xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><entry xmlns='http://www.w3.org/2005/Atom'></entry>";
			$xml_object = simplexml_load_string($xml_string);
			$title_node = $xml_object->addChild("title", htmlspecialchars(("TitleNode"), ENT_QUOTES, 'UTF-8'));
			$updated_node = $xml_object->addChild("updated", htmlspecialchars(($update_date), ENT_QUOTES, 'UTF-8'));
			$author_node = $xml_object->addChild("author");
			$author_name = $author_node->addChild("name", ("CTCT Samples"));
			$id_node = $xml_object->addChild("id", htmlspecialchars(($id),ENT_QUOTES, 'UTF-8'));
			$summary_node = $xml_object->addChild("summary", htmlspecialchars(("Customer document"),ENT_QUOTES, 'UTF-8'));
			$summary_node->addAttribute("type", "text");
			$content_node = $xml_object->addChild("content");
			$content_node->addAttribute("type", "application/vnd.ctct+xml");
			$contact_node = $content_node->addChild("Contact", htmlspecialchars(("Customer document"), ENT_QUOTES, 'UTF-8'));
			$contact_node->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
			$email_node = $contact_node->addChild("EmailAddress", htmlspecialchars(($params['email_address']), ENT_QUOTES, 'UTF-8'));
			$fname_node = $contact_node->addChild("FirstName", urldecode(htmlspecialchars(($params['first_name']), ENT_QUOTES, 'UTF-8')));
			$lname_node = $contact_node->addChild("LastName", urldecode(htmlspecialchars(($params['last_name']), ENT_QUOTES, 'UTF-8')));
			$lname_node = $contact_node->addChild("MiddleName", urldecode(htmlspecialchars(($params['middle_name']), ENT_QUOTES, 'UTF-8')));
			$lname_node = $contact_node->addChild("CompanyName", urldecode(htmlspecialchars(($params['company_name']), ENT_QUOTES, 'UTF-8')));
			//$lname_node = $contact_node->addChild("JobTitle", urldecode(htmlspecialchars(($params['job_title']), ENT_QUOTES, 'UTF-8')));

			if ($params['status'] == 'Do Not Mail') {
				$this->actionBy = 'ACTION_BY_CONTACT';
			}

			$optin_node = $contact_node->addChild("OptInSource", htmlspecialchars($this->actionBy));
			$hn_node = $contact_node->addChild("HomePhone", htmlspecialchars($params['home_number'], ENT_QUOTES, 'UTF-8'));
			$wn_node = $contact_node->addChild("WorkPhone", htmlspecialchars($params['work_number'], ENT_QUOTES, 'UTF-8'));
			$ad1_node = $contact_node->addChild("Addr1", htmlspecialchars($params['address_line_1'], ENT_QUOTES, 'UTF-8'));
			//$ad2_node = $contact_node->addChild("Addr2", htmlspecialchars($params['address_line_2'], ENT_QUOTES, 'UTF-8'));
			//$ad3_node = $contact_node->addChild("Addr3", htmlspecialchars($params['address_line_3'], ENT_QUOTES, 'UTF-8'));
			$city_node = $contact_node->addChild("City", htmlspecialchars($params['city_name'], ENT_QUOTES, 'UTF-8'));
			$state_node = $contact_node->addChild("StateCode", htmlspecialchars($params['state_code'], ENT_QUOTES, 'UTF-8'));
			//$state_name = $contact_node->addChild("StateName", htmlspecialchars($params['state_name'], ENT_QUOTES, 'UTF-8'));
			$ctry_node = $contact_node->addChild("CountryCode", htmlspecialchars($params['country_code'], ENT_QUOTES, 'UTF-8'));
			$zip_node = $contact_node->addChild("PostalCode", htmlspecialchars($params['zip_code'], ENT_QUOTES, 'UTF-8'));
			//$subzip_node = $contact_node->addChild("SubPostalCode", htmlspecialchars($params['sub_zip_code'], ENT_QUOTES, 'UTF-8'));
			//$note_node = $contact_node->addChild("Note", htmlspecialchars($params['notes'], ENT_QUOTES, 'UTF-8'));
			//$emailtype_node = $contact_node->addChild("EmailType", htmlspecialchars($params['mail_type'], ENT_QUOTES, 'UTF-8'));

			if (! empty($params['custom_fields'])) {
				foreach ($params['custom_fields'] as $k=>$v) {
					$contact_node->addChild("CustomField".$k, htmlspecialchars(($v), ENT_QUOTES, 'UTF-8'));
				}
			}

			$contactlists_node = $contact_node->addChild("ContactLists");
			if ($params['lists']) {
				foreach ($params['lists'] as $tmp) {
					$contactlist_node = $contactlists_node->addChild("ContactList");
					$contactlist_node->addAttribute("id", $tmp);
				}
			}

			$entry = $xml_object->asXML();
			return $entry;
		}

    }
?>
