<?php
/**
 * Storage class for a sponsor.
 *
 * @author Enrico Hartung (enrico@iptel.org)
 * @version 1.0 [2007/06/23]
 */
class Sponsor {

	/* database id of sponsor */
	private $id;
	
	/* name of sponsor */
	private $name;
	
	/* image of sponsor */ 
	private $image;


	/**
	 * Constructor
	 * 
	 * Init objects attributes.
	 */
	public function Sponsor() {
		$this->id = -1;
		$this->name = "";
		$this->image = -1;
	}
	

	/**
	 * Set database id of this sponsor.
	 *
	 * @param int $id database id
	 */
	public function setId($id) {
		if (settype($id, "integer")) {
			$this->id = $id;
		} else {
			throw new Exception("Value for sponsor id is not an integer.");
		}
	}	


	/**
	 * Get database id of this sponsor.
	 *
	 * @return int database id
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Set name of this sponsor.
	 *
	 * @param string $id name
	 */
	public function setName($name) {
		if (settype($name, "string")) {
			$this->name = $name;
		} else {
			throw new Exception("Value for sponsor name is not a string.");
		}
	}


	/**
	 * Get name of this sponsor.
	 *
	 * @return string name
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * Set image of this sponsor.
	 *
	 * @param int $image image
	 */
	public function setImage($image) {
		if (settype($image, "integer")) {
			$this->image = $image;
		} else {
			throw new Exception("Value for sponsor image is not an integer.");
		}
	}


	/**
	 * Get image of this sponsor.
	 *
	 * @return int image
	 */
	public function getImage() {
		return $this->image;
	}


	/**
	 * Get a string version of this sponsor.
	 *
	 * This method is for debugging purposes.
	 *
	 * @return string string version of object
	 */
	public function toString() {
		$string = "Sponsor: (int) id=".$this->getId().", (string) name='".$this->getName()."', (int) image=".$this->getImage()." ";
		
		return $string;
	}
}
