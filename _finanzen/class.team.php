<?php
/**
 * Storage class for a team.
 *
 * @author Enrico Hartung (enrico@iptel.org)
 * @version 1.0 [2007/06/23]
 */
class Team {

	/* database id of team (int) */
	private $id;
	
	/* name of team (string) */
	private $name;
	
	/**
	 * Constructor
	 * 
	 * Init objects attributes.
	 */
	public function Team() {
		$this->id = -1;
		$this->name = "";
	}
	

	/**
	 * Set database id of this team.
	 *
	 * @param int $id database id
	 */
	public function setId($id) {
		if (settype($id, "integer")) {
			$this->id = $id;
		} else {
			throw new Exception("Value for team id is not an integer.");
		}
	}	


	/**
	 * Get database id of this team.
	 *
	 * @return int database id
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Set name of this team.
	 *
	 * @param string $id name
	 */
	public function setName($name) {
		if (settype($name, "string")) {
			$this->name = $name;
		} else {
			throw new Exception("Value for team name is not a string.");
		}
	}


	/**
	 * Get name of this team.
	 *
	 * @return string name
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * Get a string version of this team.
	 *
	 * This method is for debugging purposes.
	 *
	 * @return string string version of team object
	 */
	public function toString() {
		$string = "Team: (int) id=".$this->getId().", (string) name='".$this->getName()."' ";
		
		return $string;
	}
}
