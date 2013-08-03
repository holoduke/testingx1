<?php 

//echo base64_decode("NjguMTgwLjE5NS4xMzg=");
//die;

class proxyList{

	public static $proxies = Array();

	public static function addProxy($ip){
		proxyList::$proxies[] = $ip;
	}

	public static function getRandomProxy(){
		$key = array_rand(proxyList::$proxies);

		echo "random proxy asked ".proxyList::$proxies[$key]."\n\n";
		
		return proxyList::$proxies[$key];
	}
	
	public static function storeToFile(){
		file_put_contents("proxylist.json",JSON_encode(proxyList::$proxies));
	}
	
	public static function restoreFromFile(){
		$content = file_get_contents("proxylist.json");
		
		if (!$content){
			return false;
		}
		proxyList::$proxies = JSON_decode($content);
		return true;
	}
	
	public static function getContext(){
		$opts = array(
				'http'=>array(
						'method'=>"GET",
						'proxy' => proxyList::getRandomProxy(),
						'header'=>"Accept-language: en\r\n" .
						"Cookie: foo=bar\r\n",
						"referer" => "https://www.google.nl/",
						'pragma' => "no-cache",
						'user_agent'=>"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36"
		
				)
		);
		
		$context = stream_context_create($opts);

		return $context;
	}
	
	public static function fetchProxies(){

		$url = "http://www.cool-proxy.net/proxies/http_proxy_list/country_code:/port:80/anonymous:/sort:score/direction:desc";
				
		echo "going to fetch proxies from ".$url."\n\n";
		
		$html = file_get_html($url,false,proxyList::getContext());
		$trs = $html->find('table tr');
		
		//print_r($html->outertext);
		$i = 0;
		foreach ($trs as $h){
		
			$i++;
		
			//if ($i < 3) continue;
			$tds = $h->find('td');
		
			//print_r($tds[0]->innertext);
			//$texts = $tds[0]->find('font');
			$parts = explode("\"",$tds[0]->innertext);
		
			if (isset($parts[3])){
				$ip = base64_decode(trim($parts[3]));
				$port = $tds[1]->innertext;
		
				if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
					proxyList::addProxy($ip.":".$port);
					echo "added ip ".$ip.":".$port." to proxy list \n";
				}
			}
		}
		
	}
}

//proxyList::fetchProxies();
//proxyList::storeToFile();
proxyList::restoreFromFile();
proxyList::getRandomProxy();
