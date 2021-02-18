<?php


namespace app\api\controller\v1;

use app\admin\model\apply\Strlist as StrlistModel;
use app\admin\model\apply\Structure as StructureModel;
use app\BaseController;
use think\Db;


/**
 * 表单目录树
 */
class Formtree extends Base
{
    function getChild($data, $id = 0)

    {
        
        
        // dump($data);die;
        $child = array();

        foreach ($data as $key => $datum) {
            if ($datum['str_pid'] == $id) {

                // $datum['key'] = $datum['id'];
				
                $datum['children'] = $this->getChild($data, $datum['id']);

                $child[] = $datum;
                unset($data[$key]);

            }

        }

        return $child;

    }
	public function get_tree($arr,$id,$level){
		    $list =array();
		    foreach ($arr as $k=>$v){
		        if ($v['pid'] == $id){
		            $v['level']=$level;
		            $v['son'] = $this->get_tree($arr,$v['id'],$level+1);
		            $list[] = $v;
		        }
		    }
		    return $list;
	}
    public function getTree(){  
        $inf = Db::name('category')->field('id,pid,name as title,sureform')->where('type','project')->where('pid','<>',0)->select()->toArray();
        $inf = $this->getChild($inf);
        return returnSuccessMsg(1,"成功",$inf);
    }
	public function getlist(){
		$tree = $this->request->post('tree');
		dump($tree);
	}
	public function save_tree_node(){
		
		$strlist = new StrlistModel;
		$structure = new StructureModel;
		
		//获取用户id
		$u_id = $this->auth->id;
		// $u_id = 4;
		//获取当前公司id
		$company_id = $this->get_companyid();
		// $company_id = 3;
		//父级id
		// $pid = $this->request->post('pid');
		// $pid = 0;
		//应用id
		$apply_id = $this->request->post('apply_id');
		// $apply_id = 18;
		//应用名称
		$str_name = $this->request->post('str_name');
		// $str_name = 'aaa';
		$str_pid = $this->request->post('str_pid');
		
		$str_type = $this->request->post('type');
		// dump($str_pid);
		//如果是第一层 添加到strlist
		if($str_pid == 0){
			$structure->apply_id = $apply_id;
			$structure->u_id = $u_id;
			$structure->str_name = $str_name;
			$structure->company_id = $company_id;
			$structure->createtime = time();
			$structure->str_pid = $str_pid;
			$structure->type = $str_type;
			$structure->save();
			$structure_id = $structure->id;
			$addid = $structure->id;
			
			$strlist->apply_id = $apply_id;
			$strlist->structure_id = $structure_id;
			$strlist->save();
			
			$this->success(__('创建成功'),$addid);
		//如果不是第一层 
		}else{
			$structure->apply_id = $apply_id;
			$structure->u_id = $u_id;
			$structure->str_name = $str_name;
			$structure->company_id = $company_id;
			$structure->createtime = time();
			$structure->str_pid = $str_pid;
			$structure->type = $str_type;
			$structure->save();
			$addid = $structure->id;
			$this->success(__('创建成功'),$addid);
		}
	}
	public function get_tree_node(){
		//获取用户id
		$u_id = $this->auth->id;
		// $u_id = 4;
		//获取当前公司id
		$company_id = $this->get_companyid();
		// $company_id = 3;
		//apply_id
		$apply_id = $this->request->post('apply_id');
		// $apply_id = 18;
		// $inf = Db::name('s_strlist')->where('apply_id',$apply_id)->column('structure_id');
		$list = Db::name('s_apply_structure')->field('id,str_name as label,icon,str_pid,type')->where('apply_id',$apply_id)->where('deletetime',0)->select();
		$list = $this->getChild($list);
		$form_list = Db::name('s_apply_structure')->field('id,str_name as label,icon,str_pid,type')->where('company_id',$company_id)->where('type',2)->where('apply_id',$apply_id)->where('deletetime',0)->select();
		$this->success(__('获取成功'),$list);
	}
	public function del_tree_node(){
		//获取用户id
		$u_id = $this->auth->id;
		// $u_id = 4;
		//获取当前公司id
		$company_id = $this->get_companyid();
		// $company_id = 3;
		//结构的id
		$structure_id = $this->request->post('structure_id');
		// $structure_id = 2;
		$structure_idinf = Db::name('s_apply_structure')->where('id',$structure_id)->find();
		if($structure_idinf['str_pid'] == 0){
			$sure = Db::name('s_apply_structure')->where('str_pid',$structure_idinf['id'])->where('deletetime',0)->count();
			if($sure>0){
				$this->error(__('请先删除目录下所有应用'));
			}else{
				$del = Db::name('s_apply_structure')->where('id',$structure_id)->delete();
				if($del !== false){
					$this->success(__('删除成功'));
				}else{
					$this->error(__('删除失败'));
				}
			}
		}else{
			if($u_id == $structure_idinf['u_id']){
				$del = Db::name('s_apply_structure')->where('id',$structure_id)->delete();
				if($del !== false){
					$this->success(__('删除成功'));
				}else{
					$this->error(__('删除失败'));
				}
			}else{
				$this->error(__('只能创建人删除'));
			}
		}
		// dump($structure_idinf);
	}
	public function del_node(){
		
		$u_id = $this->auth->id;
		
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$id = isset($_POST['structure_id']) ? $_POST['structure_id'] : '';
		$time = time();
		$inf = Db::name('s_apply_structure')->where('id',$id)->find();
		if($type == 1){
			if($inf['u_id'] == $u_id){
				$n = 0;
				$soninf = Db::name('s_apply_structure')->where('str_id',$id)->select();
				foreach($soninf as $key => $val){
					if($val['u_id'] == $u_id){
						$n = $n+1;
					}
				}
				if($n>0){
					$this->error(__('结构下有其他人的表单'));
				}else{
					$del = Db::name('s_apply_structure')->where('id',$id)->update(['deletetime'=>$time]);
					$delv = Db::name('s_apply_structure')->where('str_pid',$id)->update(['deletetime'=>$time]);
				}
			}else{
				$this->error(__('只能本人删除'));
			}
		}else{
			$del = Db::name('s_apply_structure')->where('id',$id)->update(['deletetime'=>$time]);
		}
		$this->setlog('删除'.$inf['str_name']);
		
		$this->success(__('成功'));
	}
	public function get_tree_list($apply_id,$company_id){
		
		$strlist = new StrlistModel;
		
		// $u_id = $this->auth->id;
		// $company_id = $this->get_companyid();
		
		$inf = Db::name('s_strlist')->where('apply_id',$apply_id)->column('structure_id');
		// dump($inf);
		$list = Db::name('s_apply_structure')->field('id,str_name as label,str_pid')->whereIn('id',$inf)->select();
		// dump($list);
		return $list;
	}
	public function re_name(){
		$id = isset($_POST['id']) ? $_POST['id'] : '8';
		$name = isset($_POST['name']) ? $_POST['name'] : '重命名ces';
		// type1 分类 2分组和表单
		$type = isset($_POST['type']) ? $_POST['type'] : '2';
		if($type == '1'){
			$sure = Db::name('s_apply_category')->where('id',$id)->update(['cate_name' => $name]);
			// dump(Db::name('s_apply_category')->getlastsql());
			if($sure !== false){
				$this->success(__('成功'));
			}else{
				$this->error(__('失败'));
			}
		}else if($type == '2'){
			$sure = Db::name('s_apply_structure')->where('id',$id)->update(['str_name' => $name]);
			if($sure !== false){
				$this->success(__('成功'));
			}else{
				$this->error(__('失败'));
			}
		}else{
			$sure = Db::name('s_apply_formlist')->where('id',$id)->update(['name' => $name]);
			if($sure !== false){
				$this->success(__('成功'));
			}else{
				$this->error(__('失败'));
			}
		}
		
	}
}