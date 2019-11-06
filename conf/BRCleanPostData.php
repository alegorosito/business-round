<?php 

	/**
	 * 
	 */
	class BRCleanPostData
	{
		
		public static function cleanPost($data = null) 
		{
			$data = trim($data);
			$data = stripslashes($data);
			//$data = htmlspecialchars($data);
			
			return $data; 
		}
		
	}

 ?>