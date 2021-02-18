<?php

namespace app\api\controller\v1;

use app\admin\model\apply\Department as Department;

use think\Db;

class Powerlist_ extends Base
{
	/*  */
	public function getpower(){
		// $company_id = $this->get_companyid();
		$company_id = 3;
		$inf = Db::name('s_dddepartment_list')->where('company_id',$company_id)->select();
		foreach($inf as $key => $val){
			$user = Db::name('s_user_organization')->where('dd_orgid',$val['ddid'])->where('company_id',$company_id)->select();
			$inf[$key]['userlist'] = $user;
		}
		// dump($inf);
		$lastinf = $this->getChild($inf);
		$data['department'] = $lastinf;
		$roleinf = Db::name('s_rolegroup')->where('company_id',$company_id)->select();
		
		foreach($roleinf as $rolekey => $roleval){
			$srolelist = Db::name('s_rolelist')->where('groupId',$roleval['groupId'])->select();
			$roleinf[$rolekey]['rolelist'] = $srolelist;
		}
		// dump($roleinf);
		$data['role'] = $roleinf;
		$this->success(__('success'), $data);
	}
	public function getrole(){
		// $ddid = $this->request->post('ddid');
	}
	function getChild($data, $id = 0)
	
		    {
	
		        $child = [];
	
		        foreach ($data as $key => $datum) {
	
		            if ($datum['parentid'] == $id) {
	
		                $child[$datum['ddid']] = $datum;
	
		                unset($data[$key]);
	
		                $child[$datum['ddid']]['itemlist'] = $this->getChild($data, $datum['ddid']);
	
		            }
	
		        }
	
		        return $child;
	
		}
}