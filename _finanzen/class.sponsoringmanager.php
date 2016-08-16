<?php
require_once("class.sponsor.php");
require_once("class.sponsorcontract.php");
require_once("class.team.php");

define("IMAGE1_BASE_AMOUNT_MIN", 0);
define("IMAGE1_BASE_AMOUNT_MAX", 2000000);
//define("IMAGE1_PER_POINT_MIN", 500);
//define("IMAGE1_PER_POINT_MAX", 1000);
define("IMAGE1_PER_POINT_MIN", 2000);
define("IMAGE1_PER_POINT_MAX", 10000);
define("IMAGE1_EXTRA_RANK1_MIN", 200000);
define("IMAGE1_EXTRA_RANK1_MAX", 1000000);
define("IMAGE1_EXTRA_RANK2_MIN", 200000);
define("IMAGE1_EXTRA_RANK2_MAX", 500000);
define("IMAGE1_EXTRA_RANK3_MIN", 100000);
define("IMAGE1_EXTRA_RANK3_MAX", 300000);
define("IMAGE1_EXTRA_AUDIENCE_MIN", 0);
define("IMAGE1_EXTRA_AUDIENCE_MAX", 1000000);
define("IMAGE1_EXTRA_CHAMPIONSHIP_MIN", 1000000);
define("IMAGE1_EXTRA_CHAMPIONSHIP_MAX", 2000000);
define("IMAGE1_EXTRA_TOP5_MIN", 1000000);
define("IMAGE1_EXTRA_TOP5_MAX", 2000000);

define("IMAGE2_BASE_AMOUNT_MIN", 1000000);
define("IMAGE2_BASE_AMOUNT_MAX", 2000000);
//define("IMAGE2_PER_POINT_MIN", 1000);
//define("IMAGE2_PER_POINT_MAX", 2000);
define("IMAGE2_PER_POINT_MIN", 2000);
define("IMAGE2_PER_POINT_MAX", 10000);
define("IMAGE2_EXTRA_RANK1_MIN", 200000);
define("IMAGE2_EXTRA_RANK1_MAX", 1000000);
define("IMAGE2_EXTRA_RANK2_MIN", 200000);
define("IMAGE2_EXTRA_RANK2_MAX", 500000);
define("IMAGE2_EXTRA_RANK3_MIN", 100000);
define("IMAGE2_EXTRA_RANK3_MAX", 300000);
define("IMAGE2_EXTRA_AUDIENCE_MIN", 0);
define("IMAGE2_EXTRA_AUDIENCE_MAX", 1000000);
define("IMAGE2_EXTRA_CHAMPIONSHIP_MIN", 1000000);
define("IMAGE2_EXTRA_CHAMPIONSHIP_MAX", 2000000);
define("IMAGE2_EXTRA_TOP5_MIN", 1000000);
define("IMAGE2_EXTRA_TOP5_MAX", 2000000);

define("IMAGE3_BASE_AMOUNT_MIN", 1500000);
define("IMAGE3_BASE_AMOUNT_MAX", 3000000);
//define("IMAGE3_PER_POINT_MIN", 2000);
//define("IMAGE3_PER_POINT_MAX", 3000);
define("IMAGE3_PER_POINT_MIN", 2000);
define("IMAGE3_PER_POINT_MAX", 5000);
define("IMAGE3_EXTRA_RANK1_MIN", 200000);
define("IMAGE3_EXTRA_RANK1_MAX", 1000000);
define("IMAGE3_EXTRA_RANK2_MIN", 200000);
define("IMAGE3_EXTRA_RANK2_MAX", 500000);
define("IMAGE3_EXTRA_RANK3_MIN", 100000);
define("IMAGE3_EXTRA_RANK3_MAX", 300000);
define("IMAGE3_EXTRA_AUDIENCE_MIN", 0);
define("IMAGE3_EXTRA_AUDIENCE_MAX", 2000000);
define("IMAGE3_EXTRA_CHAMPIONSHIP_MIN", 1000000);
define("IMAGE3_EXTRA_CHAMPIONSHIP_MAX", 3000000);
define("IMAGE3_EXTRA_TOP5_MIN", 500000);
define("IMAGE3_EXTRA_TOP5_MAX", 1500000);

