<?php
/**
 * Storage class for a contract of sponsor with a team.
 *
 * @author Enrico Hartung (enrico@iptel.org)
 * @version 1.0 [2007/06/23]
 */
class SponsorContract {
	
	/* contract id (int) */
	private $id;
	
	/* sponsor (Sponsor) */
	private $sponsor;

	/* team (Team) */
	private $team;

	/* start date of contarct (int) */
	private $start;

	/* end date of contract (int) */
	private $end;

	/* base amount of contract (int) */
	private $baseAmount;

	/* amount per point (int) */
	private $perPoint;

	/* extra per 1st rank (int) */
	private $extraRank1;

	/* extra per 2nd rank (int) */
	private $extraRank2;

	/* extra per 3rd rank (int) */
	private $extraRank3;

	/* extra for audience over 35,000 (int) */
	private $extraAudience;

	/* extra for win of championship (int) */
	private $extraChampionship;

	/* extra for a rank under the top 5 (int) */
	private $extraTop5;

	/* karma of sponsor in negotiations for this contract */
	private $karma;

	/* status of contract */
	/* 0 = impossible / negotiation failed */
	/* 1 = first offer / under negotiation */
	/* 2 = negotiated and ready to sign */
	/* 3 = valid contract (int) */
	private $status;


	/**
	 * Constructor
	 *
	 * Init non-object attributes of this sponsor contract.
	 */
	public function SponsorContract() {
		$this->setId(-1);
		$this->setStart(0);
		$this->setEnd(0);
		$this->setBaseAmount(0);
		$this->setPerPoint(0);
		$this->setExtraRank1(0);
		$this->setExtraRank2(0);
		$this->setExtraRank3(0);
		$this->setExtraAudience(0);
		$this->setExtraChampionship(0);
		$this->setExtraTop5(0);
		$this->setKarma(0);
		$this->setStatus(0);
	}


	/**
	 * Set id for this contract.
	 *
	 * @param int $id contract id
	 */
	public function setId($id) {
		if (settype($id, "integer")) {
			$this->id = $id;
		} else {
			throw new Exception("Value for contract id is not an integer.");
		}
	}


	/**
	 * Get id of this contract.
	 *
	 * @return int contract id
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Set sponsor for this contract.
	 *
	 * @param Sponsor $sponsor sponsor
	 */
	public function setSponsor(Sponsor $sponsor) {
		$this->sponsor = $sponsor;
	}


	/**
	 * Get sponsor of this contract.
	 *
	 * @return Sponsor sponsor
	 */
	public function getSponsor() {
		return $this->sponsor;
	}


	/**
	 * Set team for this contract.
	 *
	 * @param Team $team team
	 */
	public function setTeam($team) {
		$this->team = $team;
	}


	/**
	 * Get team of this contract.
	 *
	 * @return Team team
	 */
	public function getTeam() {
		return $this->team;
	}


	/**
	 * Set start date for this contract.
	 *
	 * @param string $start start date
	 */
	public function setStart($start) {
		if (settype($start, "string")) {
			$this->start = $start;
		} else {
			throw new Exception("Value for start date is not a string.");
		}
	}


	/**
	 * Get start date of this contract.
	 *
	 * @return string start date
	 */
	public function getStart() {
		return $this->start;
	}


	/**
	 * Set end date for this contract.
	 *
	 * @param string $end end date
	 */
	public function setEnd($end) {
		if (settype($end, "string")) {
			$this->end = $end;
		} else {
			throw new Exception("Value for end date is not a string.");
		}
	}


	/**
	 * Get end date of this contract.
	 *
	 * @return string end date
	 */
	public function getEnd() {
		return $this->end;
	}


	/**
	 * Set year for this contract.
	 *
	 * @param string $year 
	 */
	public function setYear($year) {
		if (settype($year, "string")) {
			$this->year = $year;
		} else {
			throw new Exception("Value for year date is not a string.");
		}
	}


	/**
	 * Get year of this contract.
	 *
	 * @return string year 
	 */
	public function getYear() {
		return $this->year;
	}
	

	/**
	 * Set base amount for this contract.
	 *
	 * @param int $baseAmount base amount
	 */
	public function setBaseAmount($baseAmount) {
		if (settype($baseAmount, "integer")) {
			$this->baseAmount = $baseAmount;
		} else {
			throw new Exception("Value for the base amount is not an integer.");
		}
	}


	/**
	 * Get base amount of this contract.
	 *
	 * @return int base amount
	 */
	public function getBaseAmount() {
		return $this->baseAmount;
	}


	/**
	 * Set per point amount for this contract.
	 *
	 * @param int $perPoint per point amount
	 */
	public function setPerPoint($perPoint) {
		if (settype($perPoint, "integer")) {
			$this->perPoint = $perPoint;
		} else {
			throw new Exception("Value for amount per point is not an integer.");
		}
	}


