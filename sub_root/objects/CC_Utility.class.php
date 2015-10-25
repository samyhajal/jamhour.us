<?php
 /**
    * Class that is using REST to communicate with ConstantContact server
 	* This class currently supports actions performed using the contacts, lists, and campaigns APIs
    * @author ConstantContact Dev Team
    * @version 2.0.0
    * @since 30.03.2010
    */
    class CC_Utility {
        // FROM HERE YOU MAY MODIFY YOUR CREDENTIALS
        var $login = 'JamhourUS'; //Username for your account
        var $password = 'educationisjob1'; //Password for your account
        var $apikey = '4f408cda-ca48-4b40-b1ce-038bb77b1fce'; // API Key for your account.

        // CONTACT LIST OPTIONS
        var $contact_lists = array(); // Define which lists will be available for sign-up.
        var $force_lists = false; // Set this to true to take away the ability for users to select and de-select lists
        var $show_contact_lists = true; // Set this to false to hide the list name(s) on the sign-up form.
        // NOTE - Contact Lists will only be hidden if force_lists is set to true. This is to prevent available checkboxes form being hidden.

        // FORM OPT IN SOURCE - (Who is performing these actions?)
        var $actionBy = 'ACTION_BY_CUSTOMER'; // Values: ACTION_BY_CUSTOMER or ACTION_BY_CONTACT
        // ACTION_BY_CUSTOMER - Constant Contact Account holder. Used in internal applications.
        // ACTION_BY_CONTACT - Action by Site visitor. Used in web site sign-up forms.

        // DEBUGGING
        var $curl_debug = true; // Set this to true to see the response code returned by cURL

        // YOUR BASIC CHANGES SHOULD END HERE
        var $requestLogin; //this contains full authentication string.
        var $lastError = ''; // this variable will contain last error message (if any)
        var $apiPath = 'https://api.constantcontact.com/ws/customers/'; //is used for server calls.
        var $doNotIncludeLists = array('Removed', 'Do Not Mail', 'Active'); //define which lists shouldn't be returned.


        public function __construct() {
            //when the object is getting initialized, the login string must be created as API_KEY%LOGIN:PASSWORD
            $this->requestLogin = $this->apikey."%".$this->login.":".$this->password;
            $this->apiPath = $this->apiPath.$this->login;
        }

         /**
         * Method that returns a list with all states found in states.txt file
         * @return array with state codes and state names
         */
         public function getStates() {
            $returnArr = array();
            $lines = file("files/states.txt");
            foreach ($lines as $line) {
                $tmp = explode(" - ", $line);
                if (sizeof($tmp) == 2) {
                    $returnArr[trim($tmp[1])] = trim($tmp[0]);
                }
            }
            return $returnArr;
         }

        /**
        * Returns a list with all countries found in countries.txt file
        * @return array with country codes and country names
        */
        public function getCountries() {
            $returnArr = array();
            $lines = file("files/countries.txt");
            foreach ($lines as $line) {
                $tmp = explode(" - ", $line);
                if (sizeof($tmp) == 2) {
					if ( strlen(trim($tmp[0])) <= 24 )
					{
						$returnArr[trim($tmp[1])] = trim($tmp[0]);
					}
                }
            }
            return $returnArr;
        }

        /**
        * Validate an email address
        * @return  TRUE if address is valid and FALSE if not.
        */
        public function isValidEmail($email){
			$match	= ( preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email) == true ) ? 1 : 0;
             echo $match;
        }

        /**
        * Private function used to send requests to ConstantContact server
        * @param string $request - is the URL where the request will be made
        * @param string $parameter - if it is not empty then this parameter will be sent using POST method
        * @param string $type - GET/POST/PUT/DELETE
        * @return a string containing server output/response
        */
        protected function doServerCall($request, $parameter = '', $type = "GET", $convert = true) {
            $ch = curl_init();
			if ( $convert == true ) {
				$request = str_replace('http://', 'https://', $request);
			}
            
			//echo $request.'<br />';
			//echo $type.'<br />';
            // Convert id URI to BASIC compliant
            curl_setopt($ch, CURLOPT_URL, $request);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->requestLogin);
            # curl_setopt ($ch, CURLOPT_FOLLOWLOCATION  ,1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/atom+xml", 'Content-Length: ' . strlen($parameter)));
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            switch ($type) {
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
                    break;
                case 'PUT':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
                    break;
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    break;
                default:
                    curl_setopt($ch, CURLOPT_HTTPGET, 1);
                    break;
            }

           $emessage = curl_exec($ch);
           if ($this->curl_debug) {   echo $error = curl_error($ch);   }
           curl_close($ch);

           return $emessage;
        }

    }

?>
