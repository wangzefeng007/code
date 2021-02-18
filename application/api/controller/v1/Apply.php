<?php

namespace app\api\controller\v1;

use app\admin\model\apply\Images as ImagesModel;
use app\admin\model\apply\Category as CategoryModel;
use app\admin\model\apply\Formlist as FormlistModel;
use app\admin\model\apply\Structure as StructureModel;

use think\Db;
class Apply extends Base
{
    
    //应用图库
    public function images()
    {
        $where['type_status']  = 1;
        $imagemodel = new ImagesModel();
        $list =  $imagemodel
            ->where($where)
            ->select();
        $apply_images =[];
        foreach ($list as $item => $value)
        {
            $apply_images[$item]['image_path'] = cdnurl($value['local_path_image'],true);
        }
        $data[] = $apply_images;
        $this->success(__('success'), $data);
    }
    
    //应用分类图库图库
    public function images_cate()
    {
        $where['type_status']  = 2;
        $imagemodel = new ImagesModel();
        $list =  $imagemodel
            ->where($where)
            ->select();
        $apply_images =[];
        foreach ($list as $item => $value)
        {
            $apply_images[$item]['image_path'] = cdnurl($value['local_path_image'],true);
        }
        $data[] = $apply_images;
        $this->success(__('success'), $data);
    }
    public function get_apply_cate()
    {
        $company_id = $this->get_companyid();
        // $company_id = 3;
        $category =  new CategoryModel;
        $company_list = $category->where('company_id',$company_id)->where('deletetime',0)
        ->order('weigh', 'desc')
        ->select();
        $data['category'] = $company_list;
        $this->success(__('success'), $data);
    }
    // 初始化
	public function get_apply_cate_list(){
		$company_id = $this->get_companyid();
// 		$company_id = 3;
		$category =  new CategoryModel;
		$formlist = new FormlistModel;
		
		$company_list = Db::name('s_apply_category')->where('company_id',$company_id)->where('deletetime',0)->select();
// 		dump($company_list);
		foreach($company_list as $key => $val){
		  //  dump($val);
		  //dump($company_id);
		  //dump($val['id']);
			$inf = Db::name('s_apply_formlist')->where('category_id',$val['id'])->where('company_id',$company_id)->where('deletetime',0)->select();
// 			dump($inf);
			$company_list[$key]['list'] = $inf;
		}
// 		dump($company_list);
		$this->success(__('获取成功'),$company_list);
	}
    //创建应用分类
    public function set_apply_cate()
    {
        $uid = $this->auth->id;
        //获取当前公司id
        $company_id = $this->get_companyid();
        // 获取当前分类名
        $cate_name = $this->request->post('cate_name');
        $imgbg = $this->request->post('imgbg');
        if (empty($cate_name)) {
            $this->error(__('名称不能为空'));
        }
        $weigh = $this->request->post('weigh');
        // dump($weigh);die;
        if (empty($weigh) && $weigh < 0) {
          $weigh = 1;
        }
        // 验证当前公司是否已经有这个名称了 如果重复就不能添加 
        if(!$this->get_cate_name($cate_name,$company_id))
        {
             $this->error(__('当前名称已存在'));
        }
        $category =  new CategoryModel;
        $category->cate_name = $cate_name;
        $category->company_id = $company_id;
        $category->imgbg = $imgbg;
        $category->u_id = $uid;
        $category->weigh = intval($weigh);
        $isok = $category->save();
        $inf = $this->get_apply_cate_list($company_id);
        if($isok)
        {
            
            $this->setlog('创建应用分类',$cate_name);
            $this->success(__('创建成功'),$inf);
        }
         $this->error(__('当前名称已存在'));
    }
    //删除应用分类
	public function del_apply_cate()
	{
		$category = new CategoryModel;
		//获取用户id
		$uid = $this->auth->id;
		//获取当前公司id
		$company_id = $this->get_companyid();
		// 获取当前分类id
		$cate_id = $this->request->post('cate_id');
		
		$sure = CategoryModel::where('u_id',$uid)->where('id',$cate_id)->where('company_id',$company_id)->find();
		$suretwo = FormlistModel::where('category_id',$cate_id)->where('company_id',$company_id)->select();          
		if($suretwo){
		    $this->error(__('应用中还有内容'));
		}else{
		    if($sure){
		        $sure->deletetime = time();
    			$sure->save();
    			$this->setlog('删除应用分类'.$sure['cate_name']);
    			$inf = $this->get_apply_cate_list($company_id);
    			$this->success(__('删除成功'),$inf);
		    }else{
		        $this->error(__('只能创建人删除'));
		    }
		}
// 		if($sure && $suretwo){
// 			$sure->deletetime = time();
// 			$sure->save();
// 			$this->setlog('删除应用分类'.$sure['cate_name']);
// 			$this->success(__('删除成功'));
// 		}else{
// 			$this->error(__('只能创建人删除'));
// 		}
	}
    /**
     *判断当前的应用是否重复 
     */
    public function get_cate_name($cate_name,$company_id)
    {
        $category =  new CategoryModel;
        $where['company_id'] = $company_id;
        $where['cate_name'] = $cate_name;
        $company_list = $category->where($where)->where('deletetime',0)->find();
        if (empty($company_list)) {
           return true;
        }
        return false;
    }
    
