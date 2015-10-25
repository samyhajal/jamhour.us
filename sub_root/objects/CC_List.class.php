<?php
/**
    * Class that is used for retrieving
    * all the Email Lists from Constant Contact and
    * all Registered Email Addresses
    */
    class CC_List extends CC_Utility {

        /**
        * Recursive Method that retrieves all the Email Lists from ConstantContact.
        * @param string $path [default is empty]
        * @return array of lists
        */
        public function getLists($path = '') {
            $mailLists = array();

            if ( empty($path)) {
                $call = $this->apiPath.'/lists';
            } else {
                $call = $path;
            }

            $return = $this->doServerCall($call);
            $parsedReturn = simplexml_load_string($return);
            $call2 = '';

            foreach ($parsedReturn->link as $item) {
                $tmp = $item->Attributes();
                $nextUrl = '';
                if ((string) $tmp->rel == 'next') {
                    $nextUrl = (string) $tmp->href;
                    $arrTmp = explode($this->login, $nextUrl);
                    $nextUrl = $arrTmp[1];
                    $call2 = $this->apiPath.$nextUrl;
                    break;
                  }
            }

            foreach ($parsedReturn->entry as $item) {
                if ($this->contact_lists ){
                if (in_array((string) $item->title, $this->contact_lists)) {
                    $tmp = array();
                    $tmp['id'] = (string) $item->id;
                    $tmp['title'] = (string) $item->title;
                    $mailLists[] = $tmp;
                   }
                } else if (!in_array((string) $item->title, $this->doNotIncludeLists)) {
                    $tmp = array();
                    $tmp['id'] = (string) $item->id;
                    $tmp['title'] = (string) $item->title;
                    $mailLists[] = $tmp;
                }
            }

            if ( empty($call2)) {
                return $mailLists;
            } else {
                return array_merge($mailLists, $this->getLists($call2));
            }

        }

        /**
        * Method that retrieves  all Registered Email Addresses.
        * @param string $email_id [default is empty]
        * @return array of lists
        */
        public function getAccountLists($email_id = '') {
            $mailAccountList = array();

            if ( empty($email_id)) {
                $call = $this->apiPath.'/settings/emailaddresses';
            } else {
                $call = $this->apiPath.'/settings/emailaddresses/'.$email_id;
            }

            $return = $this->doServerCall($call);
            $parsedReturn = simplexml_load_string($return);

            foreach ($parsedReturn->entry as $item) {
                $nextStatus = $item->content->Email->Status;
                $nextEmail = (string) $item->title;
                $nextId = $item->id;
                $nextAccountList = array('Email'=>$nextEmail, 'Id'=>$nextId);
                if($nextStatus == 'Verified'){
                    $mailAccountList[] = $nextAccountList;
                }
            }
            return $mailAccountList;
        }

    }

?>
