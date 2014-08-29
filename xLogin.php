<?php
/**
* @author heylisten@xtiv.net
* @name Login
* @desc Handles the logic of authentication to the website
* @version v2(1.6)
* @icon key.png
* @mini key
* @link login/keys
* @see domain
* @release delta
* @delta true
* @todo 
**/
class xLogin extends Xengine {
		function __construct($c){
			parent::__construct($c); // IMPORTANT!!!
			
			$this->set('SUPER_ADMIN',false);
			$q = $this->q();

			$SUPER_ADMIN = $q->Select('id','Users',array(
				'power_lvl' 	=> 9
			));



			// If there fails to be a super-admin - we need to make one!
			if( empty($SUPER_ADMIN) ){
				$this->set('login_title','Add a Admin Account');
				$this->set('login_message','Enter a Username/Password Combo to Continue');

				if (isset($_SERVER['HTTP_COOKIE'])) {
				    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
				    foreach($cookies as $cookie) {
				        $parts = explode('=', $cookie);
				        $name = trim($parts[0]);
				        setcookie($name, '', time()-1000);
				        setcookie($name, '', time()-1000, '/');
				    }
				}
			}else{
				$this->_SET['SUPER_ADMIN'] = $SUPER_ADMIN[0]['id'];
			}
		}



		// The Site allows login anywhere, as so long as autorun is running...
		function autoRun($X){
			// Whenever we find the POST Login Set, we run the Login Procedure. 
			if(isset($_POST['login']) && is_array($_POST['login'])){
				switch ($_POST['login']['action']) {
					case 'register':
						return $this->register($_POST['login']);
					break;
					
					default:
						return $this->login($_POST['login']['username'],$_POST['login']['password']);
					break;
				}
			} else if($this->Key['is']['user']){
				return array(
					'user' => $_SESSION['user']
				);
			} 
		}

		private function getUserByName($user,$cols = 'id'){
			$u = $this->Q->Select($cols,'Users',array(
				'username' 	=> $user
			));
			return $u[0];	
		}

		private function getUserByEmail($email,$cols = 'id'){
			$u = $this->Q->Select($cols,'Users',array(
				'email' 	=> $email
			));
			return $u[0];	
		}

		/**
		 * @remotable
		 */
		private function login($username,$password){	
			
			if($this->is_email($username)){
				$u = $this->getUserByEmail($username,'id,username,email,password,hash,power_lvl');				
			}else{
				$u = $this->getUserByName($username,'id,username,email,password,hash,power_lvl');
			}

			
			// Username Not Found
			if(empty($u)){
				// This Might be the first user ever.

				$super = $this->Q->Select('id','Users', array(
					'power_lvl' => 9
				));

				$super = ( empty($super) ) ? false : true ;

				if( empty($super) ){
					$_POST['login']['power_lvl'] = 9;
					$this->insertUser($_POST['login']);
					return array('success'=>true);
				}else{
					return array(
						'success' => FALSE,
						'error'   => eval('return "'.$this->_LANG['LOGIN']['ERROR']['USERNAME'].'";')
					);	
				}
			}else{
				$hash = sha1( md5( base64_encode($u['email'].$password) ) );
				$pass = md5( sha1( $password ) );

				// FAILED
				if($hash !== $u['hash'] && $pass !== $u['password']){
					if($u['hash'] == '' && $pass === $u['password']){
						$this->Q->Update('Users',array(
							'hash' => $hash
						),array(
							'id' => $u['id']
						));
						return $this->login($username,$password);
					}

					return array(
						'success' => FALSE,
						'error'   => $this->_LANG['LOGIN']['ERROR']['PASSWORD']
					);
				}else{
					$this->setUser($u);

					$this->Q->Update('Users',array(
						'last_login' => time()
					),array(
						'id' => $u['id']
					));

					if($u['power_lvl'] === 9)
						$this->syncDbTables();

					return array(
						'success' => TRUE,
						'data' 	  => $_SESSION['user']
					);
				}
			}
			return false;
		}

		function logout(){
			unset($_SESSION['user']);
			$this->set('login_title','You Have Been Successfully Logged Out');
			$this->set('login_message','Goodbye');
		}

