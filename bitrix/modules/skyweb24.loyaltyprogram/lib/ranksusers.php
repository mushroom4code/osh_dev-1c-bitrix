<?
namespace Skyweb24\Loyaltyprogram;
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__DIR__ .'/lang.php');
Loc::loadMessages(__FILE__);
/**
*    profiles list
*/
class RanksUsers{

	function __construct (){
		$this->settings=Settings::getInstance();
		$this->tableRanksInfo=[];
		$tableRanks=Entity\RankUsersTable::getMap();

		foreach($tableRanks as $nextcolumn){
			if($nextcolumn->getName() != 'ranks'){
				$this->tableRanksInfo[$nextcolumn->getName()]=$nextcolumn->getTitle();
			}

		}
	}
	
	//get
	public function getRankSettings(){
		return $this->rankSettings;
	}
	
	public function getProfiles(){
	    if(empty($this->Profiles)){
            $profiles=new Profiles;
            $this->Profiles=$profiles->getListProfiles();
            unset($this->Profiles['separator']);
        }
		return $this->Profiles;
	}
	
	public function getData($id){
		if($id=='new'){
			$data=[
				'sort'=>100,
				'active'=>'Y',
				'coeff'=>1,
				'name'=>'NEW RANK',
				'value'=>100,
				'settings'=>[
					'period' =>[
						'type'=>'month',
						'size'=>1
					],
					'rewriteRank'=>$this->rankSettings['rewriteRank']
				],
				'profiles'=>[]
			];
		}else{
			$data=Entity\RankUsersTable::getById($id)->fetch();
		}
		return $data;
	}

    /**
     * @return array
     */
    public function getRanks()
    {
        if(empty($this->rankList)) {
            $this->rankList=[];
            $data = Entity\RankUsersTable::getList(['filter' => ['active' => 'Y']]);
            while ($arData = $data->fetch()) {
                $this->rankList[]=$arData;
            }
        }
        return $this->rankList;
    }

    /**
     * @param int $userId
     * @return int id ranks or 0 if not found
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getRankUser($userId=0){
        $rank=0;
        if($userId>0) {
            $data = Entity\RankUsersTable::getList(['filter' => ['user_id' => $userId]]);
            if ($arData = $data->fetch()) {
                $rank = $arData['rank_id'];
            }
        }
        return $rank;
    }

    public function getRankUsers(array $filter=[]){
        $ranks=[];
        if(count($filter)>0){
            $data = Entity\RankUsersTable::getList(['filter' => $filter]);
        }else{
            $data = Entity\RankUsersTable::getList();
        }
        while($arData = $data->fetch()) {
            $ranks[$arData['user_id']] = $arData['rank_id'];
        }
        return $ranks;
    }
	
	//edit ranks

    /**
     * @param $id
     * @param $direction
     */
    private function dataActivate($id, $direction){
		Entity\RankUsersTable::update($id, [
			'active'=>$direction,
			'date_setting'=>new \Bitrix\Main\Type\DateTime
		]);
	}
	
	private function dataDelete($id){
		Entity\RankUsersTable::delete($id);
	}
	
	private function dataEdit($id, $data){
		$oldData=Entity\RankUsersTable::getById($id)->fetch();
		$newData=[];
		$newData['name']=$data['name'];
		$newData['coeff']=$data['coeff'];
		$newData['sort']=$data['sort'];
		$newData['active']=$data['active'];
		$newData['value']=$data['value'];
		$newData['value']=empty($newData['value'])?0:$newData['value'];
		$newData['date_setting']=new \Bitrix\Main\Type\DateTime;
		$tmpSettings=unserialize($oldData['settings']);
		$tmpSettings['period']['type']=$data['settings'];
		$newData['settings']=serialize($tmpSettings);
		Entity\RankUsersTable::update($id, $newData);
	}
	
	public function saveSetting($data){
		$newData=[
			'id'=>$data['id'],
			'user_id'=>$data['user_id'],
			'rank_id'=>$data['rank_id'],
			'active'=>empty($data['active'])?'N':'Y',
			'date_setting'=>new \Bitrix\Main\Type\DateTime,
		];
		$this->checkAgent();
		if($data['id']!='new'){
			$upd=Entity\RankUsersTable::update($data['id'], $newData);
		}else{
			$upd=Entity\RankUsersTable::add($newData);
		}
		return $upd;
	}
	