    //当前用户可以看到的应用分类 返回为数组
    // public function get_user_apply_cate()
    // {
    //   $data =  $this->get_user_apply_cates(); 
    //   $categorymodel  = new CategoryModel();
    //   $categorylist =  $categorymodel->where('id','in',$data)->order('weigh','desc')->select();
    //   $data = [];
    //   foreach($categorylist as $key => $value)
    //   {
    //       $data[$key]['id'] = $value['id'];
    //       $data[$key]['cate_name'] = $value['cate_name'];
    //       $data[$key]['company_id'] = $value['company_id'];
    //       $data[$key]['weigh'] = $value['weigh'];
    //   }
    //   $this->success(__('success'), $data);
    // }
    public function get_user_apply_cate(){
		$category = new CategoryModel;
		$uid = $this->auth->id;
		//获取当前公司id
		$company_id = $this->get_companyid();
		$inf = $category->where('company_id',$company_id)->where('deletetime',0)->select();
		if($inf){
			$this->success(__('查询成功'),$inf);
		}else{
			$this->error(__('查询失败'));
		}
		
	}
    /**
     * 添加应用
     * 获取到 
     * 用户id
     * 获取当前公司id
     * 获取人员id
     * 获取结构id 如果没有选择为空
     * 
     */ 
    public function add()
    {
		$Formlist = new FormlistModel();
        // $this->create_Message();
        $uid = $this->auth->id;
		//公司id
        $company_id = $this->get_companyid();
        //选择的分类id
        $apply_id = $this->request->post('apply_id');
		//选择的结构id 
		$str_id = $this->request->post('str_id');
		//应用名称
        $name = $this->request->post('name');
		//应用类型
		$type = $this->request->post('type');
		//imgas_id 图库 id
		$imgas_id = $this->request->post('imgas_id');
		//背景色
		$imgbg = $this->request->post('imgbg');
		//创建时间
		$createtime = time();
		
		$data['id'] = NULL;
		$data['company_id'] = $company_id;
		$data['category_id'] = $apply_id;
		$data['str_id'] = $str_id;
		$data['imgas_id'] = $imgas_id ;
		$data['type'] = $type;
		$data['name'] = $name;
		$data['imgbg'] = $imgbg;
		$data['createtime'] = $createtime;
		$data['u_id'] = $uid;
// 		dump($data);
		$select = $Formlist->where('name',$name)->where('company_id',$company_id)->find();
		
		if($select){
			$this->error(__('当前名称已存在'));
		}else{
		  //  dump($data);
			$sure = $Formlist->insert($data);
// 			$insertId = Db::name('s_apply_formlist')->getLastInsID();
// 			$inf = $Formlist->where('id',$insertId)->find();
            $inf = $this->get_apply_cate_list($company_id);
			if($sure){
			    $this->setlog('创建应用'.$name);
				$this->success(__('创建成功'),$inf);
			}else{
				$this->error(__('稍后重试'));
			}
		}
		
    }
    /* 
	删除应用
	*/
    public function app_del(){
		$Formlist = new FormlistModel();
		
		$company_id = $this->get_companyid();
		
		$uid = $this->auth->id;
// 		dump($uid);
		/* 应用id */
		$formlist_id = $this->request->post('formlist_id');
		$inf = Db::name('s_apply_formlist')->where('id',$formlist_id)->where('u_id',$uid)->where('company_id',$company_id)->find();
		if($inf){
    		$time = time();
            $delsure = Db::name('s_apply_formlist')->where('id',$inf['id'])->update(['deletetime'=>$time]);
			$this->setlog('删除应用'.$inf['name']);
			$list = $this->get_apply_cate_list();
			$this->success(__('删除成功'),$list);
		}else{
			$this->error(__('只能创建人删除'));
		}
	}
}