		function setEmail($time){
			if($time == $_SESSION['setEmail']['time']){
				$this->set('setEmail',true);
				$this->set('username',$_SESSION['setEmail']['username']);

				if($_POST['email']){
					$this->q()->Update('Users',array(
						'email' => $_POST['email']
					),array(
						'id'	=>	$_SESSION['setEmail']['user_id']
					));
					header("Location: /");
				}
			}else{
				echo 'Not Allowed.';
				exit;
			}
		}

		function index(){
			
		}

		function keys(){

		}

		/**
			@name fireKey
			@blox fireKey
			@desc Manage Inventory
			@backdoor true 
			@icon book
		**/
		function masterKeys(){
			$this->masterKey();
		}

		function masterKey(){ 
			$user = $_SESSION['user'];
			if(isset($_POST['key']) && $user['power_lvl'] == 9){
				
				$p = $_POST['key'];
				$l = $this->_LANG['LOGIN'];
				$q = $this->q();
				$u = $this->getUserByEmail($p['email']);

				if( !empty($u) ){
					$m = $q->Update('Users', array('power_lvl' => 9),$u);
				}else{
					$q->Insert('Users',array(
						'email'     => $p['email'],
						'power_lvl' => 9
					)); 
				} 

				$headers = "From: ". $_SESSION['user']['username'] 
					. "(".$_SESSION['user']['email'].")" . "\r\n" .
			    'Reply-To: '.$_SESSION['user']['email']  . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();

				$m = mail(
					$p['email'],
					$_SESSION['user']['username'] . $l['emails']['key']['sub'] . $_SERVER['HTTP_HOST'], 
					$l['emails']['key']['msg'],
					$headers
				);

				$return = array(
					'success' => $m,
					'data'	=> $_POST,
					'error'   => $q->error
				);

			}else{
				$return = array(
					'success' => false,
					'data'	  => $_POST,
					'error'   => 'Permission Denied'
				);
			}
			return $return;
		}

		/**
			@name fireKey
			@blox fireKey
			@desc Manage Inventory
			@backdoor true
			@filter keys
			@icon book
		**/
		function fireKey(){

		}
		function fireKeys(){

		}
		/**
			@name waterKey
			@blox waterKey
			@desc Manage Inventory
			@backdoor true
			@filter keys
			@icon book
		**/
		function waterKey(){
			
		}
		function waterKeys(){

		}
		/**
			@name earthKey
			@blox earthKey
			@desc Manage Inventory
			@backdoor true
			@filter keys
			@icon book
		**/
		function earthKey(){
			
		}
		function earthKeys(){
			
		}
		/**
			@name windKey
			@blox Wind Key
			@desc Ability to landmark and create urls
			@backdoor true
			@filter keys
			@icon book
		**/
		function windKey(){
			
		}
		function windKeys(){
			
		}


		function profile($user_id='')
		{
			# code...
			if(isset($_POST['user']) && $this->Key['is']['user']){
				// exit;
				$q = $this->q();

				$q->Update('Users',$_POST['user'],array(
					'id' => $_SESSION['user']['id']
				));

				$user = $q->Select('*','Users',array(
					'id' => $_SESSION['user']['id']
				));
				unset($_SESSION['user']);

				$this->setUser($user[0]);

				$this->set('user',$_SESSION['user']);	

			}
		}

