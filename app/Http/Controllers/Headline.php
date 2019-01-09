<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;


class Headline extends Controller{
    public function addHeadline(){  //添加头条
        $input=request()->all();
        $content=$input['content'];
        date_default_timezone_set('Asia/Shanghai');
        $input['date']=date('Y-m-d');
        $input['time']=date('H:i:s');
        $list=Db::table('headline')->insert($input);
        if($list){
            return ['state'=>1];
        }else {
            return ['state'=>0];
        }
    }
    public function showHeadline(){ //查看头条
        $input=request()->input('id');
        if(empty($input)){
            $list=Db::table('headline')->orderBy('id', 'DESC')->get();
            return $list;
        }else{
            $list=Db::table('headline')->where('id',$input)->get();
            return $list;
        }
    }
    public function showHeadlineType(){ //查看头条分类
        $input=request()->input('id');
        if(empty($input)){
            $headlineFirstType=Db::table('headline_type')->where('type',0)->get();
            $headlineSecondType=Db::table('headline_type')->where('type','!=',0)->get();
            return ['headlineFirstType'=>$headlineFirstType,'headlineSecondType'=>$headlineSecondType];
        }else{
            $second=Db::table('headline_type')->where('id',$input)->first();
            if(empty($second)){ //判断所属二级分类是否存在
                $headlineFirstType=Db::table('headline_type')->where('type',0)->get();
                $headlineSecondType=Db::table('headline_type')->where('type','!=',0)->get();
                return ['headlineFirstType'=>$headlineFirstType,
                        'headlineSecondType'=>$headlineSecondType,
                        'fType'=>'',
                        'sType'=>''];
            }else{
                $first=Db::table('headline_type')->where('id',$second->type)->first();
                if(empty($first)){  //判断所属一级分类是否存在
                    $headlineFirstType=Db::table('headline_type')->where('type',0)->get();
                    $headlineSecondType=Db::table('headline_type')->where('type','!=',0)->get();
                    return ['headlineFirstType'=>$headlineFirstType,
                        'headlineSecondType'=>$headlineSecondType,
                        'fType'=>'',
                        'sType'=>''];
                }
                $headlineFirstType=Db::table('headline_type')->where('type',0)->get();
                $headlineSecondType=Db::table('headline_type')->where('type','!=',0)->get();
                return ['headlineFirstType'=>$headlineFirstType,
                    'headlineSecondType'=>$headlineSecondType,
                    'fType'=>$first->id,
                    'sType'=>$second->id];
            }
        }
    }
    public function delHeadline(){  //删除头条
        $input=request()->input('id');
        $headline=Db::table('headline')->where('id',$input)->first();
        if(!empty($headline->cover)){
            $imgUrl=base_path().'/public'.str_replace('\\','/',$headline->cover);
            unlink($imgUrl);
        }
        $res=Db::table('headline')->where('id',$input)->delete();
        if($res){
            return ['state'=>'1'];
        }else{
            return ['state'=>'0'];
        }
    }
    public function changeHeadline(){   //修改头条
        $input=request()->all();
        $res=Db::table('headline')->where('id',$input['id'])
            ->update(['title'=>$input['title'],
                'content'=>$input['content'],
                'date'=>$input['date'],
                'time'=>$input['time'],
                'cover'=>$input['cover'],
                'type'=>$input['type']
            ]);
        if($res){
            return ['state'=>'1'];
        }else{
            return ['state'=>'0'];
        }
    }
    public function headlinePage(){ //头条分页
        $headline = DB::table('headline')->paginate(10);
        return $headline;
    }
    public function headlineTypePage(){ //分类
        $fType=Db::table('headline_type')->where('type',0)->get();
        $sType=Db::table('headline_type')->where('type','!=',0)->get();
        return ['fType'=>$fType,'sType'=>$sType];
    }
    public function showSecondType(){
        $id=request()->input('id');
        $list=Db::table('headline_type')->where('type',$id)->get();
        return $list;
    }
    public function addHeadlineType(){  //添加头条分类
        $t=request()->input('type');
        $typeName=request()->input('typeName');
        if(empty($t)){
            $type['typeName']=$typeName;
            $type['type']=0;
            $res=Db::table('headline_type')->insert($type);
            if($res){
                return ['state'=>'1'];
            }else{
                return ['state'=>'0'];
            }
        }else{
            $type['typeName']=$typeName;
            $type['type']=$t;
            $res=Db::table('headline_type')->insert($type);
            if($res){
                return ['state'=>'1'];
            }else{
                return ['state'=>'0'];
            }
        }
    }
    public function delHeadlineType(){   //删除头条分类
        $id=request()->input('id');
        $list=Db::table('headline_type')->where('id',$id)->first();
        if($list->type==0){
            $res1=Db::table('headline_type')->where('id',$id)->delete();
            $res2=Db::table('headline_type')->where('type',$list->id)->delete();
            if($res1){
                return ['state'=>1];
            }else{
                return ['state'=>0];
            }
        }else{
            $res=Db::table('headline_type')->where('id',$id)->delete();
            if($res){
                return ['state'=>1];
            }else{
                return ['state'=>0];
            }
        }
    }
}
