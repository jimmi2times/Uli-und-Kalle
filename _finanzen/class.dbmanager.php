<?php
require_once("class.sponsor.php");
require_once("class.sponsorcontract.php");
require_once("class.team.php");

define("POSITIONS_TABLE", "uli_positions");
define("SPONSORING_TABLE", "uli_sponsoring");
define("SPONSORS_TABLE", "uli_sponsors");
define("TEAM_TABLE", "uli");
define("TEAM_RANKING_TABLE", "uli_team_ranking");

/**
 * Manager class for the database transactions.
 *
 * @author Enrico Hartung (enrico@iptel.org)
 * @version 1.0 [2007/06/23]
 */
class DbManager {
	
	/* mysql database host */
	private $host;

	/* mysql database user */
	private $user;

	/* password for mysql database user */
	private $password;

	/* database to use */
	private $db;

	/* prefix for mysql database tables */
	private $prefix;

	/* link to mysql database connection */
	private $dbLink;


	/**
	 * Constructor
	 *
	 * Init database parameters.
	 *
	 * @param string $host database host
	 * @param string $user database user
	 * @param string $password password for user
	 * @param string $db database
	 * @param string $prefix table prefix
	 */
	public function DbManager($host, $user, $password, $db, $prefix) {
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->db = $db;
		$this->prefix = $prefix;
	}


	/**
	 * Add a sponsor contract to the database.
	 *
	 * @param SponsorContract $contract sponsor contract
	 * @return int sponsor contract id
	 */
	public function addSponsorContract(SponsorContract $contract) {
		/* transform SponsorContract into an array */
		$data = array();
		$data['sponsor_id'] = $contract->getSponsor()->getId();
		$data['team_id'] = $contract->getTeam()->getId();
		$data['start'] = $contract->getStart();
		$data['end'] = $contract->getEnd();
		$data['base'] = $contract->getBaseAmount();
		$data['per_point'] = $contract->getPerPoint();
		$data['extra_rank1'] = $contract->getExtraRank1();
		$data['extra_rank2'] = $contract->getExtraRank2();
		$data['extra_rank3'] = $contract->getExtraRank3();
		$data['extra_audience'] = $contract->getExtraAudience();
		$data['extra_championship'] = $contract->getExtraChampionship();
		$data['extra_top5'] = $contract->getExtraTop5();
		$data['karma'] = $contract->getKarma();
		$data['status'] = $contract->getStatus();
		$data['year'] = $contract->getYear();
		$data['leagueID'] = $contract->getleagueID();
		
		/* add sponsor contract into the database */
		$id = $this->insert($this->prefix.SPONSORING_TABLE, $data);
		return $id;
	}


	/**
	 * Update a sponsor contract in database.
	 *
	 * @param SponsorContract $contract sponsor contract
	 */
	public function updateSponsorContract(SponsorContract $contract) {
		/* transform SponsorContract into an array */
		$data = array();
		$data['sponsor_id'] = $contract->getSponsor()->getId();
		$data['team_id'] = $contract->getTeam()->getId();
		$data['start'] = $contract->getStart();
		$data['end'] = $contract->getEnd();
		$data['base'] = $contract->getBaseAmount();
		$data['per_point'] = $contract->getPerPoint();
		$data['extra_rank1'] = $contract->getExtraRank1();
		$data['extra_rank2'] = $contract->getExtraRank2();
		$data['extra_rank3'] = $contract->getExtraRank3();
		$data['extra_audience'] = $contract->getExtraAudience();
		$data['extra_championship'] = $contract->getExtraChampionship();
		$data['extra_top5'] = $contract->getExtraTop5();
		$data['karma'] = $contract->getKarma();
		$data['status'] = $contract->getStatus();

		/* update sponsor contract in database */
		$this->update($this->prefix.SPONSORING_TABLE, $contract->getId(), $data);
	}


	/**
	 * Delete a sponsor contract in the database.
	 *
	 * @param SponsorContract $contract sponsor contract
	 */
	public function deleteSponsorContract(SponsorContract $contract) {
		$id = $contract->getId();

		$this->delete($this->prefix.SPONSORING_TABLE, $id);
	}