define("IMAGE4_BASE_AMOUNT_MIN", 2500000);
define("IMAGE4_BASE_AMOUNT_MAX", 3500000);
//define("IMAGE4_PER_POINT_MIN", 2000);
//define("IMAGE4_PER_POINT_MAX", 3000);
define("IMAGE4_PER_POINT_MIN", 2000);
define("IMAGE4_PER_POINT_MAX", 5000);
define("IMAGE4_EXTRA_RANK1_MIN", 200000);
define("IMAGE4_EXTRA_RANK1_MAX", 1000000);
define("IMAGE4_EXTRA_RANK2_MIN", 200000);
define("IMAGE4_EXTRA_RANK2_MAX", 500000);
define("IMAGE4_EXTRA_RANK3_MIN", 100000);
define("IMAGE4_EXTRA_RANK3_MAX", 300000);
define("IMAGE4_EXTRA_AUDIENCE_MIN", 0);
define("IMAGE4_EXTRA_AUDIENCE_MAX", 1500000);
define("IMAGE4_EXTRA_CHAMPIONSHIP_MIN", 1000000);
define("IMAGE4_EXTRA_CHAMPIONSHIP_MAX", 3000000);
define("IMAGE4_EXTRA_TOP5_MIN", 0);
define("IMAGE4_EXTRA_TOP5_MAX", 1000000);

define("IMAGE5_BASE_AMOUNT_MIN", 2500000);
define("IMAGE5_BASE_AMOUNT_MAX", 4000000);
//define("IMAGE5_PER_POINT_MIN", 3000);
//define("IMAGE5_PER_POINT_MAX", 4000);
define("IMAGE5_PER_POINT_MIN", 2000);
define("IMAGE5_PER_POINT_MAX", 5000);
define("IMAGE5_EXTRA_RANK1_MIN", 200000);
define("IMAGE5_EXTRA_RANK1_MAX", 1000000);
define("IMAGE5_EXTRA_RANK2_MIN", 200000);
define("IMAGE5_EXTRA_RANK2_MAX", 500000);
define("IMAGE5_EXTRA_RANK3_MIN", 100000);
define("IMAGE5_EXTRA_RANK3_MAX", 300000);
define("IMAGE5_EXTRA_AUDIENCE_MIN", 0);
define("IMAGE5_EXTRA_AUDIENCE_MAX", 1500000);
define("IMAGE5_EXTRA_CHAMPIONSHIP_MIN", 1000000);
define("IMAGE5_EXTRA_CHAMPIONSHIP_MAX", 4000000);
define("IMAGE5_EXTRA_TOP5_MIN", 0);
define("IMAGE5_EXTRA_TOP5_MAX", 1000000);

define("BASE_AMOUNT_DELTA", 500000);
define("PER_POINT_DELTA", 1000);
define("EXTRA_RANK1_DELTA", 100000);
define("EXTRA_RANK2_DELTA", 100000);
define("EXTRA_RANK3_DELTA", 100000);
define("EXTRA_AUDIENCE_DELTA", 250000);
define("EXTRA_CHAMPIONSHIP_DELTA", 500000);
define("EXTRA_TOP5_DELTA", 250000);

/*
 * Sommer 2007
define("BASE_AMOUNT_DELTA", 100000);
define("PER_POINT_DELTA", 100);
define("EXTRA_RANK1_DELTA", 10000);
define("EXTRA_RANK2_DELTA", 10000);
define("EXTRA_RANK3_DELTA", 10000);
define("EXTRA_AUDIENCE_DELTA", 50000);
define("EXTRA_CHAMPIONSHIP_DELTA", 50000);
define("EXTRA_TOP5_DELTA", 50000);
*/


define("NEGOTIATION_MULTIPLIER", 5);

/* one day in seconds */
define("ONE_DAY", 24 * 3600);


/**
 * Manager class for the sponsors and their contracts with the teams.
 *
 * @author Enrico Hartung (enrico@iptel.org)
 * @version 1.1 [2008/01/07]
 */
class SponsoringManager {

	private $dbManager;	

	private $baseValues;

