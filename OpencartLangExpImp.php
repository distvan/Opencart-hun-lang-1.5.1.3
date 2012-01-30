<?php 

class OpencartLangExpImp
{
	private $_fromLanguage;
	private $_pathFrom;
	private $_importPath;
	private $_csvFilePath;
	private $_temp;

	public function __construct()
	{
		$this->_fromLanguage = "hungarian";
    	$this->_pathFrom = "/home/distvan/www/opencart_demo/admin/language/" . $this->_fromLanguage;	
		$this->_importPath = "/home/distvan/Dokumentumok/temp/translate/languageproba/";
		$this->_csvFilePath = "/home/distvan/GIT/Opencart-hun-lang-1.5.1.3/csv/catalog_hungarian_1513.csv";
	}	
	
	
	/* Kiexportálja a megadott nyelvű változókat tab szeparáltan
	 * */
	public function export()
	{
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($this->_pathFrom)), 
                                         			RecursiveIteratorIterator::SELF_FIRST
        );
          foreach($objects as $name => $object)
		  {
		  	if(!is_dir($name))
			{	
				$info = pathinfo($name);
				$dir = dirname($name);
				$dirName = basename($dir);
				if ($dirName == $this->_fromLanguage)
				{
					$dirName = " ";
				}
				$fileName = $info['basename'];
				$_ = array();
				require ($name);
				foreach($_ as $key => $value)
				{
					$row = $this->_fromLanguage . "\t" . $dirName . "\t" . $fileName . "\t" . $key . "\t" . str_replace("\n", "", $value);
					echo $row . "\n";
				}	
			}
		  }
	}
	
	/* A meglévő lefordított csv fájlból nyelvi fájlokat generál
	 * */
	public function import()
	{
		$handle = null;
		$lines = file($this->_csvFilePath);

		foreach($lines as $line)
		{
    		$pieces = explode("\t", $line);
    		$dirPath = $this->_importPath.str_replace(" ","",trim($pieces[0], "\"")) . "/" . trim($pieces[1],"\"");
    		echo "dir:".$dirPath."\n";
    		if(trim($pieces[1],"\"") != " ")
    		{
    			if(!is_dir($dirPath))
    			{
    				mkdir($dirPath, 0777, true);	
    			}
    		}
    		if(trim($pieces[1],"\"") == " ")
    		{
    			$file = trim($pieces[0], "\"") . "/" . trim($pieces[2],"\"");
    		}else{
    			$file = trim($pieces[0], "\"") . "/" . trim($pieces[1],"\"") . "/" . trim($pieces[2],"\"");
    		}
    		$fullPath = $this->_importPath . $file;
			try{
    			if(!$handle = fopen($fullPath, 'a'))
				{
					echo 'Cannot open the file:' . $fullPath;
				}
			}catch(Exception $e)
			{
				
			}
			$value = str_replace("\"\"", "\"", addcslashes(trim(trim(trim($pieces[4], "\""),"\n"),"\""), "'"));
			$content = "\$_['" . trim($pieces[3],"\"") . "'] = '" . $value . "';\n";
			$this->writeFile($handle, $content, $fullPath);
			fclose($handle);
		}
	}
	
	private function writeFile($handle, $content, $filename)
	{
		try
		{
			if($filename !== $this->_temp)
			{
				if($this->_temp !== null)
				{
					$h = fopen($this->_temp, 'a');
					fwrite($h, "?>");
				}
				fwrite($handle, "<?\n");
				$this->_temp = $filename;
			}
	     	fwrite($handle, $content);
		}
		catch(Exception $e)
		{
			echo "Cannot write to file:" . $filename;
		}
    }
}
?>