	/**
	 * Return all sponsor contracts within a given period.
	 *
	 * @param string $start start date
	 * @param string $end end date
	 * @return array list of sponsor contracts
	 */
	public function getSponsorContracts(Sponsor $sponsor = NULL, Team $team = NULL, $start = 0, $end = PHP_INT_MAX, $status = -1, $year, $leagueID = '') {
		$contracts = array();

		/* connect to mysql database */
		$this->connect();
		
		/* query mysql database to get all sponsor contracts with give start and end date */
		$contractCriteria = array();
		
		if(isset($sponsor)) {
			$contractCriteria[] = array('name'=>"sponsor_id", 'func'=>"=", 'value'=>$sponsor->getId());
		}
		if(isset($team)) {
			$contractCriteria[] = array('name'=>"team_id", 'func'=>"=", 'value'=>$team->getId());
		}
		
		/*
		if(isset($leagueID)) {
			
			$league_members = get_all_league_members($leagueID);
			if($league_members){
				$all_ulis = '(';
				foreach ($league_members as $uli){
					$all_ulis .= $uli['ID'].',';						
					}
				}		
			$contractCriteria[] = array('name'=>"team_id", 'func'=>" IS IN ", 'value'=>$league_members);
		}
		*/
		
		
		//$contractCriteria[] = array('name'=>"end", 'func'=>">=", 'value'=>$start);
		//$contractCriteria[] = array('name'=>"start", 'func'=>"<=", 'value'=>$end);

		$contractCriteria[] = array('name'=>"year", 'func'=>"=", 'value'=>$year);


		/* only return contracts with particular status (optional) */
		if($status > -1) {
			$contractCriteria[] = array('name'=>"status", 'func'=>"=", 'value'=>$status);
		}

		// print_r($contractCriteria);
		$result = $this->select($this->prefix.SPONSORING_TABLE, NULL, $contractCriteria, "karma DESC");
		// print_r($result);
		if($result != NULL) {

			/* receive data of sponsor contracts from result */
			foreach($result as $line) {
				$contract = new SponsorContract();
				$contract->setId($line['id']);
				$contract->setSponsor($this->getSponsor($line['sponsor_id']));
				$contract->setTeam($this->getTeam($line['team_id']));
				$contract->setStart($line['start']);
				$contract->setEnd($line['end']);
				$contract->setBaseAmount($line['base']);
				$contract->setPerPoint($line['per_point']);
				$contract->setExtraRank1($line['extra_rank1']);
				$contract->setExtraRank2($line['extra_rank2']);
				$contract->setExtraRank3($line['extra_rank3']);
				$contract->setExtraAudience($line['extra_audience']);
				$contract->setExtraChampionship($line['extra_championship']);
				$contract->setExtraTop5($line['extra_top5']);
				$contract->setKarma($line['karma']);
				$contract->setStatus($line['status']);
	
				/* add contract to contracts list */
				$contracts[] = $contract;
			}
		}

		/* disconnect from mysql database */
		$this->disconnect();

		/* return list of sponsor contracts */
		return $contracts;
	}


	/**
	 * Return sponsor contract of a aponsor with a team within a given period.
	 *
	 * Returns NULL if no contract with the given attributes exists and the latest if more than one exists in the
	 * given period.
	 *
	 * @param Sponsor $sponsor sponsor
	 * @param Team $team team
	 * @param string $start start date
	 * @param string $end end date
	 * @return SponsorContract sponsor contract
	 */
	public function getSponsorContract(Sponsor $sponsor, Team $team, $start, $end, $status = -1, $year) {
		$contract = NULL;
		$tmpEnd = 0;
		// $sponsor;
		// echo $year;
		$sponsorContracts = $this->getSponsorContracts($sponsor, $team, $start, $end, $status, $year);

		/* search for the most recent contract */	
		foreach($sponsorContracts as $sponsorContract) {
			/* check weather this contract is the latest */
			if($sponsorContract->getEnd() > $tmpEnd) {
				$contract = $sponsorContract;
				/* set new reference end date */
				$tmpEnd = $sponsorContract->getEnd();
			}
		}
	
		/* return sponsor contract */
		return $contract;
	}


	/**
	 * Return all sponsors.
	 *
	 * @return array list of sponsors
	 */
	public function getAllSponsors() {
		$sponsors = array();
		
		/* connect to mysql database */
		$result = $this->select($this->prefix.SPONSORS_TABLE);
		
		/* receive data of sponsors from result */
		foreach($result as $line) {
			$sponsor = new Sponsor();
			$sponsor->setId($line['ID']);
			$sponsor->setName($line['name']);
			$sponsor->setImage($line['image']);

			/* add sponsor to sponsors list */
			$sponsors[] = $sponsor;
		}
		
		/* return list of sponsors */
		return $sponsors;
	}