	public function show(){
		$sTableID = 'sw24_rank_users';
		//$oSort = new \CAdminUiSorting($sTableID, "sort", "asc");
		//tmp fix
		if(class_exists('\CAdminUiSorting')){
			$oSort = new \CAdminUiSorting($sTableID, "sort", "asc");
		}else{
			$oSort = new \CAdminSorting($sTableID, "sort", "asc");
		}
		//e.o.tmp fix
		
		global $by, $order, $APPLICATION, $FIELDS;
		\Bitrix\Main\Diag\Debug::dumpToFile($FIELDS, 'fields_ranks_users', 'dlog.txt');
		if (!isset($by)){$by = 'id';}
		if (!isset($order)){$order = 'asc';}
		$by=$by=='sort'?'id':$by;
		$lAdmin = new \CAdminUiList($sTableID, $oSort);
		
		$rights=$APPLICATION->GetGroupRight($this->settings->getModuleId());
		/// ????
		if($rights>"E"){
			$ids=[];
			$action='';
			if($lAdmin->GroupAction()){
				if(is_array($_REQUEST['ID'])){
					$ids=$_REQUEST['ID'];
				}elseif(!empty($_REQUEST['ID'])){
					$ids=[$_REQUEST['ID']];
				}
				$action=$_REQUEST['action'];
			}
			
			if($lAdmin->EditAction()) {
				$action='edit';
				$ids=array_keys($FIELDS);
			}

			if(count($ids)>0){
				foreach($ids as $nextId){
					switch($action){
						case "deactivate":
							$this->dataActivate($nextId, 'N');
							break;
						case "activate":
							$this->dataActivate($nextId, 'Y');
							break;
						case "delete":
							$this->dataDelete($nextId);
							break;
						case "edit":
							$this->dataEdit($nextId, $FIELDS[$nextId]);
							break;
					}
				}
			}	
		}

		$ranksData = new \Skyweb24\Loyaltyprogram\Ranks;
		$ranksName = $ranksData->getRanks(false);
		$filterRanks = [];
		$selectRanks = [];
		foreach($ranksName as $rank){
			$filterRanks[$rank['id']] = $rank['name'];			
		}
		
		$filterFields = [
			[
				"id" => "USER_ID",
				"name" => Loc::getMessage("skyweb24.loyaltyprogram_FILTER_USER_ID_TITLE"),
				"type" => "custom_entity",
				"selector" => array("type" => "user"),
				"filterable" => "",
				"default" => true
			],
			[
				"id" => "RANK_ID",
				"name" => Loc::getMessage("skyweb24.loyaltyprogram_FILTER_USER_ID_TITLE"),
				"type" => "list",
				"items" => $filterRanks,
				"filterable" => "",
				"default" => true
			]
		];
		
		$arFilter = array();
		
		$lAdmin->AddFilter($filterFields, $arFilter);

		$headers=[];
		foreach($this->tableRanksInfo as $keyColumn=>$nameInfo){
			if($keyColumn=='type'){
				continue;
			}
			$headers[]=['id' => $keyColumn, 'content' => $nameInfo, 'sort' => $keyColumn, 'default' => true];
		}
		$lAdmin->AddHeaders($headers);
		$result=Entity\RankUsersTable::GetList(['filter' => $arFilter, 'order'=>[$by=>$order]]);
		$rsData = new \CAdminUiResult($result, $sTableID);
		$rsData->NavStart();
		$lAdmin->SetNavigationParams($rsData, array());
		
		while($arRes = $rsData->Fetch()){
			if(empty($arRes['settings'])){
				$arRes['settings']='month';
			}else{
				$tmpSettings=unserialize($arRes['settings']);
				$arRes['settings']=$tmpSettings['period']['type'];
			}
			$tmpProf=[];
			if(!empty($arRes['profiles'])){
				$tmpProf=unserialize($arRes['profiles']);
			}
			$rsUser = \CUser::GetByID($arRes['user_id']);
			$arUser = $rsUser->Fetch();

			$row = $lAdmin->AddRow($arRes['id'], $arRes);
			$row->AddViewField("id", '<a href="javascript:void(0);" onclick="document.location.href=\''.$APPLICATION->GetCurPage().'?id='.$arRes['id'].'&lang='.LANGUAGE_ID.'\'">'.$arRes['id'].'</a>');
			$row->AddViewField("user_id", '<a href="/bitrix/admin/user_edit.php?lang='. LANGUAGE_ID . '&ID='. $arUser["ID"] .'  "> ['.$arUser["ID"].']</a> ' . $arUser["NAME"]  .' '. $arUser["LAST_NAME"] );
			$row->addSelectField("rank_id",$filterRanks);			
			$row->AddCheckField("active", true);
			
			$arActions=[];
			if($row->arRes["active"] == "Y"){
				$arActions[] = [
					"TEXT" => Loc::getMessage("skyweb24.loyaltyprogram_TABLE_DEACTIVATE"),
					"ACTION" => $lAdmin->ActionDoGroup($row->arRes['id'], 'deactivate'),
					"ONCLICK" => ""
				];
			}else{
				$arActions[] = [
					"TEXT" => Loc::getMessage("skyweb24.loyaltyprogram_TABLE_ACTIVATE"),
					"ACTION" => $lAdmin->ActionDoGroup($row->arRes['id'], 'activate'),
					"ONCLICK" => ""
				];
			}
			
			$arActions[] = [
				"ICON" => "edit",
				"TEXT" => GetMessage('MAIN_EDIT'),
				"TITLE" => GetMessage("IBLOCK_EDIT_ALT"),
				"ONCLICK" => 'document.location.href="/bitrix/admin/skyweb24_loyaltyprogram_ranks_list_users.php?id='.$row->arRes['id'].'&lang='.LANGUAGE_ID.'"'
			];
			
			$arActions[] = [
				"ICON" => "delete",
				"TEXT" => GetMessage('MAIN_DELETE'),
				"TITLE" => GetMessage("IBLOCK_DELETE_ALT"),
				"ACTION" => "if(confirm('".Loc::getMessage("skyweb24.loyaltyprogram_RANKS_CONFIRM_DELETE")."')) ".$lAdmin->ActionDoGroup($row->arRes['id'], 'delete'),
				"ONCLICK" => ""
			];
			
			$row->AddActions($arActions);
		}

		$aContext[] = [
			"TEXT" => Loc::getMessage("skyweb24.loyaltyprogram_RANKS_ADD"),
			"LINK_PARAM" => ["id=add"],
			"ONCLICK" => 'document.location.href="/bitrix/admin/skyweb24_loyaltyprogram_ranks_list_users.php?id=new&lang='.LANGUAGE_ID.'"'
		];
		
		$lAdmin->AddAdminContextMenu($aContext, false);

		$actionList=['edit'=>'edit','delete'=>'delete','activate'=>'activate','deactivate'=>'deactivate'];
		$lAdmin->AddGroupActionTable($actionList);

		$lAdmin->AddFooter(
			array(
				array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
				array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
			)
		);
		
		$lAdmin->DisplayFilter($filterFields);
		$lAdmin->DisplayList();

	}
	