	/**
	 * Constructor
	 *
	 * Inits the local database manager.
	 *
	 * @param DbManager $dbManager reference to a database manager
	 */
	public function SponsoringManager(DbManager $dbManager) {
		$this->dbManager = $dbManager;

		/* put base values into an array of SponsorContract objects */
		/* to make the later use easier */
		/* 1. values for image = 1 */
		$this->baseValues[1]['min'] = new SponsorContract();
		$this->baseValues[1]['min']->setBaseAmount(IMAGE1_BASE_AMOUNT_MIN);
		$this->baseValues[1]['min']->setPerPoint(IMAGE1_PER_POINT_MIN);
		$this->baseValues[1]['min']->setExtraRank1(IMAGE1_EXTRA_RANK1_MIN);
		$this->baseValues[1]['min']->setExtraRank2(IMAGE1_EXTRA_RANK2_MIN);
		$this->baseValues[1]['min']->setExtraRank3(IMAGE1_EXTRA_RANK3_MIN);
		$this->baseValues[1]['min']->setExtraAudience(IMAGE1_EXTRA_AUDIENCE_MIN);
		$this->baseValues[1]['min']->setExtraChampionship(IMAGE1_EXTRA_CHAMPIONSHIP_MIN);
		$this->baseValues[1]['min']->setExtraTop5(IMAGE1_EXTRA_TOP5_MIN);
		$this->baseValues[1]['max'] = new SponsorContract();
		$this->baseValues[1]['max']->setBaseAmount(IMAGE1_BASE_AMOUNT_MAX);
		$this->baseValues[1]['max']->setPerPoint(IMAGE1_PER_POINT_MAX);
		$this->baseValues[1]['max']->setExtraRank1(IMAGE1_EXTRA_RANK1_MAX);
		$this->baseValues[1]['max']->setExtraRank2(IMAGE1_EXTRA_RANK2_MAX);
		$this->baseValues[1]['max']->setExtraRank3(IMAGE1_EXTRA_RANK3_MAX);
		$this->baseValues[1]['max']->setExtraAudience(IMAGE1_EXTRA_AUDIENCE_MAX);
		$this->baseValues[1]['max']->setExtraChampionship(IMAGE1_EXTRA_CHAMPIONSHIP_MAX);
		$this->baseValues[1]['max']->setExtraTop5(IMAGE1_EXTRA_TOP5_MAX);

		/* 2. values for image = 2 */
		$this->baseValues[2]['min'] = new SponsorContract();
		$this->baseValues[2]['min']->setBaseAmount(IMAGE2_BASE_AMOUNT_MIN);
		$this->baseValues[2]['min']->setPerPoint(IMAGE2_PER_POINT_MIN);
		$this->baseValues[2]['min']->setExtraRank1(IMAGE2_EXTRA_RANK1_MIN);
		$this->baseValues[2]['min']->setExtraRank2(IMAGE2_EXTRA_RANK2_MIN);
		$this->baseValues[2]['min']->setExtraRank3(IMAGE2_EXTRA_RANK3_MIN);
		$this->baseValues[2]['min']->setExtraAudience(IMAGE2_EXTRA_AUDIENCE_MIN);
		$this->baseValues[2]['min']->setExtraChampionship(IMAGE2_EXTRA_CHAMPIONSHIP_MIN);
		$this->baseValues[2]['min']->setExtraTop5(IMAGE2_EXTRA_TOP5_MIN);
		$this->baseValues[2]['max'] = new SponsorContract();
		$this->baseValues[2]['max']->setBaseAmount(IMAGE2_BASE_AMOUNT_MAX);
		$this->baseValues[2]['max']->setPerPoint(IMAGE2_PER_POINT_MAX);
		$this->baseValues[2]['max']->setExtraRank1(IMAGE2_EXTRA_RANK1_MAX);
		$this->baseValues[2]['max']->setExtraRank2(IMAGE2_EXTRA_RANK2_MAX);
		$this->baseValues[2]['max']->setExtraRank3(IMAGE2_EXTRA_RANK3_MAX);
		$this->baseValues[2]['max']->setExtraAudience(IMAGE2_EXTRA_AUDIENCE_MAX);
		$this->baseValues[2]['max']->setExtraChampionship(IMAGE2_EXTRA_CHAMPIONSHIP_MAX);
		$this->baseValues[2]['max']->setExtraTop5(IMAGE2_EXTRA_TOP5_MAX);

		/* 3. values for image = 3 */
		$this->baseValues[3]['min'] = new SponsorContract();
		$this->baseValues[3]['min']->setBaseAmount(IMAGE3_BASE_AMOUNT_MIN);
		$this->baseValues[3]['min']->setPerPoint(IMAGE3_PER_POINT_MIN);
		$this->baseValues[3]['min']->setExtraRank1(IMAGE3_EXTRA_RANK1_MIN);
		$this->baseValues[3]['min']->setExtraRank2(IMAGE3_EXTRA_RANK2_MIN);
		$this->baseValues[3]['min']->setExtraRank3(IMAGE3_EXTRA_RANK3_MIN);
		$this->baseValues[3]['min']->setExtraAudience(IMAGE3_EXTRA_AUDIENCE_MIN);
		$this->baseValues[3]['min']->setExtraChampionship(IMAGE3_EXTRA_CHAMPIONSHIP_MIN);
		$this->baseValues[3]['min']->setExtraTop5(IMAGE3_EXTRA_TOP5_MIN);
		$this->baseValues[3]['max'] = new SponsorContract();
		$this->baseValues[3]['max']->setBaseAmount(IMAGE3_BASE_AMOUNT_MAX);
		$this->baseValues[3]['max']->setPerPoint(IMAGE3_PER_POINT_MAX);
		$this->baseValues[3]['max']->setExtraRank1(IMAGE3_EXTRA_RANK1_MAX);
		$this->baseValues[3]['max']->setExtraRank2(IMAGE3_EXTRA_RANK2_MAX);
		$this->baseValues[3]['max']->setExtraRank3(IMAGE3_EXTRA_RANK3_MAX);
		$this->baseValues[3]['max']->setExtraAudience(IMAGE3_EXTRA_AUDIENCE_MAX);
		$this->baseValues[3]['max']->setExtraChampionship(IMAGE3_EXTRA_CHAMPIONSHIP_MAX);
		$this->baseValues[3]['max']->setExtraTop5(IMAGE3_EXTRA_TOP5_MAX);

		/* 4. values for image = 4 */
		$this->baseValues[4]['min'] = new SponsorContract();
		$this->baseValues[4]['min']->setBaseAmount(IMAGE4_BASE_AMOUNT_MIN);
		$this->baseValues[4]['min']->setPerPoint(IMAGE4_PER_POINT_MIN);
		$this->baseValues[4]['min']->setExtraRank1(IMAGE4_EXTRA_RANK1_MIN);
		$this->baseValues[4]['min']->setExtraRank2(IMAGE4_EXTRA_RANK2_MIN);
		$this->baseValues[4]['min']->setExtraRank3(IMAGE4_EXTRA_RANK3_MIN);
		$this->baseValues[4]['min']->setExtraAudience(IMAGE4_EXTRA_AUDIENCE_MIN);
		$this->baseValues[4]['min']->setExtraChampionship(IMAGE4_EXTRA_CHAMPIONSHIP_MIN);
		$this->baseValues[4]['min']->setExtraTop5(IMAGE4_EXTRA_TOP5_MIN);
		$this->baseValues[4]['max'] = new SponsorContract();
		$this->baseValues[4]['max']->setBaseAmount(IMAGE4_BASE_AMOUNT_MAX);
		$this->baseValues[4]['max']->setPerPoint(IMAGE4_PER_POINT_MAX);
		$this->baseValues[4]['max']->setExtraRank1(IMAGE4_EXTRA_RANK1_MAX);
		$this->baseValues[4]['max']->setExtraRank2(IMAGE4_EXTRA_RANK2_MAX);
		$this->baseValues[4]['max']->setExtraRank3(IMAGE4_EXTRA_RANK3_MAX);
		$this->baseValues[4]['max']->setExtraAudience(IMAGE4_EXTRA_AUDIENCE_MAX);
		$this->baseValues[4]['max']->setExtraChampionship(IMAGE4_EXTRA_CHAMPIONSHIP_MAX);
		$this->baseValues[4]['max']->setExtraTop5(IMAGE4_EXTRA_TOP5_MAX);

		/* 5. values for image = 5 */
		$this->baseValues[5]['min'] = new SponsorContract();
		$this->baseValues[5]['min']->setBaseAmount(IMAGE5_BASE_AMOUNT_MIN);
		$this->baseValues[5]['min']->setPerPoint(IMAGE5_PER_POINT_MIN);
		$this->baseValues[5]['min']->setExtraRank1(IMAGE5_EXTRA_RANK1_MIN);
		$this->baseValues[5]['min']->setExtraRank2(IMAGE5_EXTRA_RANK2_MIN);
		$this->baseValues[5]['min']->setExtraRank3(IMAGE5_EXTRA_RANK3_MIN);
		$this->baseValues[5]['min']->setExtraAudience(IMAGE5_EXTRA_AUDIENCE_MIN);
		$this->baseValues[5]['min']->setExtraChampionship(IMAGE5_EXTRA_CHAMPIONSHIP_MIN);
		$this->baseValues[5]['min']->setExtraTop5(IMAGE5_EXTRA_TOP5_MIN);
		$this->baseValues[5]['max'] = new SponsorContract();
		$this->baseValues[5]['max']->setBaseAmount(IMAGE5_BASE_AMOUNT_MAX);
		$this->baseValues[5]['max']->setPerPoint(IMAGE5_PER_POINT_MAX);
		$this->baseValues[5]['max']->setExtraRank1(IMAGE5_EXTRA_RANK1_MAX);
		$this->baseValues[5]['max']->setExtraRank2(IMAGE5_EXTRA_RANK2_MAX);
		$this->baseValues[5]['max']->setExtraRank3(IMAGE5_EXTRA_RANK3_MAX);
		$this->baseValues[5]['max']->setExtraAudience(IMAGE5_EXTRA_AUDIENCE_MAX);
		$this->baseValues[5]['max']->setExtraChampionship(IMAGE5_EXTRA_CHAMPIONSHIP_MAX);
		$this->baseValues[5]['max']->setExtraTop5(IMAGE5_EXTRA_TOP5_MAX);
	}