		private function insertUser($user)
		{
			$hash = sha1( md5( base64_encode($user['email'].$user['password']) ) );
			$pass = md5(sha1($user['password']));
			return $this->Q->Insert('Users',array(
				'email'     => $user['email'],
				'username'  => $user['username'],
				'password'  => $pass,
				'hash'      => $hash,
				'power_lvl' => $user['power_lvl']
			));
		}
		/**
		 * @remotable
		 * @formHandler
		 * @remoteName
		 */
		function newAdmin($form=false,$error=false){
			$success = false;
			$L       = $this->_LANG['LOGIN'];

			if(isset($form['username'])){
				// Form has been posted. lets check values...
				// Server Side Form Checking... 
				$_SESSION['newAdminForm'] = $form;
				$error = $this->validateNewUserForm($form);

				if(!$error){

					if(!$this->insertUser($form)){
						$error = $this->Q->error;
					}else{


						$success = true;
						$to = $form['email'];
						foreach($form as $k => $v){
							$form['msg'] = str_replace("$".$k,$v,$form['msg']);
						}

						$message = wordwrap($form['msg'],70);
						$headers = 'From: noreply@'.str_replace('www.', '', $_SERVER['HTTP_HOST']). "\r\n" .'X-Mailer: PHP/' . phpversion();

						$form['mail'] = $message;	
						if( !mail($to, $subject, $message, $headers) ){
							$error = $this->lang($this->_LANG['LOGIN']['ERROR']['SENDMAIL'],$form);
						}
					}
				}
			}else{
				$form = (!isset($_SESSION['newAdminForm'])) ? array(
					'username' => $L['USERNAME'], 
					'password' => '', 
					'email'    => $L['EMAIL'] ,
					'subject'  => $this->lang($L['NEW_ADMIN_EMAIL_SUBJECT'],$_SERVER)
				) : $_SESSION['newAdminForm'];
				unset($_SESSION['newAdminForm']);
			}

			return array(
				'success'  => $success,
				'error'    => urldecode($error),
				'username' => $form['username'],
				'password' => $form['password'], 
				'confirm'  => $form['confirm'],
				'email'    => $form['email'],
				'subject'  => $form['subject']
			);
		}

		function usernameIsUnique($username)
		{
			return (0 === count($this->q()->Select(
				'*','Users',array(
					'username' => $username
				))
			));
		}

		private function validateNewUserForm($form)
		{
			// These all the required fields.
			foreach (['username','password','confirm','email'] as $key => $value) {
				if($value == 'username'){
					if(!$this->usernameIsUnique($form['username'])){
						return $this->lang($this->_LANG['LOGIN']['ERROR']['UNIQUE'],$form);
					}		
				}
				if ($form[$value] === ''){
					return ucfirst($value) . " Required";
				}
			}

			if(!$this->is_email($form['email'],true))
				return $this->lang($this->_LANG['LOGIN']['ERROR']['EMAIL'],$form);
	
			// Requists passed.
			// Check to make sure pass and confirm are the same.
			if($form['password'] !== $form['confirm'])
				return $this->_LANG['LOGIN']['ERROR']['CONFIRM'];
		}

		function setUser($user){
			$unset = ['password','hash'];
			
			$_SESSION['user'] = $user;
			$_SESSION['user']['secret'] = md5($user['username'].$user['password']);

			foreach ($unset as $k => $v) 
				if( isset($_SESSION['user'][$v]) )
					unset($_SESSION['user'][$v]);

			foreach($_SESSION['user'] as $k => $v){
				setcookie("user[$k]",$v);
			}

			return $_SESSION['user'];
		}

		function checkUserX($user){
			$x = $this->getXTends();
			foreach($x as $k => $v){
				$Link = str_replace('.php','',$k);
				$Link = strtolower(substr($Link,1));
				if($Link == strtolower($user)){
					return false;
				}
			}
			return true;
		}

		function register($form=null){
			$form = ($form) ? $form : $_POST['form']; 
			$q = $this->q();

			$error = $this->validateNewUserForm($form);

			$this->set('error',$error);

			if(!$error){ 
				$exist = $q->Select('*','Users',array(
					'username'	=> $form['username'],
					'email'		=> $form['email']
				),'','=','OR');

				# Create new user 
				if(empty($exist) && $this->checkUserX($form['username']) ){
					# Check user name against xtensions
					$form['hash'] 		= sha1(md5(base64_encode($form['email'].$form['password'])));
					$form['password'] 	= md5(sha1($form['password']));
					$form['power_lvl'] 	= 1;
					unset($form['confirm']);
					unset($form['action']);

					$q->Insert('Users',$form);
					$this->setUser($form);

					//header('Location: /'.$form['username']);
				}else{
					$exist = $exist[0];
					$error = "Invalid Username - Please choose another";

					if($form['email'] == $exist['email'] ){
						$error = "A user with this email already exists!";
					}

					if($form['username'] == $exist['username'] ){
						$error = "The Username, $form[username], Is Not Available.";
					}

					$this->set('error',$error);
				}

			}

			//


			$this->set('WWW_PAGE','Create Your Free Account Now');
			$this->set('PAGE_TITLE','Create Your Free Account Now');
			return array(
				'success' => (empty($error)),
				'error'   => $error,
				'form'    => $form
			);
		}

	}
?>