	/**
	 * Return sponsor with given sponsor id.
	 *
	 * @param int $sponsorId sponsor id
	 * @return Sponsor sponsor or NULL if not found
	 */
	public function getSponsor($sponsorId) {
		/* connect to mysql database */
		$sponsorCriteria = array(array('name'=>"ID", 'func'=>"=", 'value'=>$sponsorId));
		$result = $this->select($this->prefix.SPONSORS_TABLE, NULL, $sponsorCriteria);
		
		if($result) {
			/* receive data of team from result */
			$sponsor = new Sponsor();
			$sponsor->setId($result[0]['ID']);
			$sponsor->setName($result[0]['name']);
			$sponsor->setImage($result[0]['image']);
			
			/* return sponsor */
			return $sponsor;
		}

		return NULL;
	}


	/**
	 * Return all teams.
	 *
	 * @return array list of teams
	 */
	public function getAllTeams() {
		$teams = array();
		
		/* connect to mysql database */
		$result = $this->select($this->prefix.TEAM_TABLE);
		
		/* receive data of teams from result */
		foreach($result as $line) {
			$team = new Team();
			$team->setId($line['ID']);
			$team->setName($line['uliname']);

			/* add team to teams list */
			$teams[] = $team;
		}
		
		/* return list of teams */
		return $teams;
	}


	/**
	 * Return team with given team id.
	 *
	 * @param int $teamId team id
	 * @return Team team or NULL if not found
	 */
	public function getTeam($teamId) {
		/* connect to mysql database */
		$teamCriteria = array(array('name'=>"ID", 'func'=>"=", 'value'=>$teamId));
		$result = $this->select($this->prefix.TEAM_TABLE, NULL, $teamCriteria);
		
		if($result) {
			/* receive data of team from result */
			$team = new Team();
			$team->setId($result[0]['ID']);
			$team->setName($result[0]['uliname']);
			
			/* return team */
			return $team;
		}

		return NULL;
	}


	/**
	 * Get ranking.
	 *
	 * @param int $year entry id
	 * @return array ranking (int rank => int team id)
	 */
	public function getRanking($year) {
		/* init ranking array */
		$ranking = array();
		$ranking[0] = 0;

		// TODO umbauen auf halbjahres und jahresweise prüfung
		// TODO umbauen auf League System
		// im Moment per Hand 17 und 34
		$cols = array("uliID", "position");
		$criteria = array(array('name'=>"year", 'func'=>" like ", 'value'=>"%".$year), 
				array('name'=>"round", 'func'=>"=", 'value'=>"34"));

		$result = $this->select($this->prefix.POSITIONS_TABLE, $cols, $criteria);
		foreach($result as $entry) {
			$ranking[$entry['position']] = $entry['uliID'];
		}
		return $ranking;	
	}

	/**
	 * Get TeamRanking.
	 *
	 * @param int uliID entry id
	 * @return ranking (int ranking)
	 */
	public function getTeamRanking($uliID) {
		/* init ranking array */

		$cols = array("TR_gesamt");
		$criteria = array(array('name'=>"uliID", 'func'=>" = ", 'value'=>$uliID));

		$result = $this->select($this->prefix.TEAM_RANKING_TABLE, $cols, $criteria);
		foreach($result as $entry) {
			$ranking = $entry['TR_gesamt'];
		}
		return $ranking;	
	}



	/**
	 * Select entries from a mysql database table.
	 *
	 * @param string $table database table
	 * @param array $cols list of columns to be selected
	 * @param array $criteria list of criteria for selection (e.g. ('name' => "id", 'func' => ">", 'value' => 10)
	 */
	private function select($table, array $cols = NULL, array $criteria = NULL, $order = NULL) {
		/* set default values because of missing functionality in php to do this in the function */
		/* definition with multiple arguments*/
		if(!isset($cols)) {
			$cols = array();
		}
		if(!isset($criteria)) {
			$cols = array();
		}
		if(!isset($order)) {
			$order = "";
		}
		
		/* connect to mysql database */
		$this->connect();

		/* escape special chars in parameters */
		$escapedTable = mysql_real_escape_string($table);
		$escapedOrder = mysql_real_escape_string($order);

		/* mysql query to update entry */
		if(count($cols) == 0) {
			$query = "SELECT * FROM ".$escapedTable;
		} else {
			/* escape special chars */
			$escapedCols = $this->mysqlRealEscapeArray($cols);
			$query = "SELECT ".implode(", ", array_values($escapedCols))." FROM ".$escapedTable;
		}

		/* create criteria part and add it to the query */
		if(count($criteria) > 0) {
			/* escape special chars */
			$escapedCriteria = $this->mysqlRealEscapeArray($criteria);
			$criteriaArray = array();
			foreach($escapedCriteria as $criteria) {
				$criteriaArray[] = $criteria['name'] ." ".$criteria['func']." '".$criteria['value']."'";
			}
			$query .= " WHERE ".implode(" AND ", $criteriaArray);
		}		

		/* add order to the query */
		if($escapedOrder != "") {
			$query .= " ORDER BY ".$escapedOrder;
		}
		/* send mysql query to database */
		$result = mysql_query($query);
	
		/* receive returned data from result */
		$output = array();
		if($result) {
			while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$output[] = $line;
			}
		}
		/* disconnect from mysql database */
		$this->disconnect();