	/**
	 * Init the contracts of a season.
	 *
	 * Season means in this case a period of time with a start and an end date. This makes it possible to have
	 * contracts for half seasons, etc., too.
	 *
	 * @param int $start start date of season
	 * @param int $end end adte of season
	 */
	public function initSeason($start, $end, $year, $leagueID) {
		$allTeams = $this->dbManager->getAllTeams();
		$allSponsors = $this->dbManager->getAllSponsors();

		/* create sponsor contracts for all teams and sponsors if none exists */
		foreach($allTeams as $team) {
			if($team != NULL) {
				foreach($allSponsors as $sponsor) {
					if($sponsor != NULL) {
						if ($this->dbManager->getSponsorContract($sponsor, $team, $start, $end, -1,$year) == NULL) {
							$this->createSponsorContract($sponsor, $team, $start, $end, $year, $leagueID);
						}
					}
				}	
			}
		}
	}


	/**
	 * Init the contracts of a season.
	 *
	 * Season means in this case a period of time with a start and an end date. This makes it possible to have
	 * contracts for half seasons, etc., too.
	 *
	 * @param int $start start date of season
	 * @param int $end end adte of season
	 */
	public function initSeason_team($start, $end, $year, $uliID, $leagueID) {
		$team = $this->dbManager->getTeam($uliID);
		$allSponsors = $this->dbManager->getAllSponsors();
		/* create sponsor contracts for all teams and sponsors if none exists */
				foreach($allSponsors as $sponsor) {
					if($sponsor != NULL) {
						if ($this->dbManager->getSponsorContract($sponsor, $team, $start, $end, -1,$year) == NULL) {
							$this->createSponsorContract($sponsor, $team, $start, $end, $year, $leagueID);
						}
					}
		}
	}