	/**
	 * Get per point amount of this contract.
	 *
	 * @return int per point amount
	 */
	public function getPerPoint() {
		return $this->perPoint;
	}


	/**
	 * Set extra amount per 1st rank (per round) for this contract.
	 *
	 * @param int $extraRank1 extra per 1st rank
	 */
	public function setExtraRank1($extraRank1) {
		if (settype($extraRank1, "integer")) {
			$this->extraRank1 = $extraRank1;
		} else {
			throw new Exception("Value for extra amount for 1st rank is not an integer.");
		}
	}


	/**
	 * Get extra amount per 1st rank (per round) of this contract.
	 *
	 * @return int extra per 1st rank
	 */
	public function getExtraRank1() {
		return $this->extraRank1;
	}


	/**
	 * Set extra amount per 2ndt rank (per round) for this contract.
	 *
	 * @param int $extraRank2 extra per 2nd rank
	 */
	public function setExtraRank2($extraRank2) {
		if (settype($extraRank2, "integer")) {
			$this->extraRank2 = $extraRank2;
		} else {
			throw new Exception("Value for extra amount for 2nd rank is not an integer.");
		}
	}


	/**
	 * Get extra amount per 2nd rank (per round) of this contract.
	 *
	 * @return int extra per 2nd rank
	 */
	public function getExtraRank2() {
		return $this->extraRank2;
	}


	/**
	 * Set extra amount per 3rd rank (per round) for this contract.
	 *
	 * @param int $extraRank3 extra per 3rd rank
	 */
	public function setExtraRank3($extraRank3) {
		if (settype($extraRank3, "integer")) {
			$this->extraRank3 = $extraRank3;
		} else {
			throw new Exception("Value for extra amount for 3rd rank is not an integer.");
		}
	}


	/**
	 * Get extra amount per 3rd rank (per round) of this contract.
	 *
	 * @return int extra per 3rd rank
	 */
	public function getExtraRank3() {
		return $this->extraRank3;
	}


	/**
	 * Set extra amount for an audience over 35,0000 for this contract.
	 *
	 * @param int $extraAudience extra for audience
	 */
	public function setExtraAudience($extraAudience) {
		if (settype($extraAudience, "integer")) {
			$this->extraAudience = $extraAudience;
		} else {
			throw new Exception("Value for extra amount for audience over 35,000 is not an integer.");
		}
	}


	/**
	 * Get extra amount for an audience over 35,000 of this contract.
	 *
	 * @return int extra for audience
	 */
	public function getExtraAudience() {
		return $this->extraAudience;
	}


	/**
	 * Set extra amount for championship for this contract.
	 *
	 * @param int $extraChampionship extra championship
	 */
	public function setExtraChampionship($extraChampionship) {
		if (settype($extraChampionship, "integer")) {
			$this->extraChampionship = $extraChampionship;
		} else {
			throw new Exception("Value for extra amount for championship is not an integer.");
		}
	}


	/**
	 * Get extra amount for championship of this contract.
	 *
	 * @return int extra championship
	 */
	public function getExtraChampionship() {
		return $this->extraChampionship;
	}


	/**
	 * Set extra amount for rank under top 5 for this contract.
	 *
	 * @param int $extraTop5 extra for top 5
	 */
	public function setExtraTop5($extraTop5) {
		if (settype($extraTop5, "integer")) {
			$this->extraTop5 = $extraTop5;
		} else {
			throw new Exception("Value for extra amount for reaching top 5 is not an integer.");
		}
	}


	/**
	 * Get extra amount for rank under top 5 of this contract.
	 *
	 * @return int extra for top 5
	 */
	public function getExtraTop5() {
		return $this->extraTop5;
	}


	/**
	 * Set karma of sponsor for the negotiations of this contract.
	 *
	 * @param int $karma karma
	 */
	public function setKarma($karma) {
		if (settype($karma, "integer")) {
			$this->karma = $karma;
		} else {
			throw new Exception("Value for karma is not an integer.");
		}
	}


	/**
	 * Get karma of sponsor for the negotiations of this contract.
	 *
	 * @return int karma
	 */
	public function getKarma() {
		return $this->karma;
	}


	/**
	 * Set status of this contract.
	 *
	 * @param int $status status
	 */
	public function setStatus($status) {
		if (settype($status, "integer")) {
			$this->status = $status;
		} else {
			throw new Exception("Value for status is not an integer.");
		}
	}


	/**
	 * Get leagueID of this contract.
	 *
	 * @return int leagueID
	 */
	public function getleagueID() {
		return $this->status;
	}

	/**
	 * Set leagueID of this contract.
	 *
	 * @param int $leagueID leagueID
	 */
	public function setleagueID($leagueID) {
		if (settype($leagueID, "integer")) {
			$this->status = $leagueID;
		} else {
			throw new Exception("Value for status is not an integer.");
		}
	}


	/**
	 * Get status of this contract.
	 *
	 * @return int status
	 */
	public function getStatus() {
		return $this->status;
	}

}