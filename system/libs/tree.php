<?php
include_once('system/libs/json.php');
include_once('system/libs/treebranch.php');

class TafelTree {

	protected $id;
	protected $width;
	protected $height;
	protected $pathImgs;
	protected $options;

	public function __construct($id, $options = array()){
		$this->id = $id;
		$this->pathImgs = '/templates/admin/images/tree/';
		$this->width = '100%';
		$this->height = 'auto';
		$this->options = array();
		foreach ($options as $property => $value) {
			$this->options[$property] = $value;
		}
	}

	public function loadJSON ($json, $id, $imgs = 'imgs/', $width = '100%', $height = 'auto', $options = array()) {
		$tree = new TafelTree($id, $imgs, $width, $height, $options);
		$service = new Services_JSON();
		$tree->items = TafelTree::loadServiceJSON($service->decode($json));
		return $tree;
	}

	public function loadServiceJSON ($service) {
		$branches = array();
		foreach ($service as $branch) {
			$branches[] = TafelTreeBranch::loadServiceJSON($branch);
		}
		return $branches;
	}

	public function getId() {
		return $this->id;
	}
	public function getPathImgs() {
		return $this->pathImgs;
	}
	public function getWidth() {
		return $this->width;
	}
	public function getHeight() {
		return $this->height;
	}

	public function add (TafelTreeBranch $branch) {
		if (!isset($this->items)) {
			$this->items = array();
		}
		$this->items[] = $branch;
	}

	public function addBranch ($id, $txt, $options = array()) {
		$branch = new TafelTreeBranch ();
		$branch->setId($id);
		$branch->setText($txt);
		foreach ($options as $property => $value) {
			if ($property != 'items') {
				$branch->setParam($property, $value);
			}
		}
		if (isset($options['items'])) {
			foreach ($options['items'] as $opt) {
				$branch->addBranch(null, null, $opt);
			}
		}
		if (!isset($this->items)) {
			$this->items = array();
		}
		$this->items[] = $branch;
		return $branch;
	}

	public function display ($debug = 0) {
		if ($debug == 1) {
			$d = '<br />';
		} elseif ($debug == 2) {
			$d = "\n";
		} else {
			$d = '';
		}
		if (count($this->options) > 0) {
			$s = new Services_JSON();
			$options = ','.$s->encode($this->options);
		}
		$str = "var tree_".$this->id." = null;".$d;
		$str .= "function TafelTreeInit() {".$d;
		$str .= "tree_".$this->id." = new TafelTree (".$d;
		$str .= "'".$this->getId()."', ".$d;
		$str .= $this->getJSON().", ".$d;
		$str .= "'".$this->getPathImgs()."', ".$d;
		$str .= "'".$this->getWidth()."', ".$d;
		$str .= "'".$this->getHeight()."'".$options;
		$str .= ");".$d;
		$str .= "};".$d;
		return $str;
	}

	public function getJSON () {
		$service = new Services_JSON();
		return $service->encode($this->items);
	}
}