	/**
	 * Delete the contracts of a season.
	 *
	 * Season means in this case a period of time with a start and an end date. This makes it possible to have
	 * contracts for half seasons, etc., too.
	 *
	 * @param int $start start date of season
	 * @param int $end end adte of season
	 */
	public function deleteSeason($start, $end, $year) {
		$allContracts = $this->dbManager->getSponsorContracts(NULL, NULL, $start, $end, -1, $year);
		/* create sponsor contracts for all teams and sponsors if none exists */
		// echo $year;
		foreach($allContracts as $contract) {
			if($contract != NULL) {
				$this->dbManager->deleteSponsorContract($contract);
			}
		}
	}


	/**
	 * Create a new sponsor contract.
	 *
	 * This function creates a new contract between and a team and writes it into the database. The values of a
	 * 'first offer' contract are calculated on the base of the image of the sponsor and the last year ranking of 
	 * the team. If the status is 'impossible' no values will be calculated.
	 *
	 * @param Sponsor $sponsor sponsor
	 * @param Team $team team
	 * @param int $start start date
	 * @param int $end end date
	 * @return SponsorContract a new sponsor contract
	 */
	public function createSponsorContract(Sponsor $sponsor, Team $team, $start, $end, $year, $leagueID) {
		$contract = new SponsorContract();
		$contract->setSponsor($sponsor);
		$contract->setTeam($team);

		// TODO auf League System umbauen
		$teamRanking = $this->dbManager->getTeamRanking($team->getId());	

		$contract->setStart($start);
		$contract->setEnd($end);
		$contract->setYear($year);
		

		/* Ab einem Team Ranking von 50 steigt das Angebot auf das max. 1,66fache */
		if ($teamRanking > 50)
			{$FaktorTR = ($teamRanking - 50) * 1/75 + 1;}
			else {$FaktorTR = 1;}
		
		/* special values that have to be calculated */
		if($sponsor->getImage() >= 1 && $sponsor->getImage() <= 5) {
			$minValues = $this->baseValues[$sponsor->getImage()]['min'];
			$maxValues = $this->baseValues[$sponsor->getImage()]['max'];

			$contract->setBaseAmount(rand($minValues->getBaseAmount(), $maxValues->getBaseAmount()) * $FaktorTR);
			$contract->setPerPoint(rand($minValues->getPerPoint(), $maxValues->getPerPoint()) * $FaktorTR);

			/* calculate base ranking extras (rank1 > rank2 > rank 3) */
			$extraRank3 = rand($minValues->getExtraRank3(), $maxValues->getExtraRank3());
			/* make sure that minimum for rank2 > rank3 */
			if($extraRank3 > $minValues->getExtraRank2()) {
				$extraRank2 = rand($extraRank3, $maxValues->getExtraRank2());
			} else {
				$extraRank2 = rand($minValues->getExtraRank2(), $maxValues->getExtraRank2());
			}
			/* make sure that minimum for rank1 > rank2 */
			if($extraRank2 > $minValues->getExtraRank1()) {
				$extraRank1 = rand($extraRank2, $maxValues->getExtraRank1());
			} else {
				$extraRank1 = rand($minValues->getExtraRank1(), $maxValues->getExtraRank1());
			}

			$contract->setExtraRank1($extraRank1 * $FaktorTR);
			$contract->setExtraRank2($extraRank2 * $FaktorTR);
			$contract->setExtraRank3($extraRank3 * $FaktorTR);

			$contract->setExtraAudience(rand($minValues->getExtraAudience(), $maxValues->getExtraAudience()) * $FaktorTR);
			$contract->setExtraChampionship(rand($minValues->getExtraChampionship(), $maxValues->getExtraChampionship()) * $FaktorTR);
			$contract->setExtraTop5(rand($minValues->getExtraTop5(), $maxValues->getExtraTop5()) * $FaktorTR);
				
			/* set status to 'impossible' if last years ranking is not good enough for a sponsor with this image */
			/* or else to 'first offer' */
			
			if(($sponsor->getImage() == 3 && $teamRanking < 40) || 
				($sponsor->getImage() == 4 && $teamRanking < 50) ||
				($sponsor->getImage() == 5 && $teamRanking < 65)) {
				/* set status to 'impossible' */
				$contract->setStatus(0);
			} else {
				/* set status to 'first offer' */
				$contract->setStatus(1);
			}
		}
		
		$contract->setleagueID = $leagueID;
		
		/* add new contract to database */
		$id = $this->dbManager->addSponsorContract($contract);

		/* add id to sponsor contract */
		$contract->setId($id);

		/* return the new sponsor contract */
		return $contract;
	}


