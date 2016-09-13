<?php

namespace Drupal\xmlnode\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

/**
 * Plugin implementation of the 'xmlnode' field type.
 *
 * @FieldType(
 *   id = "Xmlnode",
 *   label = @Translation("Xmlnode"),
 *   description = @Translation("Stores an xmlnode."),
 *   category = @Translation("Custom"),
 *   default_widget = "XmlnodeDefaultWidget",
 *   default_formatter = "XmlnodeDefaultFormatter"
 * )
 */
class Xmlnode extends FieldItemBase {

    /**
     * Field type properties definition.
     *
     * Inside this method we defines all the fields (properties) that our
     * custom field type will have.
     *
     * Here there is a list of allowed property types: https://goo.gl/sIBBgO
     */
    public static function propertyDefinitions(StorageDefinition $storage) {

        $properties = [];


        $properties['xml'] = DataDefinition::create('string')
            ->setLabel(t('Xml'));



        return $properties;
    }

    /**
     * Field type schema definition.
     *
     * Inside this method we defines the database schema used to store data for
     * our field type.
     *
     * Here there is a list of allowed column types: https://goo.gl/YY3G7s
     */
    public static function schema(StorageDefinition $storage) {

        $columns = [];
        $columns['xml'] = [
            'type' => 'char',
            'length' => 255,
        ];
        return [
            'columns' => $columns,
            'indexes' => [],
        ];
    }

    /**
     * Define when the field type is empty.
     *
     * This method is important and used internally by Drupal. Take a moment
     * to define when the field fype must be considered empty.
     */
    public function isEmpty() {

        $isEmpty = empty($this->get('xml')->getValue());

        return $isEmpty;
    }


    /**
     * 处理xml，获取里面的数据并数据保存到数据库，删掉xml文件。
     * 下载远程MP3文件
     */
    public function preSave() {

        //1.得到文件路径
        $fid=$this->xml;
        $file=File::load((int)$fid);
        if(is_null($file)){
            $path = 'null';
        }else {
            $path = $file->url();
        }

        //2.读取xml文件
        $xml_arr =simplexml_load_file($path);
        foreach($xml_arr->word as $k=>$v){

            //3.系统创建node
            $node = Node::create(array(
                'type' => 'word',
                'uid'=> 1,
                'status' => 1,
                'promote'=>0,
                'sticky'=> 0,
                'title'=> (string)$v->spelling,
            ));

            //4.字段赋值，获取每一个node的id,uuid
            $re=(string)$v->reference;
            $reference=str_replace('[','(',$re);
            $reference=str_replace(']',')',$reference);
            $voc=array(
                'spelling' =>(string)$v->spelling,
                'pronunciation'=> (string)$v->pronunciation,
                'audio'=>'',
                'meaning'=>(string)$v->meaning,
                'partofspeech'=>(string)$v->partOfSpeech,
                'sentence'=>(string)$v->example[0].'<br/>'.(string)$v->example[1],
                'reference'=>$reference,
            );
            $node->{'field_vocabulary'} = $voc;
            $node->save();
            //id
            $tid=$node->id();
            //uuid
            $uuid=$node->uuid();

            //5.下载mp3文件
            //扇贝API接口
            $spelling=(string)$v->spelling;
            $url="https://api.shanbay.com/bdc/search/?word=".$spelling;
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $data=curl_exec($ch);
            curl_close($ch);

            //获取返回的json数据
            $data_arr=json_decode($data);
            //获取音频地址，下载音频
            if(!$data_arr->status_code){
                $uk_audio_url=$data_arr->data->audio_addresses->uk[0];
                $file_path="./Audio";
                //uuid为文件名
                $basename=$uuid.'.mp3';
                //调用远程文件下载函数
                $this->getMp3($uk_audio_url,$file_path,$basename);
            }

            //6.关联到文章
            $article = $this->getEntity();
            $article->{'field_word'}[] = array(
                'target_id' =>$tid
            );

        }


        //7.删除文件
        file_delete($fid);

    }


    /*
    *功能：php完美实现下载远程文件保存到本地
    *参数：文件url,保存文件目录,保存文件名称，使用的下载方式
    *当保存文件名称为空时则使用远程文件原来的名称
    */
    public function getMp3($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
            if($ext!='.mp3'){
                return array('file_name'=>'','save_path'=>'','error'=>3);
            }
            $filename=time().$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $mp3=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $mp3=ob_get_contents();
            ob_end_clean();
        }
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$mp3);
        fclose($fp2);
        unset($mp3,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }



} // class


