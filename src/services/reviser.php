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

		array_shift($indics);

		$entry = TutorialEntry::findByIndexOf($this->rootEntry, $indics);

		if ($entry == null) {
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
		if ($subEntries == null) {
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

	/**
	 * Arrange an entry from old parent to new parent, both of new and old parentId are associated
	 * with **unarranged** tutorial list, oldIndex is also **unarranged**, but the newIndex would 
	 * be position after **arranged**
	 * 
	 * @param  $oldParentId: the old parent entryId
	 * @param  $oldIndex: the index in sub-entries of old parent
	 * @param  $newParentId: the new parent entryId
	 * @param  $newIndex    [description]
	 * @return array: entry with two entries, the first element is old parent entry, the second is
	 */
	public function arrangeEntry($oldParentId, $oldIndex, $newParentId, $newIndex) {
		//entry has not been arranged to different position
		if ($oldParentId == $newParentId 
			&& $oldIndex == $newIndex) 
			return array();

		$oldParent = $this->getExactEntry($oldParentId);
		if ($oldParent == null) $oldParent = $this->rootEntry;

		$newParent = $this->getExactEntry($newParentId);
		if ($newParent == null) $newParent = $this->rootEntry;

		$entry = array_splice($oldParent->subEntries, $oldIndex, 1);
		if ($entry == null) {
			Util::addError("Cannot find entry with Id: ${oldParentId}_${oldIndex}");
			return array();
		}
		$entry = array_splice($newParent->subEntries, $newIndex, 0, $entry);

		return array($oldParent, $newParent);	
	}
}
