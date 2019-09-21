<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\Project;
use DB;

class CostController extends Controller
{
	private function checkExistance($existanceCheckFor, $existanceCheckName, $costArray, $columnToSearch, $amount = 0) {
    	$checkExistance = array_search($existanceCheckFor, array_column($costArray, $columnToSearch));
    	if(!$checkExistance && !is_numeric($checkExistance)){
	    	$tempClient = [
	    				'id' => $existanceCheckFor,
	    				'name' => $existanceCheckName,
	    				'amount' => number_format($amount, 2),
	    				'breakdown' => []
	    			];
	    	return array('searchedAt' => $checkExistance, 'data' => $tempClient);
	    }else{
	    	return array('searchedAt' => $checkExistance, 'data' => '');
	    }
	}

	public function getAllCosts() {
		DB::enableQueryLog();

		if(isset($_GET['clients']) && count($_GET['clients'])> 0){
			$costQuery = Cost::leftJoin('projects', function($join) {
					  $join->on('projects.ID', '=', 'costs.Project_ID');
					})
					->whereIn('projects.Client_ID', $_GET['clients']);
			if(isset($_GET['projects']) && count($_GET['projects'])> 0){
				$costQuery->whereIn('projects.ID', $_GET['projects']);
			}
			$costData = $costQuery->get();
			$queries = DB::getQueryLog();
		}else{
			$costData = Cost::all();
		}

		$costArray = [];
		$i = 0;
		foreach ($costData as $key => $cost) {
	    	//Check Client Not Exists
			$checkClient = $this->checkExistance($cost->project->client->ID, $cost->project->client->Name, $costArray, 'id');

	    	if(!$checkClient['searchedAt'] && !is_numeric($checkClient['searchedAt'])){
		    	array_push($costArray, $checkClient['data']);
		    }
		    //Check Client Exists
			$checkClient = $this->checkExistance($cost->project->client->ID, $cost->project->client->Name, $costArray, 'id');

	    	//Check Project Not Exists
	    	$checkProject = $this->checkExistance($cost->project->ID, $cost->project->Title, $costArray[$checkClient['searchedAt']]['breakdown'], 'id');
	    	if(!$checkProject['searchedAt'] && !is_numeric($checkProject['searchedAt'])){
	    		array_push($costArray[$checkClient['searchedAt']]['breakdown'], $checkProject['data']);
	    	}
			//Check Project Exists
	    	$checkProject = $this->checkExistance($cost->project->ID, $cost->project->Title, $costArray[$checkClient['searchedAt']]['breakdown'], 'id');

	    	if($cost->cost_type->Parent_Cost_Type_ID == NULL){    		
				$costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['amount'] += $cost->Amount;
		    	
		    	//Check Parent Cost Type Not Exists
		    	$checkParentCostType = $this->checkExistance($cost->cost_type->ID, $cost->cost_type->Name, $costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'], 'id', $cost->Amount);
		    	if(!$checkParentCostType['searchedAt'] && !is_numeric($checkParentCostType['searchedAt'])){
		    		array_push($costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'], $checkParentCostType['data']);
		    	}
	    	}else if($cost->cost_type->cost_type->Parent_Cost_Type_ID == NULL){  	
		    	//Check Parent Cost Type
		    	$checkParentCostType = $this->checkExistance($cost->cost_type->Parent_Cost_Type_ID, $cost->cost_type->cost_type->Name, $costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'], 'id');
  	
		    	//Check child Cost Type Not Exists
				$checkChildCostType = $this->checkExistance($cost->cost_type->ID, $cost->cost_type->Name, $costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'][$checkParentCostType['searchedAt']]['breakdown'], 'id', $cost->Amount);
				
		    	if(!$checkChildCostType['searchedAt'] && !is_numeric($checkChildCostType['searchedAt'])){
					array_push($costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'][$checkParentCostType['searchedAt']]['breakdown'], $checkChildCostType['data']);
		    	}
	    	}else if($cost->cost_type->cost_type->Parent_Cost_Type_ID != NULL){
		    	//Check Parent Cost Type
		    	$checkParentCostType = $this->checkExistance($cost->cost_type->cost_type->Parent_Cost_Type_ID, $cost->cost_type->cost_type->cost_type->Name, $costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'], 'id');
  	
		    	//Check child Cost Type
				$checkChildCostType = $this->checkExistance($cost->cost_type->Parent_Cost_Type_ID, $cost->cost_type->cost_type->Name, $costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'][$checkParentCostType['searchedAt']]['breakdown'], 'id', $cost->Amount);

				//Check Sub Child Cost Type Not Exists
				$checkSubChildCostType = $this->checkExistance($cost->cost_type->ID, $cost->cost_type->Name, $costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'][$checkParentCostType['searchedAt']]['breakdown'][$checkChildCostType['searchedAt']]['breakdown'], 'id', $cost->Amount);
				
		    	if(!$checkSubChildCostType['searchedAt'] && !is_numeric($checkSubChildCostType['searchedAt'])){
					array_push($costArray[$checkClient['searchedAt']]['breakdown'][$checkProject['searchedAt']]['breakdown'][$checkParentCostType['searchedAt']]['breakdown'][$checkChildCostType['searchedAt']]['breakdown'], $checkSubChildCostType['data']);
		    	}
	    	}
		    $costArray[$checkClient['searchedAt']]['amount'] = array_sum(array_column($costArray[$checkClient['searchedAt']]['breakdown'], 'amount')); 
	    }
		return json_encode(array('status' => 200, 'message' => 'Data fetched successfully', 'data' => $costArray));
	}
}