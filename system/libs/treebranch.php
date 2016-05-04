<?php
class TafelTreeBranch
{
	public $id;
	public $txt;

	public function __construct(){
	}

	public function loadJSON ($json) {
		$service = new Services_JSON();
		$obj = $service->decode($json);
		$branches = array();
		foreach ($obj as $s) {
			$branches[] = TafelTreeBranch::loadServiceJSON($s);
		}
		return $branches;
	}

	public function loadServiceJSON ($service) {
		$branch = new TafelTreeBranch();

		foreach ($service as $property => $value) {
			if ($property != 'items') {
				$branch->setParam($property, $value);
			}
		}

		if (isset($service) && isset($service->items)) {
			$branch->items = array();
			foreach ($service->items as $b) {
				$branch->items[] = TafelTreeBranch::loadServiceJSON($b);
			}
		}
		return $branch;
	}

	public function getId () {return $this->id;}
	public function setId ($id) {$this->id = $id;}

	public function getText () {return $this->txt;}
	public function setText ($txt) {$this->txt = $txt;}

	public function getParam ($param) {
		if (isset($this->$param)) {
			return $this->$param;
		}
	}

	public function setParam ($param, $value) {
		if ($param == 'id') {
			$this->setId($value);
		} elseif ($param == 'txt') {
			$this->setText($value);
		} else {
			$this->$param = $value;
		}
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

	public function getJSON () {
		$service = new Services_JSON();
		return $service->encode($this);
	}
}