<?php

class Parser{
	private $viewText;
	private $attributes;

	public function __construct($viewPath, $attributes = []){
		$this->viewText = file_get_contents($viewPath);
		$this->attributes = $attributes;
	}

	public function printHtml(){
		foreach($this->attributes as $key=>$val)
			$$key = $val;
		$this->parseEcho();
		$this->parseInclude();
		$this->parseFunctions();
		$fileName = '../tmp/'.rand(111,1111).'.php';
		$file = fopen($fileName, 'w');
		fwrite($file, $this->viewText);
		fclose($file);
		include $fileName;
		unlink($fileName);
	}

	private function parseEcho(){
		$this->viewText = str_replace('{{', '<?php echo ', $this->viewText);
		$this->viewText = str_replace('}}', ';?>', $this->viewText);
	}

	private function parseInclude(){
		$this->viewText = str_replace("@inc", "<?php view(", $this->viewText);
		$this->viewText = str_replace("@endinc", "); ?>", $this->viewText);
	}

	private function parseFunctions(){
		$this->viewText = str_replace("@head", "<?php head(); ?>", $this->viewText);
		$this->viewText = str_replace("@footer", "<?php footer(); ?>", $this->viewText);
	}
}

?>