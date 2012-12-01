<?php
include_once 'src/services/connector.php';
include_once 'src/utilities/util.php';
include_once 'src/models/tutorial.php';

abstract class DisplayerBase {
	protected $layout = '';

	/**
	 * generate layout for display
	 * @return bool succeed 
	 */
	abstract public function generate();

	/**
	 * show the display layout, usually just simply echo $layout
	 */
	abstract public function show();
}

/********************* Tutorial Entries Displayer *********************/

abstract class TutorialDisplayerBase extends DisplayerBase {
    protected $tutorialName = '';
	protected $entryIdBase = 'tutorialEntry';
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
			$this->traverse($child, $this->entryIdBase.'_'.$index);
			$index++;
		}

		$footer = $this->layoutFooterForSubEntries($entry, $this->entryIdBase);
		$this->appendLayout($footer);
		return true;
	}

	protected function traverse($entry, $id) {
		$open = $this->layoutBeforeEntry($entry, $id);
		$this->appendLayout($open);

		$entryLayout = $this->layoutForEntry($entry, $id);
		$this->appendLayout($entryLayout);

		if ($entry->subEntries) {
			$header = $this->layoutHeaderForSubEntries($entry, $id);
			$this->appendLayout($header);

			$index = 0;
			foreach ($entry->subEntries as $child) {
				$this->traverse($child, $id.'_'.$index);
				$index++;
			}

			$footer = $this->layoutFooterForSubEntries($entry, $id);
			$this->appendLayout($footer);
		}

		$close = $this->layoutAfterEntry($entry, $id);
		$this->appendLayout($close);
	}

	protected function appendLayout($content) {
		$this->layout .= $content;
	}

	protected function loadEntries() {
		$connector = new FileConnector($this->tutorialName);
		return $connector->loadEntries();
	}

	abstract protected function layoutForRootEntry($entry);

	abstract protected function layoutBeforeEntry($entry, $id);
	abstract protected function layoutForEntry($entry, $id);
	abstract protected function layoutAfterEntry($entry, $id);

	abstract protected function layoutHeaderForSubEntries($entry, $id);
	abstract protected function layoutFooterForSubEntries($entry, $id);

	public function show() {
		echo $this->layout;
	}
}

class TutorialViewDisplayer extends TutorialDisplayerBase {
	public $frameName = 'toturial_frame';

	protected function layoutForRootEntry($entry) {
		return sprintf('<h1 class="tutorial_root_entry"><a href="%s" target="%s">%s</a></h1>'
			, $entry->link, $this->frameName, htmlspecialchars($entry->text));
	}
	
	protected function layoutBeforeEntry($entry, $id) {
		return sprintf('<li id="%s">', $id);
	}

	protected function layoutForEntry($entry, $id) {
		return sprintf('<a href="%s" class="%s" target="%s">%s</a>'
			, $entry->link, $entry->attributes, $this->frameName, htmlspecialchars($entry->text));
	}
	
	protected function layoutAfterEntry($entry, $id) {
		return '</li>';
	}

	protected function layoutHeaderForSubEntries($entry, $id) {
		if ($id == $this->entryIdBase) return '<div id="tutorial_entries_list" class="tutorial_entries_list"><ul>';
		return '<ul>';
	}
	
	protected function layoutFooterForSubEntries($entry, $id) {
		if ($id == $this->entryIdBase) return '</ul></div>';
		return '</ul>';
	}
}

class ReviseViewDisplayer  extends TutorialDisplayerBase {
	protected $deletable = false;

	function __construct($tutorialName = '', $deletable) { 
		parent::__construct($tutorialName);
		$this->deletable = $deletable;
	}

	protected function layoutForRootEntry($entry) {
		return sprintf('<h1 class="tutorial_root_entry"><a id="%s"  href="reviseview.php?tutorial=%s">%s</a></h1>'
			,$this->entryIdBase, $this->tutorialName, htmlspecialchars($entry->text));
	}
	protected function layoutBeforeEntry($entry, $id) {
		return '<li data-entryid="'.$id.'">';
	}

	protected function layoutForEntry($entry, $id) {
		$text = htmlspecialchars($entry->text);
		$link = htmlspecialchars($entry->link);
		$description = htmlspecialchars($entry->description);
		$relatives = json_encode($entry->relatives);
		$deleteBtn ="";
		$arrangeBtn ="";
		if($this->deletable) {
            $deleteBtn = '<input type="image" src="res/images/revise_delete.png" value="'.$id.'" name="deleteEntry"/>';
            $arrangeBtn = '<input type="image" src="res/images/revise_arrange.png" value="'.$id.'" name="arrangeEntry"/>';
    	}
		$dom = <<<EOD
<div class="tutorial_entry_container">
	<span id="$id" class="$entry->attributes" data-link="$link" data-description="$description" data-relatives="$relatives" >$text</span>
	<div class="action_buttons_container">
        <input type="image" src="res/images/revise_edit.png" value="$id" name="editEntry"/>
        <input type="image" src="res/images/revise_add.png" value="$id" name="addSubs"/>
        $deleteBtn 
        $arrangeBtn
    </div>
</div>
EOD;
		return $dom;
	}
	
