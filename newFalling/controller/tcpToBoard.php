<?php 

 	public function socketClient($string){
		$confing = array(
			'persistent' => false,
			// 'host' => $GLOBALS['ip'],
			'host' => '192.168.1.102',
			'protocol' => 'tcp',
			'port' => 8080,
			'timeout' => 18000
		);
		$Socket = new \Think\Socket($confing);
		$Socket->connect();
		$data = $string;
		$Socket->write($data);
		$dataReturn=array();
		$buf=$Socket->read(1024);
		$GLOBALS['buf'][0]=$buf;
		$i=1;
		while($buf!="#") {
			$buf=$Socket->read(1024);
			if($buf!="#"){
				$GLOBALS['buf'][$i]=$buf;
				$i++;
			}
		}
		// $test=M('test');
		// $time=date("Y-m-d H:i:s",time());
		// $test->add($time);

			$Socket->disconnect();
			dataToBase();
 	}
}
?>