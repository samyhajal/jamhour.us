<?php
/**
 * emailer class
 */
class emailer
{
	protected $type;
	protected $xtemp;
	protected $attachments = array();
	protected $mime_boundary;
	public $smtp_server;
	public $smpt_user;
	public $smpt_pass;

	/**
	 * Construct
	 * @access public
	 *
	 * @param string	$type	The type of email, either text or html
	 * @param obj		$xtemp	XTemplate object
	 */
	public function __construct( $type="text", &$xtemp=NULL )
	{
		$this->type = $type;
		if ( isset($xtemp) )
		{
			$this->xtemp = $xtemp;
		}
	}

	/**
	 * Send the email
	 *
	 * @param string	$to				To field
	 * @param string	$from			From field
	 * @param string	$subject		Subject line
	 * @param string	$message		Email message
	 * @param string	$add_headers	Additional headers
	 *
	 * @access public
	 */
	public function send( $to, $from, $subject, $message=NULL, $add_headers=NULL )
	{
		if ( isset($this->xtemp) )
		{
			$this->xtemp->parse('MAIN');
			$message = $this->xtemp->text('MAIN');
			error_log($message);
		}
		$headers	= "From:". $from ."\r\n";
		$headers	.= "Reply-To:". $from ."\r\n";
		if ( $this->type == "html" )
		{
			$this->mime_boundary	= "{==Multipart_Boundary_x{". md5(time()) ."}x}";
			$headers 		.= "MIME-Version: 1.0\r\n" .
							"Content-Type: multipart/mixed; boundary=\"". $this->mime_boundary ."\"";

			$email_msg		= $message ."\r\n".
							"--". $this->mime_boundary ."\r\n";

			$email_msg		.= "Content-Type:text/html; charset=\"iso-8859-1\"\r\n".
							"Content-Transfer-Encoding: 7bit\r\n".
			$message ."\r\n".
							"--". $this->mime_boundary ."\r\n";
			foreach ( $this->attachments as $attachment )
			{
				$email_msg	.= $this->proccess_attachments($attachment[0],$attachment[1]);
			}
			$email_msg	.= "--";
		}
		else
		{
			$email_msg	= $message;
		}
		if ( isset($add_headers ) )
		{
			$headers .= $add_headers;
		}
		if( ! isset($this->smtp_server) )
		{
			$this->send_posix($to, $subject, $email_msg, $headers);
		}
		else
		{
			$this->send_smtp($to, $subject, $email_msg, $headers);
		}
	}

	/**
	 * Sent email via posix
	 * @access public
	 *
	 * @param string	$to				To field
	 * @param string	$subject		Subject line
	 * @param string	$message		Email message
	 * @param string	$headers		Headers
	 */
	public function send_posix( $to, $subject, $message, $headers )
	{
		mail ( $to, $subject, $message, $headers );
	}

	/**
	 * Sent email via SMTP
	 * @access public
	 *
	 * @param string	$to				To field
	 * @param string	$subject		Subject line
	 * @param string	$message		Email message
	 * @param string	$headers		Headers
	 */
	public function send_smtp( $to, $subject, $message, $headers )
	{
		
	}

	/**
	 * Add an attachment to the email
	 * @access public
	 *
	 * @param string	$fileatt_name	The name of the file
	 * @param string	$fileatt		The file directory
	 */
	public function add_attachment($fileatt_name, $fileatt = __PATH_ROOT__)
	{
		$this->attachments[count($this->attachments)]	= array($fileatt_name,$fileatt);
	}

	/**
	 * Adds the attachment code to email message
	 * @access protected
	 *
	 * @param string	$fileatt_name	The name of the file
	 * @param string	$fileatt		The file directory
	 *
	 * @return string    Returns the file attachment code
	 */
	protected function proccess_attachments($fileatt_name, $fileatt)
	{
		$fileatt_type	= "application/octet-stream";

		$file		= file_get_contents($fileatt.$fileatt_name);
		$data 		= chunk_split(base64_encode($file));
		$message	=	"Content-Type: ". $fileatt_type ."; name=\"". $fileatt_name ."\"\r\n".
					"Content-Transfer-Encoding: base64\r\n".
					"Content-disposition: attachment; file=\"". $fileatt_name ."\"\r\n".
					"\r\n".
		$data.
					"--".$this->mime_boundary;

		return $message;
	}
}
?>
