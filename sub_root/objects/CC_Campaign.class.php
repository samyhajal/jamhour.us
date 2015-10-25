<?php
/**
    * Class that is used for ConstantCampaign CRUD management
   */
    class CC_Campaign extends CC_Utility {

       // set this to true to see the xml sent and the output received
       var $sent_recived_debug = false;
       var $usStates = array("AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "DC", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY");
       var $caStates = array("AB", "BC", "MB", "NB", "NL", "NT", "NS", "NU", "ON", "PE", "QC", "SK", "YT");
       var $armedForces = array("AA", "AE", "AP");

       /**
       * Method that returns a html sample for email campaign
       * @param string $sample [default is EmailContent]: EmailContent, EmailTextContent or
       * PermissionReminder
       * @param string $type [default is html]: html or text
       * @return a default content for email content or permission reminder
       */
       public function getEmailIntro($sample = 'EmailContent', $type = 'html') {
          switch($sample){
               case 'EmailContent':
                        $file = 'EmailContent.txt';
                        break;
               case 'EmailTextContent':
                        $file = 'EmailContent.txt';
                        $type = 'text';
                        break;
               case 'PermissionReminder':
                        $file = 'PermissionReminder.txt';
                        break;
               default:
                        $file = 'EmailContent.txt';
          }

            $handle = fopen("txt/$file", "rb");
            $contents = '';
                while (!feof($handle)) {
                        $contents .= fread($handle, 8192);
                }
            $contents = ($type == 'html') ? ($contents) : (trim(strip_tags($contents)));
            fclose($handle);
            return $contents;
       }




     /**
     * Method that retrieves campaingn collections from ConstantCampaign
     * If campaign_id is mentioned then only mentioned campaign is retrieved.
     * If campaign_id represents a status [SENT, DRAFT, RUNNING, SCHEDULED]
     * only the campaigns with that status will be retrieved
     * @param string $campaign_id [default is empty]
     * @return Bi-Dimenstional array with information about campaigns.
     */
     public function getCampaigns($campaign_id = '', $page = '') {
            $campaigns = array();
            $campaigns['items'] = array();

            switch($campaign_id){
                  case 'SENT':
                  case 'DRAFT':
                  case 'RUNNING':
                  case 'SCHEDULED':
                       $call = $this->apiPath.'/campaigns?status='.$campaign_id;
                       break;
                  case 'ALL':
                       $call = (!empty($page)) ? ($this->apiPath.$page) : ($this->apiPath.'/campaigns');
                       break;
                  default:
                       $call = $this->apiPath.'/campaigns/'.$campaign_id;
            }

            $return = $this->doServerCall($call);
            $parsedReturn = simplexml_load_string($return);
            //we parse here the link array to establish which are the next page and previous page
            if($parsedReturn != false){

            foreach ($parsedReturn->link as $item) {
                $attributes = $item->Attributes();
                if (! empty($attributes['rel']) && $attributes['rel'] == 'next') {
                    $tmp = explode($this->login, $attributes['href']);
                    $campaigns['next'] = $tmp[1];
                }
                if (! empty($attributes['rel']) && $attributes['rel'] == 'first') {
                    $tmp = explode($this->login, $attributes['href']);
                    $campaigns['first'] = $tmp[1];
                }
                if (! empty($attributes['rel']) && $attributes['rel'] == 'current') {
                    $tmp = explode($this->login, $attributes['href']);
                    $campaigns['current'] = $tmp[1];
                }
            }

            foreach ($parsedReturn->entry as $item) {
                $tmp = array();
                $tmp['id'] = (string) $item->id;
                $tmp['title'] = (string) $item->title;
                $tmp['name'] = (string) $item->content->Campaign->Name;
                $tmp['status'] = (string) $item->content->Campaign->Status;
                $timestamp = strtotime($item->content->Campaign->Date);
                $campaig_date = date("F j, Y, g:i a", $timestamp);
                $tmp['date'] = (string) $campaig_date;
                $campaigns['items'][] = $tmp;
              }

            }
            return $campaigns;
        }


     /**
     * Retrieves all the details for a specific campaign identified by $id.
     * @param string $id
     * @return array with all information about the campaign.
     */
     public function getCampaignDetails($id) {
     if (!empty($id)){
            $fullContact = array();
            $call = str_replace('http://', 'https://', $id);
            // Convert id URI to BASIC compliant
            $return = $this->doServerCall($call);
            $parsedReturn = simplexml_load_string($return);
            $fullCampaign['campaignId'] = $parsedReturn->id;
            $cmp_vars = get_object_vars($parsedReturn->content->Campaign);

            foreach ($cmp_vars as $var_name=>$cmp_item){
               $fullCampaign[$var_name] = $cmp_item;
            }

            $cmp_from_email = $parsedReturn->content->Campaign->FromEmail->EmailAddress;
            $fullCampaign['FromEmail'] = (string) $cmp_from_email;
            $cmp_reply_email = $parsedReturn->content->Campaign->ReplyToEmail->EmailAddress;
            $fullCampaign['ReplyToEmail'] = (string) $cmp_reply_email;
            $fullCampaign['lists'] = array();

            if ($parsedReturn->content->Campaign->ContactLists->ContactList) {
                foreach ($parsedReturn->content->Campaign->ContactLists->ContactList as $item) {
                    $fullCampaign['lists'][] = trim((string) $item->Attributes());
                }
            }
              return $fullCampaign;
          }  else {
              return false;
          }
        }

     /**
     * Check if a specific campaign exist already
     * @param string $id
     * @param string $new_name
     * @return a boolean value.
     */
     public function campaignExists($id = '', $new_name) {
         if(!empty($id)) {
         $call = $this->apiPath.'/campaigns/'.$id;
         $return = $this->doServerCall($call);
         $xml = simplexml_load_string($return);
         if ($xml !== false) {
               $id = $xml->content->Campaign->Attributes();
               $id = $id['id'];
               $name = $xml->content->Campaign->Name;
            } else {
                $id = null;
                $name = null;
            }
           $all_campaigns = $this->getCampaigns('ALL');
           $all_campaigns = $all_campaigns['items'];
           foreach ($all_campaigns as $key=>$item) {
               if ($item['name'] == $new_name)  {
                     return 1;  // 1 - the new campaign has a similar name with an old one
                     break;
               }
           }
           /**
            * 2 - this campaign already exist
            * 0 - this is a new campaign
           */
           return ($id != null) ? (2) : (0);
         }

     }


     /**
     * Method that delete a camaign; this will exclude
     * the removed campaign from overall statistics
     * @param string $id - campaign id
     * @return TRUE in case of success or FALSE otherwise
     */
     public function deleteCampaign($id) {
            if ( empty($id)) {  return false;  }
            $return = $this->doServerCall($id, '', 'DELETE');
            if (! empty($return) || $return === false) {  return false;  }
            return true;
        }

     /**
     * Upload a new campaign to ConstantContact server
     * @param string $campaignXML - formatted XML with campaign information
     * @return TRUE in case of success or FALSE otherwise
     */
     public function addCampaign($campaignXML) {
            $call = $this->apiPath.'/campaigns';
            $return = $this->doServerCall($call, $campaignXML, 'POST');
            $parsedReturn = simplexml_load_string($return);
            if ($return) {
                return true;
            } else {
                $xml = simplexml_load_string($campaignXML);
                $cmp_id = $xml->content->Campaign->Attributes();
                $cmp_id = $cmp_id['id'];
                $cmp_name = $xml->content->Campaign->Name;
             if(!empty($cmp_id)) {
                 $search_status = $this->campaignExists($cmp_id, $cmp_name);
                 switch($search_status){
                     case 0:
                        $error = 'An Error Occurred. The campaign could not be added.';
                        break;
                     case 1:
                        $error = 'The name of the campaign already exist. Each campaign must have a distinct name.';
                        break;
                     case 2:
                        $error = 'This campaign already exists.';
                        break;
                     default:
                        $error = 'An Error Occurred. The campaign could not be added.';
                 }
                $this->lastError = $error;
              }  else {
                $this->lastError = 'An Error Occurred. The campaign could not be added.';
              }
              return false;
            }

        }

     /**
     * Modifies a campaign
     * @param string $campaignId - identifies the id for the modified campaign
     * @param string $campaignXML - formed XML with campaign information
     * @return TRUE in case of success or FALSE otherwise
     */
     public function editCampaign($campaignId, $campaignXML) {
            $return = $this->doServerCall($campaignId, $campaignXML, 'PUT');
            if ($return === false) {
                $this->lastError = 'An Error Occurred. The campaign could not be edited.';
                return false;
            } else {
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
     }

     /**
     * Method that validate the current campaign before sending it to server
     * @param string $id
     * @param array $params
     * @return an error message or true
     */
     public function validateCampaign( $id, $params = array() ) {
         if( trim($params['cmp_name'])== '' ) {
             $this->lastError = '<i>Campaign Name</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_subject'])== '' ) {
             $this->lastError = '<i>Subject</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_from_name'])== '' ) {
             $this->lastError = '<i>From Name</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_from_email'])== '' ) {
             $this->lastError = '<i>From Email Address</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_reply_email'])== '' ) {
             $this->lastError = '<i>Reply Email Address</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_grt_name'])== '' ) {
             $this->lastError = '<i>Greeting Name</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_org_name'])== '' ) {
             $this->lastError = '<i>Organization Name</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_org_addr1'])== '' ) {
             $this->lastError = '<i>Address 1</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_org_city'])== '' ) {
             $this->lastError = '<i>City</i> is mandatory.';
             return true;
          } elseif( trim($params['org_zip'])== '' ) {
             $this->lastError = '<i>Zip/Postal Code</i> is mandatory.';
             return true;
          } elseif( trim($params['org_country'])== '' ) {
             $this->lastError = '<i>Country</i> is mandatory.';
             return true;
          } elseif( trim($params['cmp_html_body'])== '' ) {
             $this->lastError = '<i>HTML Body</i> is mandatory.';
             return true;
          } elseif ( $params["lists"] == NULL ) {
             $this->lastError = 'Choose at least <i>one Campaign</i> from the list.';
             return true;
          } else {
              if( trim($params['cmp_perm_reminder'])== 'YES') {
                    $reminder_text =  $params['cmp_txt_reminder'];
                    if(trim($reminder_text)== ''){
                            $this->lastError = '<i>Permission Reminder</i> is required.';
                            return true;
                    }
              }
              if(trim($params['org_country']) != '') {
                    if( trim($params['org_country'])== 'us' ) {
                            if(trim($params['org_state_us']) == '' ){
                                $this->lastError = '<i>State</i> is mandatory.';
                                return true;
                            }
                            if ( in_array($params['org_state_us'], $this->caStates) ) {
                                $this->lastError = '<i>US State</i> is required.';
                                return true;
                            }
                    } elseif( trim($params['org_country'])== 'ca' ) {
                            if(trim($params['org_state_us']) == '' ){
                                $this->lastError = '<i>State</i> is mandatory.';
                                return true;
                            }
                            if ( in_array($params['org_state_us'], $this->usStates) ) {
                                $this->lastError = '<i>CA State</i> is required.';
                                return true;
                            }
                    }
              }
              if( trim($params['cmp_as_webpage'])== 'YES' ) {
                    if(trim($params['cmp_as_webtxt'])== ''){
                            $this->lastError = '<i>Webpage Text</i> is required.';
                            return true;
                    }
                    if(trim($params['cmp_as_weblink'])== ''){
                            $this->lastError = '<i>Webpage Link Text</i> is required.';
                            return true;
                    }
              }
              if( trim($params['cmp_forward'])== 'YES') {
                    $fwd_email =  $params['cmp_fwd_email'];
                    if(trim($params['cmp_fwd_email'])== ''){
                            $this->lastError = '<i>Forward email</i> is required.';
                            return true;
                    }
              }
              if( trim($params['cmp_subscribe'])== 'YES') {
                    if(trim($params['cmp_sub_link'])== ''){
                            $this->lastError = '<i>Subscribe me</i> is required.';
                            return true;
                    }
              }
              else {        return false;        }
          }
     }


     /**
     * Method that compose the needed XML format for a campaign
     * @param string $id
     * @param array $params
     * @return Formed XML
     */
          public function createCampaignXML( $id, $params = array() ) {
            if (empty($id)) {  // Add a new Campaign
                $id = str_replace('https://', 'http://', $this->apiPath."/campaigns/1100546096289");
                $standard_id = str_replace('https://', 'http://', $this->apiPath."/campaigns");
            } else {
                $standard_id = $id;
            }
            $href = str_replace("http://api.constantcontact.com", "", $id);
            $standard_href = str_replace("https://api.constantcontact.com", "", $this->apiPath."/campaigns");               $update_date = date("Y-m-d").'T'.date("H:i:s").'+01:00';
            $xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><entry xmlns='http://www.w3.org/2005/Atom'></entry>";
            $xml_object = simplexml_load_string($xml_string);
            $link_node = $xml_object->addChild("link");
            $link_node->addAttribute("href", $standard_href); //[1st *href]
            $link_node->addAttribute("rel", "edit");
            $id_node = $xml_object->addChild("id", $standard_id);  //[1st *id]
            $title_node = $xml_object->addChild("title", htmlspecialchars($params['cmp_name'], ENT_QUOTES, 'UTF-8'));
            $title_node->addAttribute("type", "text");
            $updated_node = $xml_object->addChild("updated", htmlentities($update_date));
            $author_node = $xml_object->addChild("author");
            $author_name = $author_node->addChild("name", htmlentities("Constant Contact"));
            $content_node = $xml_object->addChild("content");
            $content_node->addAttribute("type", "application/vnd.ctct+xml");
            $campaign_node = $content_node->addChild("Campaign");
            $campaign_node->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
            $campaign_node->addAttribute("id", $id);  //[2nd *id]
            $name_node = $campaign_node->addChild("Name", urldecode(htmlspecialchars($params['cmp_name'], ENT_QUOTES, 'UTF-8')));
            $campaign_status =  !empty($params['cmp_status']) ? ($params['cmp_status']) : ('Draft');
            $status_node = $campaign_node->addChild("Status", urldecode(htmlentities($campaign_status)));
            $campaign_date = !empty($params['cmp_date']) ? ($params['cmp_date']) : ($update_date);
            $date_node = $campaign_node->addChild("Date", urldecode(htmlentities($campaign_date)));
            $subj_node = $campaign_node->addChild("Subject", urldecode(htmlspecialchars($params['cmp_subject'], ENT_QUOTES, 'UTF-8')));
            $from_name_node = $campaign_node->addChild("FromName", urldecode(htmlspecialchars($params['cmp_from_name'], ENT_QUOTES, 'UTF-8')));
            $view_as_webpage = (!empty($params['cmp_as_webpage']) &&  $params['cmp_as_webpage'] == 'YES') ? ('YES') : ('NO');
            $as_webpage_node = $campaign_node->addChild("ViewAsWebpage", urldecode(htmlentities($view_as_webpage)));
            #$as_web_lnk_txt = ($view_as_webpage == 'YES') ? ($params['cmp_as_weblink']) : ('');
            $as_web_lnk_txt = $params['cmp_as_weblink'];
            $as_weblink_node = $campaign_node->addChild("ViewAsWebpageLinkText", urldecode(htmlspecialchars(($as_web_lnk_txt), ENT_QUOTES, 'UTF-8')));
            #$as_web_txt = ($view_as_webpage == 'YES') ? ($params['cmp_as_webtxt']) : ('');
            $as_web_txt = $params['cmp_as_webtxt'];
            $as_webtxt_node = $campaign_node->addChild("ViewAsWebpageText", urldecode(htmlspecialchars(($as_web_txt), ENT_QUOTES, 'UTF-8')));
            $perm_reminder_node = $campaign_node->addChild("PermissionReminder", urldecode(htmlentities($params['cmp_perm_reminder'])));
            $permission_reminder_text = ($params['cmp_perm_reminder'] == 'YES') ? ($params['cmp_txt_reminder']) : ('');
            $text_reminder_node = $campaign_node->addChild("PermissionReminderText", urldecode(htmlspecialchars(($permission_reminder_text), ENT_QUOTES, 'UTF-8')));
            $grt_sal_node = $campaign_node->addChild("GreetingSalutation", htmlspecialchars(($params['cmp_grt_sal']), ENT_QUOTES, 'UTF-8'));
            $grt_name_node = $campaign_node->addChild("GreetingName", htmlentities($params['cmp_grt_name']));
            $grt_str_node = $campaign_node->addChild("GreetingString", htmlspecialchars($params['cmp_grt_str'], ENT_QUOTES, 'UTF-8'));
            $org_name_node = $campaign_node->addChild("OrganizationName", htmlspecialchars($params['cmp_org_name'], ENT_QUOTES, 'UTF-8'));
            $org_addr1_node = $campaign_node->addChild("OrganizationAddress1", htmlspecialchars($params['cmp_org_addr1'], ENT_QUOTES, 'UTF-8'));
            $org_addr2_node = $campaign_node->addChild("OrganizationAddress2", htmlspecialchars($params['cmp_org_addr2'], ENT_QUOTES, 'UTF-8'));
            $org_addr3_node = $campaign_node->addChild("OrganizationAddress3", htmlspecialchars($params['cmp_org_addr3'], ENT_QUOTES, 'UTF-8'));
            $org_city_node = $campaign_node->addChild("OrganizationCity", htmlspecialchars($params['cmp_org_city'], ENT_QUOTES, 'UTF-8'));
            switch($params['org_country']){
                case 'us':
                $us_state = $params['org_state_us'];
                break;
                case 'ca':
                $us_state = $params['org_state_us'];
                break;
                default:
                $us_state = '';
            }
            $org_state_us_node = $campaign_node->addChild("OrganizationState", htmlentities($us_state));
            switch($params['org_country']){
                case 'us':
                $international_state = '';
                break;
                case 'ca':
                $international_state = '';
                break;
                default:
                $international_state = htmlspecialchars($params['org_state'], ENT_QUOTES, 'UTF-8');
            }
            $org_state_name = $campaign_node->addChild("OrganizationInternationalState", htmlentities($international_state));
            $org_country_node = $campaign_node->addChild("OrganizationCountry", htmlentities($params['org_country']));
            $org_zip_node = $campaign_node->addChild("OrganizationPostalCode", htmlspecialchars($params['org_zip'], ENT_QUOTES, 'UTF-8'));
            $include_fwd_email = (!empty($params['cmp_forward']) && $params['cmp_forward'] == 'YES') ? ('YES') : ('NO');
            #$fwd_txt = ($include_fwd_email == 'YES') ? ($params['cmp_fwd_email']) :('');
            $fwd_txt = $params['cmp_fwd_email'];
            $fwd_node = $campaign_node->addChild("IncludeForwardEmail", htmlentities($include_fwd_email));
            $fwd_email_node = $campaign_node->addChild("ForwardEmailLinkText", htmlspecialchars(($fwd_txt), ENT_QUOTES, 'UTF-8'));
            $include_sub_link = (!empty($params['cmp_subscribe']) && $params['cmp_subscribe'] == 'YES') ? ('YES') : ('NO');
            $sub_node = $campaign_node->addChild("IncludeSubscribeLink", htmlentities($include_sub_link));
            #$sub_txt = ($include_sub_link == 'YES') ? ($params['cmp_sub_link']) : ('');
            $sub_txt = $params['cmp_sub_link'];
            $sub_link_node = $campaign_node->addChild("SubscribeLinkText", htmlspecialchars(($sub_txt), ENT_QUOTES, 'UTF-8'));
            $email_format_node = $campaign_node->addChild("EmailContentFormat", $params['cmp_mail_type']);
            if($params['cmp_type'] != 'STOCK'){
            $html_body_node = $campaign_node->addChild("EmailContent", htmlspecialchars($params['cmp_html_body'], ENT_QUOTES, 'UTF-8'));
            $text_body_node = $campaign_node->addChild("EmailTextContent", "<Text>".htmlspecialchars(strip_tags($params['cmp_text_body']), ENT_QUOTES, 'UTF-8')."</Text>");
            $campaign_style_sheet = ($params['cmp_mail_type'] == 'XHTML') ? ($params['cmp_style_sheet']) : ('');
            $style_sheet_node = $campaign_node->addChild("StyleSheet", htmlspecialchars($campaign_style_sheet, ENT_QUOTES, 'UTF-8'));
            }
            $campaignlists_node = $campaign_node->addChild("ContactLists");

            if ($params['lists']) {
                foreach ($params['lists'] as $list) {
                    $campaignlist_node = $campaignlists_node->addChild("ContactList");
                    $campaignlist_node->addAttribute("id", $list);
                    $campaignlink_node = $campaignlist_node->addChild("link");
                    $campaignlink_node->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
                    $campaignlink_node->addAttribute("href", str_replace("http://api.constantcontact.com", "", $list));
                    $campaignlink_node->addAttribute("rel", "self");
                }
            }

            $cmp_from_email = explode('|',$params['cmp_from_email']);
            $fromemail_node = $campaign_node->addChild("FromEmail");
            $femail_node = $fromemail_node->addChild("Email");
            $femail_node->addAttribute("id", $cmp_from_email[1]);
            $femail_node_link = $femail_node->addChild("link");
            $femail_node_link->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
            $femail_node_link->addAttribute("href", str_replace("http://api.constantcontact.com", "", $cmp_from_email[1]));
            $femail_node_link->addAttribute("rel", "self");
            $femail_addrs_node = $fromemail_node->addChild("EmailAddress", htmlentities($cmp_from_email[0]));               $cmp_reply_email = explode('|',$params['cmp_reply_email']);
            $replyemail_node = $campaign_node->addChild("ReplyToEmail");
            $remail_node = $replyemail_node->addChild("Email");
            $remail_node->addAttribute("id", $cmp_reply_email[1]);
            $remail_node_link = $remail_node->addChild("link");
            $remail_node_link->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
            $remail_node_link->addAttribute("href", str_replace("http://api.constantcontact.com", "", $cmp_reply_email[1]));
            $remail_node_link->addAttribute("rel", "self");
            $remail_addrs_node = $replyemail_node->addChild("EmailAddress", htmlentities($cmp_reply_email[0]));             $source_node = $xml_object->addChild("source");
            $sourceid_node = $source_node->addChild("id", $standard_id);  //[3th *id]
            $sourcetitle_node = $source_node->addChild("title", "Campaigns for customer: ".$this->login);
            $sourcetitle_node->addAttribute("type", "text");
            $sourcelink1_node = $source_node->addChild("link");
            $sourcelink1_node->addAttribute("href", "campaigns");  //[2nd *href]
            $sourcelink2_node = $source_node->addChild("link");
            $sourcelink2_node->addAttribute("href", "campaigns");  //[3th *href]
            $sourcelink2_node->addAttribute("rel", "self");
            $sourceauthor_node = $source_node->addChild("author");
            $sauthor_name = $sourceauthor_node->addChild("name", $this->login);
            $sourceupdate_node = $source_node->addChild("updated", htmlentities($update_date));

            $entry = $xml_object->asXML();
           // $search  = array('&gt;', '\"', '&#13;', '&#10;&#13;', '"/>', '&', '&amp;lt;', '�', '�');
           // $replace = array('>', '"', '', '', '" />', '&amp;', '&lt;', '&amp;Yuml;', '&amp;yuml;');
           // $entry = str_replace($search, $replace, $entry);

            if( $this->sent_recived_debug ) {
                echo "<div><p style=\"color: blue\">We sent the following XML:</p>  $entry  </div><hr/>";
            }

            return $entry;
        }
	}
?>