	protected function layoutAfterEntry($entry, $id) {
		if (!($entry->subEntries))
			//data attributes are forced to lowercase
			return '<ul data-entryid="'.$id.'"></ul></li>';

		return '</li>';
	}
	
	protected function layoutHeaderForSubEntries($entry, $id) {
		return '<ul data-entryid="'.$id.'">';
	}
	
	protected function layoutFooterForSubEntries($entry, $id) {
		return '</ul>';
	}

	public function setDeletable($bool) {
		$this->deletable = $bool;
	}
}

class ReviseSubViewDisplayer extends ReviseViewDisplayer {
	private $subRootEntry = null;

	public function __construct($entry, $id) {
		$this->subRootEntry = $entry;
		$this->entryIdBase = $id;
	}

	public function loadEntries() {
		return $this->subRootEntry;
	}

	public function layoutForRootEntry($entry) {
		return $this->layoutForEntry($entry, $this->entryIdBase);
	}
}

/********************* Option List for Synchronize *********************/

abstract class SyncOptionsDispayer extends DisplayerBase {

	public function generate() {
		$this->layout = '';
		$tutorials = $this->loadTutorials();
		if (empty($tutorials)) return false;
		$prefix = $this->getPrefix();
		foreach ($tutorials as $tutorial) {
			$id = $prefix.$tutorial->name;
        	$this->layout .= sprintf('<input type="checkbox" value="%s" title="Updated: %s" id="%s"/>'
        							, $tutorial->name
        							, $tutorial->updated_time
        							, $id);
    		$this->layout .= sprintf('<label for="%s" title="Updated: %s">%s</label><br />'
    								, $id
    								, $tutorial->updated_time
    								, Util::decodeFileName($tutorial->name));
		}

		return true;
	}

	public function show() {
		if (empty($this->layout)) $this->generate();
		echo $this->layout;
	}

	public function getLayout() {
		return $this->layout;
	}

	abstract protected function loadTutorials();
	abstract protected function getPrefix();
}

class DatabaseOptionssDisplayer extends SyncOptionsDispayer {
	protected function loadTutorials() {
        return DatabaseConnector::getTutorialsFromDatabase();   
	}

	protected function getPrefix() {
		return 'db_';
	}
}

class FileOptionssDisplayer extends SyncOptionsDispayer {
	protected function loadTutorials() {
		return FileConnector::getTutorialsFromFile();
	}

	protected function getPrefix() {
		return 'file_';
	}
}


/********************* Tutorials List *********************/

/**
 * Tutorial List Displayer
 */
class TutorialListDisplayer extends DisplayerBase{
	protected $deletable = false;
	function __construct($deletable = false) {
		$this->deletable = $deletable;
	}

	public function generate() {
		$this->layout = '';
		$tutorials = FileConnector::getTutorialsFromFile();
		$deleteBtn = '';
		foreach ($tutorials as $tutorial) {
			$parameter = '?tutorial='. $tutorial->name;
			if ($this->deletable) 
				$deleteBtn = '<input type="image" alt="Remove Tutorial" name="removeTutorial" src="res/images/remove.png" value="'.$tutorial->name.'">';
			$entryLayout = <<<EOD
<tr>
	<td>
		<a href="acquireview.php$parameter" title="Acquire Tutorial"><img alt="Read" src="res/images/acquire.png"></a>
		<a href="reviseview.php$parameter" title="Revise Tutorial"><img alt="Revise" src="res/images/revise.png"></a>
		<a href="download.php$parameter" title="Download Tutorial"><img alt="Download" src="res/images/download.png"></a>
		$deleteBtn
	</td>
	<td><a href="acquireview.php$parameter">$tutorial->name</a></td>
</tr>
EOD;
			$this->layout .= $entryLayout;
		}
		if ($this->layout == '') {
			Util::addError('No tutorial in progress.');
			return false;
		}

		$this->layout = '<table><thead><tr><th width="170px">Action Options</th><th>Tutorial Name</th></tr></thead><tbody>'
				.$this->layout.'</tbody></table>';
		return true;
	}

	public function show() {
		echo $this->layout;
	}
}

/********************* Error Displayer *********************/

class ErrorDisplayer extends DisplayerBase{
	protected $errors;
	public function __construct($errors = array()) {
		if (!is_array($errors) || empty($errors)) 
			$errors = Util::getErrors();

		$this->errors = $errors;
	}
	public function __get($name) {
		switch ($name) {
			case 'layout':
				if (empty($this->layout))$this->generate();
				return $this->layout;
				break;
			
			default:
				break;
		}
	}

	public function generate() {
		$this->layout = '';
		foreach ($this->errors as $error) {
			$errorMessage = sprintf('<div class="error_message">%s</div>', htmlspecialchars($error));
			$this->layout .= $errorMessage;
		}

		return true;
	}

	public function show() {
		if (empty($this->layout))$this->generate();
		echo $this->layout;
	}
}