	/**
	 * Negotiate a sponsor contract.
	 *
	 * @param SponsorContract $sponsorContract old sponsor contract
	 * @return SponsorContract the negotiated contract
	 * @throws Exception
	 */
	public function negotiateSponsorContract(SponsorContract $contract) {
		/* first, check status of contract */
		if($contract->getStatus() == 0) {
			throw new Exception("Impossible contracts cannot be negotiated.");
		} elseif($contract->getStatus() == 2) {
			throw new Exception("Negotiations are over, this contract is ready to sign.");
		} elseif($contract->getStatus() == 3) {
			throw new Exception("Valid contracts can no more be negotiated.");
		} elseif($contract->getStatus() == 1) {
			if($contract->getStart() > time()) {
				throw new Exception("The sponsor has not decided yet whether he will accept the current contract.");
				return;
			}
			/* get teams last year ranking */
			/** @todo get last year */
			$lastYear = intval(date('Y', $start - 365 * 24 * 3600));
			
			$ranking = $this->dbManager->getRanking($lastYear);
			/* init team rank with last rank for e.g. new teams */
			$teamRanking = count($ranking) + 1;
			foreach($ranking as $position => $rankedTeam) {
				if($rankedTeam == $contract->getTeam()->getId()) {
					$teamRanking = $position;
					break;
				}
			}
			$negContract = new SponsorContract();
			$negContract->setId($contract->getId());
			$negContract->setSponsor($contract->getSponsor());
			$negContract->setTeam($contract->getTeam());

			/* set start value to next negotiation time (in 1-4 days) */
			// $negContract->setStart(time() + rand(1,4) * ONE_DAY);
			$negContract->setStart(time() + rand(89400,259200));
// TEST
//			$negContract->setStart(time() + rand(1,2));
			

			$negContract->setEnd($contract->getEnd());
			$negContract->setStatus(1);

			/* values to be negotiated */
			$newBaseAmount = $contract->getBaseAmount() + rand(0, BASE_AMOUNT_DELTA);
			$newPerPoint = $contract->getPerPoint() + rand(0, PER_POINT_DELTA);

			/* calculate delta ranking extras (rank1 > rank2 > rank 3) */
			$newExtraRank3 = $contract->getExtraRank3() + rand(0, EXTRA_RANK3_DELTA);

			/* make sure that rank2 > rank3 */
			$diff = $newExtraRank3 - $contract->getExtraRank2();
			if($diff > 0) {
				$newExtraRank2 = $contract->getExtraRank2() + rand($diff, EXTRA_RANK2_DELTA);
			} else {
				$newExtraRank2 = $contract->getExtraRank2() + rand(0, EXTRA_RANK2_DELTA);
			}
			/* make sure that rank1 > rank2 */
			$diff = $newExtraRank2 - $contract->getExtraRank1();
			if($diff > 0) {
				$newExtraRank1 = $contract->getExtraRank1() + rand($diff, EXTRA_RANK1_DELTA);
			} else {
				$newExtraRank1 = $contract->getExtraRank1() + rand(0, EXTRA_RANK1_DELTA);
			}

			$newExtraAudience = $contract->getExtraAudience() + rand(0, EXTRA_AUDIENCE_DELTA);
			$newExtraChampionship = $contract->getExtraChampionship() + rand(0, EXTRA_CHAMPIONSHIP_DELTA);
			$newExtraTop5 = $contract->getExtraTop5() + rand(0, EXTRA_TOP5_DELTA);

			/* calculate new karma value (player wants more money) */
			$karma = $contract->getKarma();
			/* mood of the sponsor */
			$karma += rand(1, 3);

			$negContract->setKarma($karma);

			/* sponsor starts to think about a making a contract with a karma of 10 */
			if($karma >= 10) {
				/* the maximum karma is 20 */
				if($karma >= 20 || 1 == rand(1, 20 - $karma)) {
					$negContract->setStatus(2);
				}
			}

			/* set new values */
			$negContract->setBaseAmount($newBaseAmount);
			$negContract->setPerPoint($newPerPoint);
			$negContract->setExtraRank1($newExtraRank1);
			$negContract->setExtraRank2($newExtraRank2);
			$negContract->setExtraRank3($newExtraRank3);
			$negContract->setExtraAudience($newExtraAudience);
			$negContract->setExtraChampionship($newExtraChampionship);
			$negContract->setExtraTop5($newExtraTop5);
			
			/* update contract in database */
			$id = $this->dbManager->updateSponsorContract($negContract);

			/* return the negotiated sponsor contract */
			return $negContract;
		}
		return NULL;
	}


