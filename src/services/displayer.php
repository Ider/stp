<?php
include_once 'src/services/connector.php';
include_once 'src/utilities/util.php';

abstract class DisplayerBase {
	protected $layout = '';

	//generate layout for display, return bool value to indicate if succeed 
	abstract public function generate();

	//show the display layout, usually just simply echo $layout
	abstract public function show();
}

abstract class TutorialDisplayerBase extends DisplayerBase {
    protected $tutorialName = '';
	protected $entryIdBase = 'tutorialEntry_';
	protected $rootEntry = null;

	function __construct($tutorialName = '') {
        $this->tutorialName = $tutorialName;
    }

	public function __get($name) {
		switch ($name) {
		 	case 'layout':
		 		return $this->layout;
		 		break;
		 	case 'tutorialName':
		 		return $this->tutorialName;
		 		break;
		 	case 'rootEntry':
		 		return $this->rootEntry;
		 		break;
		 	default:
		 		null;
		 		break;
		 }
	}

	public function __set($name, $value) {
		switch ($name) {
			case 'tutorialName':
				$this->tutorialName = $value;
				break;
			default:
				break;
		}
	}

	public function generate() {		
		$entry = $this->loadEntries();
		if (!$entry) return false;
		
		$this->rootEntry = $entry;

		$this->layout = '';

		$rootEntryLayout = $this->layoutForRootEntry($entry);
		$this->appendLayout($rootEntryLayout);

		if (!$entry->subEntries) return true;

		$header = $this->layoutHeaderForSubEntries($entry, $this->entryIdBase);
		$this->appendLayout($header);

		$index = 0;
		foreach ($entry->subEntries as $child) {
			$this->analyze($child, $this->entryIdBase . $index);
			$index++;
		}

		$footer = $this->layoutFooterForSubEntries($entry, $this->entryIdBase);
		$this->appendLayout($footer);
		return true;
	}

	protected function analyze($entry, $id) {
		$entryLayout = $this->layoutForEntry($entry, $id);
		$this->appendLayout($entryLayout);

		if (!$entry->subEntries) return;

		$header = $this->layoutHeaderForSubEntries($entry, $id);
		$this->appendLayout($header);

		$index = 0;
		foreach ($entry->subEntries as $child) {
			$this->analyze($child, $id . '_' . $index);
			$index++;
		}

		$footer = $this->layoutFooterForSubEntries($entry, $id);
		$this->appendLayout($footer);
	}

	protected function appendLayout($content) {
		$this->layout .= $content;
	}

	protected function loadEntries() {
		$connector = new FileConnector($this->tutorialName);
		return $connector->loadEntries();
	}

	abstract protected function layoutForRootEntry($entry);
	abstract protected function layoutForEntry($entry, $id);
	abstract protected function layoutHeaderForSubEntries($entry, $id);
	abstract protected function layoutFooterForSubEntries($entry, $id);

	public function show() {
		echo $this->layout;
	}
}

class TutorialViewDisplayer extends TutorialDisplayerBase {
	public $frameName = 'toturial_rame';

	protected function layoutForRootEntry($entry) {
		return sprintf('<h1><a href="%s" target="%s">%s</a></h1>'.PHP_EOL
			, $entry->link, $this->frameName, htmlspecialchars($entry->text));
	}

	protected function layoutForEntry($entry, $id) {
		return sprintf('<li id="%s" class="%s"><a href="%s" target="%s">%s</a></li>'.PHP_EOL
			, $id, $entry->attributes, $entry->link, $this->frameName, htmlspecialchars($entry->text));
	}
	
	protected function layoutHeaderForSubEntries($entry, $id) {
		if ($id == $this->entryIdBase) return '<ul id="tutorial_list">';
		return '<ul>';
	}
	
	protected function layoutFooterForSubEntries($entry, $id) {
		return '</ul>';
	}
}

class RiviseViewDisplay  extends TutorialDisplayerBase {

	protected function layoutForRootEntry($entry) {
		return sprintf('<h1><a href="reviseview.php?tutorial=%s">%s</a></h1>'.PHP_EOL
			, $this->tutorialName, htmlspecialchars($entry->text));
	}

	protected function layoutForEntry($entry, $id) {
		return sprintf('<li id="%s" class="%s">%s</li>'.PHP_EOL
			, $id, $entry->attributes, htmlspecialchars($entry->text));
	}
	
	protected function layoutHeaderForSubEntries($entry, $id) {
		return '<ul>';
	}
	
	protected function layoutFooterForSubEntries($entry, $id) {
		return '</ul>';
	}
}

/*
** Tutorial List Displayer
*/
class TutorialListDisplayer extends DisplayerBase{
	protected $viewURL = '';

	function __construct($url) {
		$this->viewURL = $url;
	}

	public function generate() {
		$this->layout = '';
		$files = scandir(CONTENT_DIR);

		foreach ($files as $file) {
			if ($file[0] == '.') continue;
			$name = str_replace('_', ' ', $file);
			$entryLayout = sprintf('<li><a href="%s?tutorial=%s">%s</a></li>', $this->viewURL, $file, $name);
			$this->layout .= $entryLayout;
		}
		if ($this->layout == '') {
			Util::addError('No tutorial in progress.');
			return false;
		}

		$this->layout = '<ul>'.$this->layout.'</ul>';
		return true;
	}

	public function show() {
		echo '<h1>Tutorial List</h1>';
		echo $this->layout;
	}
}

/*
** Error Displayer
*/
class ErrorDisplayer extends DisplayerBase{
	public function generate() {
		return true;
	}

	public function show() {
		$errors = Util::getErrors();
		foreach ($errors as $error) {
			$errorMessage = sprintf('<div class="error_message">%s</div>', $error);
			echo $errorMessage;
		}
	}
}