		/* return the selected data */
		return $output;
	}


	/**
	 * Update a mysql database table entry.
	 *
	 * @param string $table database table
	 * @param int $id entry id
	 * @param array $data entry data
	 * @throws Exception
	 */
	private function update($table, $id, array $data) {
		/* connect to mysql database */
		$this->connect();

		/* make sure that id is an integer */
		if(!settype($id, "int")) {
			throw new Exception("Entry id is no integer.");
		}

		/* escape special chars in parameters */
		$escapedTable = mysql_real_escape_string($table);
		$escapedData = $this->mysqlRealEscapeArray($data);

		/* reformat the input array */
		$tmpArray = array();
		foreach($escapedData as $col => $value) {
			$tmpArray[] = $col."=".$value;
		}
		/* mysql query to update entry */
		$query = "UPDATE ".$escapedTable." SET ".implode(", ", $tmpArray)." WHERE id = '".$id."'";

		/* send mysql query to database */
		$result = mysql_query($query);
	
		/* disconnect from mysql database */
		$this->disconnect();
	}


	/**
	 * Insert a new entry into a mysql database table.
	 *
	 * @param string $table database table
	 * @param array $data entry data
	 * @return int entry database id
	 */
	private function insert($table, array $data) {
		/* connect to mysql database */
		$this->connect();
		
		/* escape special chars in parameters */
		$escapedTable = mysql_real_escape_string($table);
		$escapedData = $this->mysqlRealEscapeArray($data);

		/* mysql entry to insert entry */
		$query = "INSERT INTO ".$escapedTable." (".implode(",", array_keys($escapedData)).") VALUES (".implode(",", array_values($escapedData)).")";
		
		/* send mysql query to database */
		$result = mysql_query($query);
	
		/* get id of inserted entry */
		$id = mysql_insert_id();

		/* disconnect from mysql database */
		$this->disconnect();

		return $id;
	}


	/**
	 * Delete a mysql database table entry.
	 *
	 * @param string $table database table
	 * @param int $id entry id
	 * @throws Exception
	 */
	private function delete($table, $id) {
		/* connect to mysql database */
		$this->connect();
		
		/* make sure that id is an integer */
		if(!settype($id, "int")) {
			throw new Exception("Entry id is no integer.");
		}

		/* escape special chars in parameters */
		$escapedTable = mysql_real_escape_string($table);

		/* mysql query to delete entry */
		$query = "DELETE FROM ".$escapedTable." WHERE id = '".$id."'";
		
		/* send mysql query to database */
		$result = mysql_query($query);
	
		/* disconnect from mysql database */
		$this->disconnect();
	}


	/**
	 * scapes special characters in a string for use in a SQL statement.
	 *
	 * @param array $input input array
	 * @return array escaped array
	 */
	private function mysqlRealEscapeArray(array $input) {
		$output = array();
		foreach($input as $name => $value) {
			if(is_array($value)) {
				$output[mysql_real_escape_string($name)] = $this->mysqlRealEscapeArray($value);
			} else {
				$output[mysql_real_escape_string($name)] = mysql_real_escape_string($value);
			}
		}
		return $output;
	}


	/**
	 * Create connection to mysql database.
	 */
	private function connect() {
		unset($this->dbLink);
		/* create connection to database host */
		// $dbLink = mysql_connect("mysql.localhost", "de88623", "proberaum1");
		// $dbLink = mysql_connect("localhost", "robert", "YMbHFY+On2");
		$dbLink = mysql_connect("localhost", "root", "");
		/* select database */
		mysql_select_db("liga_2009");
		/* set link to database as a object attribute */
		$this->dbLink = $dbLink;
	}


	/**
	 * Disconnect database connection.
	 */
	private function disconnect() {
		/* check whether connection exits */
		if(isset($this->dbLink)) {
			/* close connection */
			mysql_close($this->dbLink);
			unset($this->dbLink);
		}
	}

}