	//agent
	
	public function checkAgent(){
		$res = \CAgent::GetList(["ID" => "DESC"], ["NAME" => "\Skyweb24\Loyaltyprogram\Eventmanager::setRanks();"]);
		if(!$arRes=$res->GetNext()){
			$delayExec=\ConvertTimeStamp(time(), "FULL", LANGUAGE_ID);
			\CAgent::AddAgent("\\Skyweb24\\Loyaltyprogram\\Eventmanager::setRanks();", $this->settings->getModuleId(), "N", 86400, "", "Y", $delayExec);
		}
		return true;
	}
	
	public function getUser(int $id){
		return Entity\RankUsersTable::getList([
			'select'=>[
				'*',
				'rank_name'=>'ranks.name',
				'rank_sort'=>'ranks.sort',
				'rank_active'=>'ranks.active',
				'rank_coeff'=>'ranks.coeff',
				'rank_value'=>'ranks.value',
				'rank_date_setting'=>'ranks.date_setting'
			],
			'filter'=>['user_id'=>$id]
		])->fetch();
	}
	
	public static function getTurnoverByPeriod($fromUnixTime, $toUnixTime, $size){
		global $DB;
		$turnover=[];
		if($size==0){
			$results=$DB->Query('select * from b_user where active="Y";');
			while($row = $results->Fetch()){
				$turnover[$row['ID']]=0.1;
			}
		}else{
			$results=$DB->Query('select 
				sum(b_sale_basket.PRICE*b_sale_basket.QUANTITY) as turnover,
				b_sale_basket.ORDER_ID,
				b_sale_order.DATE_STATUS,
				b_sale_order.USER_ID
				from b_sale_basket
			left join b_sale_order on (b_sale_order.ID=b_sale_basket.ORDER_ID)
			where b_sale_basket.ORDER_ID is not null and b_sale_order.STATUS_ID="F" and b_sale_order.DATE_STATUS>=FROM_UNIXTIME('.$fromUnixTime.') and b_sale_order.DATE_STATUS<=FROM_UNIXTIME('.$toUnixTime.')
			group by b_sale_order.USER_ID');
			while($row = $results->Fetch()){
				$turnover[$row['USER_ID']]=$row['turnover'];
			}
		}
		return $turnover;
	}
	
	public static function setUserRanks($data){
		Entity\RankUsersTable::add($data);
	}

	public static function setRanks(){
		$oldUsers=[];
		$newUsers=[];
		$options=Settings::getInstance()->getOptions();
		$statusOrder=!empty($options['orderstatus'])?$options['orderstatus']:'F';
		$result=Entity\RankUsersTable::getList([]);
		while($arRes = $result->Fetch()){
			$oldUsers[$arRes['user_id']]=$arRes;
		}
		
		$result=Entity\RankUsersTable::getList([
			'order'=>['sort'=>'asc'],
			'filter'=>['active'=>'Y']
		]);
		while($arRes = $result->Fetch()){
			//only turnover!!!
			$arRes['value']=empty($arRes['value'])?0:$arRes['value'];
			$settings=unserialize($arRes['settings']);
			if(empty($settings['period']['type'])){continue;}
		
			$tmpSize=$settings['period']['type']=='quarter'?($settings['period']['size']*3):$settings['period']['size'];
			$tmpType=$settings['period']['type']=='quarter'?'month':$settings['period']['type'];
			
			if($tmpType!='all'){
				$period=Tools::getPeriod($settings['period']['type']);
				$dateFrom = new \DateTime();
				$dateFrom->setTimestamp($period['dateFrom']['unixTime']);
				$dateFrom->modify('-'.$tmpSize.' '.$tmpType);
				$period['dateFrom']=['unixTime'=>$dateFrom->getTimestamp(), 'format'=>\ConvertTimeStamp($dateFrom->getTimestamp(), "SHORT", LANGUAGE_ID)];
				$dateTo = $dateFrom->modify('+'.$tmpSize.' '.$tmpType);
				$period['dateTo']=['unixTime'=>$dateTo->getTimestamp(), 'format'=>\ConvertTimeStamp($dateTo->getTimestamp(), "SHORT", LANGUAGE_ID)];
			}else{
				$dateFrom = \Bitrix\Main\Type\DateTime::createFromTimestamp(0);
				$period['dateFrom']=['unixTime'=>0, 'format'=>\ConvertTimeStamp(0, "SHORT", LANGUAGE_ID)];
				$dateTo = new \DateTime();
				$period['dateTo']=['unixTime'=>$dateTo->getTimestamp(), 'format'=>\ConvertTimeStamp($dateTo->getTimestamp(), "SHORT", LANGUAGE_ID)];
				$period['currentDate']=$period['dateTo'];
			}
			
			$afterPeriod = new \DateTime();
			$afterPeriod = $dateTo->modify('+1 day');
			$period['afterPeriod']=['unixTime'=>$afterPeriod->getTimestamp(), 'format'=>\ConvertTimeStamp($afterPeriod->getTimestamp(), "SHORT", LANGUAGE_ID)];
			$turnover=self::getTurnoverByPeriod($period['dateFrom']['unixTime'], $period['dateTo']['unixTime'], $arRes['value']);
			foreach($turnover as $keyT=>$valT){
				if($valT>$arRes['value']){
					$newUsers[$keyT]=$arRes['id'];
				}
			}
		}
		$currentTime=new \Bitrix\Main\Type\DateTime;
		if(count($newUsers)>0){
			foreach($newUsers as $user_id=>$rank_id){
				$status='insert';
				if(!empty($oldUsers[$user_id])){
					$currentUser=$oldUsers[$user_id];
					if($currentUser['rank_id']==$rank_id && $currentUser['active']=='Y'){
						$status='skip';
					}else{
						$status='update';
					}
					unset($oldUsers[$user_id]);
				}
				if($status!='skip'){
					$upd=[
						'user_id'=>$user_id,
						'rank_id'=>$rank_id,
						'active'=>'Y',
						'date_setting'=>$currentTime
					];
					if($status=='update'){
						Entity\RankUsersTable::update($currentUser['id'], $upd);
					}else{
						Entity\RankUsersTable::add($upd);
					}
				}
			}
		}
		if(count($oldUsers)>0){
			foreach($oldUsers as $nextUser){
				Entity\RankUsersTable::update($nextUser['id'], [
					'active'=>'N',
					'date_setting'=>$currentTime
				]);
			}
		}
	}
	
}

?>