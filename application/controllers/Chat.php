<?

/**
 * 
 */
class Chat extends CI_Controller
{
	
	function __construct()
	{
			parent::__construct();
	}
	public function index(){
		$this->load->view("master");
		$this->load->view("chat");
	}
}