	/**
	 * Accept a sponsor contract.
	 *
	 * @param SponsorContract $sponsorContract old sponsor contract
	 * @return SponsorContract the accepted contract
	 * @throws Exception
	 */
	public function acceptSponsorContract(SponsorContract $contract) {
		/* first, check status of contract */
		if($contract->getStatus() == 0) {
			throw new Exception("Impossible contracts cannot be accepted.");
		} elseif($contract->getStatus() == 2) {
			throw new Exception("Negotiations are over, this contract is ready to sign.");
		} elseif($contract->getStatus() == 3) {
			throw new Exception("Valid contracts can no more be accepted.");
		} elseif($contract->getStatus() == 1) {
			if($contract->getStart() > time()) {
				throw new Exception("The sponsor has not decided yet whether he will accepts the current contract.");
				return;
			}
			/* get teams last year ranking */
			/** @todo get last year */
			$lastYear = intval(date('Y', $start - 365 * 24 * 3600));
			
			$ranking = $this->dbManager->getRanking($lastYear);
			/* init team rank with last rank for e.g. new teams */
			$teamRanking = count($ranking) + 1;
			foreach($ranking as $position => $rankedTeam) {
				if($rankedTeam == $contract->getTeam()->getId()) {
					$teamRanking = $position;
					break;
				}
			}
			/* set start value to next negotiation time (in 1-4 days) */
			// $contract->setStart(time() + rand(1,4) * ONE_DAY);
			$contract->setStart(time() + rand(89400,259200));
// TEST 
//			$contract->setStart(time() + rand(1,2));
			
			/* calculate new karma value (player wants no more money) */
			$karma = $contract->getKarma();
			/* mood of the sponsor */
			$karma += rand(3, 5);

			$contract->setKarma($karma);

			/* sponsor starts to think about a making a contract with a karma of 10 */
			if($karma >= 10) {
				/* the maximum karma is 20 */
				if($karma >= 20 || 1 == rand(1, 20 - $karma)) {
					$contract->setStatus(2);
				}
			}

			/* update contract in database */
			$id = $this->dbManager->updateSponsorContract($contract);
		}
		return $contract;
	}


