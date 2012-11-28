<?php

include_once 'src/models/entry.php';
include_once 'src/utilities/util.php';

class Reviser {
	protected $rootEntry = null;

	function __construct($rootEntry = '') {
        $this->rootEntry = $rootEntry;
    }

	public function getRootEntry() {
		return $this->rootEntry;
	}

	/** 
	 * 	$entryId is start with 'tutorialEntry', and indics in each sub entry levels
	 * 	concatenated with '_'
	 * 	@return TutorialEntry: 	the entry that indics specified with;
	 *						 	if $entryId does not contain any index,  
	 *								null will be returned instead of rootEntry;
	 *						 	if indics do not indicate correct entry, an empty
	 *								will be created and returned.
	 */
	public function getExactEntry($entryId) {
		$indics = explode('_', $entryId);
		$count = count($indics);
		if ($count <= 1) return null;

		$entry = $this->rootEntry;
		for ($i=1; $i < $count; $i++) { 
			$index = intval($indics[$i]);
			if ($index < 0 || count($entry->subEntries)-1 < $index){
				$entry = null;
				break;
			}
			$entry = $entry->subEntries[$index];
		}

		if (!$entry) {
			Util::addError('Cannot find entry with ID: '.$entryId);
			return new TutorialEntry();
		}

		return $entry;
	}
	
	public function setText($entryId, $text) {
		$text = trim($text);
		if (empty($text)) {
			Util::addError("Text cannot be empty!");
			return;
		}
		
		$entry = $this->getExactEntry($entryId);
		$entry->text = $text;
	}

	public function setLink($entryId, $link) {
		$entry = $this->getExactEntry($entryId);
		$entry->link = $link;
	}

	public function setDescription($entryId, $link) {
		$entry = $this->getExactEntry($entryId);
		$entry->description = $link;
	}

	public function setAttibutes($entryId, $attributes) {
		$entry = $this->getExactEntry($entryId);
		$entry->attributes = $attributes;
	}

	public function addSubEntries($entryId, $subContent) {
		$subEntries = TutorialEntry::getEntriesFromContent($subContent);
		if (!$subEntries) {
			return null;
		}
		$entry = $this->getExactEntry($entryId);
		$entry->subEntries = array_merge($entry->subEntries, $subEntries);
		return $entry;
	}

	public function deleteEntry($parentId, $childIndex) {
		$entry = $this->getExactEntry($parentId);
		if ($entry == null) $entry = $this->rootEntry;

		array_splice($entry->subEntries, $childIndex, 1);
		return $entry;
	}
}
