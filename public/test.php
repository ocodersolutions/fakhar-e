<?php
phpinfo(); 
$words = '[> Hats|>hats]';
	    preg_match_all('/\[.*?\]|[^ ]+/', $words, $words);
	    array_walk_recursive($words, function(&$v){$v= trim($v,'[]');} );
	    if( isset($words[0]) ) {
    	    foreach($words[0] as $word) { 
    	        if(!is_string($word)) { 
    	            return false; 
    	        }
    	        $exitOnce = false;
    	        foreach(explode('|', $word) as $word) {
    	            if(stripos($sRawData,$word)!==false) { 
    	            	 $exitOnce = true; 
    	        	}    	        	
    	        }
    	        // not find even once
    	        if( !$exitOnce ) return false;
    	    }
	    }
	    return true;

die;

$url = 'http://www.anrdoezrs.net/click-7397357-10995993?url=http%3A%2F%2Fwww.thebay.com%2Fwebapp%2Fwcs%2Fstores%2Fservlet%2Fen%2Fthebay%2Fencounter-75g-deodorant-0043-65000316000--24';
$img = 'http://s7d9.scene7.com/is/image/TheBay/3607348544059_main?$PDPLARGE$';

echo substr($url ,strpos($url, '?url')).'_'.$img ."<br />";

echo '3_'.md5(substr($url ,strpos($url, '?url')).'_'.$img);
die;

function getSize($str)
{
		$pattern = '/((,|\s|\.|^)[0-9][0-9]((\s)?(-|x|X)(\s)?[0-9][0-9])*(,|\s|\.|$))/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			return $arr[0];
		}
		
		$pattern = '/(,|\s|\.|^)(Small|Medium|X*-?Large|[1-9]XB|X*[SML]|[1-9]?X?LT)(,|\s|\.|$)/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			return $arr[0];
		}
	
		return "opps{$str}";
}


$str = 'opps sdfsd X-Large dfsddf';
echo getSize($str);
exit(0);

echo '<pre>';
print_r($arr);
echo '</pre>';