	/**
	 * Sign a sponsor contract.
	 *
	 * @param SponsorContract $sponsorContract old sponsor contract
	 * @return SponsorContract the signed contract
	 * @throws Exception
	 */
	public function signSponsorContract(SponsorContract $contract, $year) {
		/* first, check status of contract */
		if($contract->getStatus() == 0) {
			throw new Exception("Impossible contracts cannot be signed.");
		} elseif($contract->getStatus() == 1) {
			throw new Exception("Contracts under negotiation cannot be signed.");
		} elseif($contract->getStatus() == 2) {
			if($contract->getStart() > time()) {
				throw new Exception("The sponsor has not decided yet whether he will accept the current contract.");
				return;
			}

			/** @todo make the following database operations transaction safe */

			/* get all contracts (under negotiation) of this sponsor */
			$sponsorContracts = $this->dbManager->getSponsorContracts($contract->getSponsor(), NULL, $contract->getStart(), $contract->getEnd(),-1, $year);

//print_r($sponsorContracts);

			/* check whether there is no other valid contract with sponsor */
			foreach($sponsorContracts as $sponsorContract) {
				if($sponsorContract->getStatus() == 3) {
					/* there is already another contract with this sponsor */
					/* cancel this negotiation */
					$contract->setStatus(0);
					$this->dbManager->updateSponsorContract($contract);
					return $contract;
				}
			}

			/* there is no other valid contract with this sponsor ... */

			/* update contract in database */
			$contract->setStatus(3);
			$this->dbManager->updateSponsorContract($contract);

			/* cancel all other negotiations with this sponsor */
			foreach($sponsorContracts as $sponsorContract) {
				/* cancel only other contracts not the current */
				if($sponsorContract->getId() != $contract->getId()) {
					/* cancel only contracts the are under negotiation (>0) */ 
					if($sponsorContract->getStatus() != 0) {
						$sponsorContract->setStatus(0);
						$this->dbManager->updateSponsorContract($sponsorContract);
					}
				}
			}

			/* cancel all other negotiations with this team */
			$teamContracts = $this->dbManager->getSponsorContracts(NULL, $contract->getTeam(), $contract->getStart(), $contract->getEnd(), -1, $year);
			foreach($teamContracts as $teamContract) {
				/* cancel only other contracts not the current */
				if($teamContract->getId() != $contract->getId()) {
					/* cancel only contracts the are under negotiation (>0) */ 
					if($teamContract->getStatus() != 0) {
						$teamContract->setStatus(0);
						$this->dbManager->updateSponsorContract($teamContract);
					}
				}
			}

		}
		return $contract;
	}
}
