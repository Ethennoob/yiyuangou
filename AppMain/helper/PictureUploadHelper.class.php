<?php
    namespace AppMain\helper;
    use System\BaseHelper;
    /*
     *图片上传类 
     *
     */
    class PictureUploadHelper extends BaseHelper{
        /*
        *单图片上传函数 
        *$pictureName //post过来的图片名称
        *$folderName //保存图片的文件名称
        *$whether   //是否生成缩略图 false不生成 true生成 默认为false
        */
       function pictureUpload($pictureName,$folderName,$whether=false){
           //验证上传文件是否是图片
           $upload=new \System\lib\Upload\Upload();
           $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
           $upload->rootPath = '../html/';
           $upload->savePath = '/images/'.$folderName.'/';   // 设置附件上传（子）目录
           //$upload->saveName = explode('.', $saveName)[0];
           // 开启子目录保存 并以日期（格式为Ymd）为子目录
           $upload->autoSub = true;
           $upload->subName = array('date','Ymd');
           //$upload->savename = $fileName;
           // 上传文件
           $info=$upload->uploadOne($pictureName);
           if(!$info){
               $errorMsg=$upload->getError();
               $this->R(['errorMsg'=>$errorMsg],'40019');
           }
           $fileName=$info['savename'];
           $temPath='.'.$info['savepath'];
           $originalUrl=$info['savepath'].$info['savename'];//原图所在的文件夹路径
           if($whether){
               //生成压缩图片
                $image=new \System\lib\Image\Image($temPath.$fileName);//打开原图路径
                $img1=$image->size();
                $return['img1']['path']=$temPath.$fileName;
                $return['img1']['width']=$img1[0];
                $return['img1']['heigh']=$img1[1];
                //200*200缩略图
                $img2Path=$temPath.str_replace('.', '_thumb200.', $fileName);
                $originalThumbUrl=$info['savepath'].str_replace('.', '_thumb200.', $fileName);//原缩略图所在的文件夹路径
                $image->thumb(200,10000)->save($img2Path);
                $img2=$image->size();
                $return['img2']['path']=$img2Path;
                $return['img2']['width']=$img2[0];
                $return['img2']['heigh']=$img2[1];
           }  else {
               $originalThumbUrl='';
           }
           $returnData=[
               'img'=>$originalUrl,
               'thumb'=>$originalThumbUrl,
           ];
           return $returnData;
       }
        /*
        *多图片上传函数 
        *$pictureNameMore //post过来的图片名称
        *$folderNameMore //保存图片的文件名称
        *$whetherMore    //是否生成缩略图 默认不生成 true生成 
        */
       public function pictureUploadMore($pictureNameMore,$folderNameMore,$whetherMore=false){
            //验证上传文件是否是图片
           $upload=new \System\lib\Upload\Upload();
           $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
           $upload->rootPath = '../html/';
           $upload->savePath = '/images/'.$folderNameMore.'/';   // 设置附件上传（子）目录
           //$upload->saveName = explode('.', $saveName)[0];
           // 开启子目录保存 并以日期（格式为Ymd）为子目录
           $upload->autoSub = true;
           $upload->subName = array('date','Ymd');
           //$upload->savename = $fileName;
           //多图上传
           $moreInfo=$upload->upload($pictureNameMore);
           /*if(!$moreInfo){
             $errorMsg=$upload->getError();
             $this->R(['errorMsg'=>$errorMsg],'40021');
           }*/
           if($moreInfo){
                $figureUrl='';
                $img3PathUrl='';
                foreach ($moreInfo as $key => $v) {
                    $figureName=$v['savename'];
                    $figurePath='.'.$v['savepath'];
                    $figureUrl.=$v['savepath'].$v['savename'].';';
                    if($whetherMore){
                        //生成压缩图片
                       $image=new \System\lib\Image\Image($figurePath.$figureName);//打开原图路径
                       $img1=$image->size();
                       $return['img1']['path']=$figurePath.$figureName;
                       $return['img1']['width']=$img1[0];
                       $return['img1']['heigh']=$img1[1];
                       //200*200缩略图
                       $img3Path=$figurePath.str_replace('.', '_thumb300.', $figureName);
                       $img3PathUrl.=$v['savepath'].str_replace('.', '_thumb300.', $figureName).';';
                       $image->thumb(200,10000)->save($img3Path);
                       $img2=$image->size();
                       $return['img2']['path']=$img3Path;
                       $return['img2']['width']=$img2[0];
                       $return['img2']['heigh']=$img2[1];
                    }
                }
           }else{
               $figureUrl='';
                $img3PathUrl='';
           }
           $returnData=[
               'img'=>$figureUrl,
               'thumb'=>$img3PathUrl,
           ];
           return $returnData;
       }
    }
?>