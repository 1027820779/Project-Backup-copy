<?php
namespace Drupal\json_export\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\node\Entity\Node;

class JsonExportController {

    /**
     * 按照词汇id返回相关内容
     * @param $ID 单词ID
     * @return JsonResponse 返回json数据
     */
    public function index($ID){

        $node = Node::load($ID);
        if(!empty($node)){
            $type=$node->getType();
            if($type=="word"){
                $json_arr=array();
                //拼写
                $json_arr["spelling"]=$node->get("field_danci")->value;
                //音标
                $json_arr["pronunciation"]=$node->get("field_yinbiao")->value;
                //发音
                $audio_uri=$node->get("field_duyin")->entity->uri->value;
                $json_arr["audio"]=file_create_url($audio_uri);
                //意思
                $json_arr["meaning"][]=$node->get("field_yisi")->value;
                $json_arr["meaning"][]=$node->get("field_yingwenshiyi")->value;
                //词性
                $json_arr["partOfSpeech"]=$node->get("field_cixing")->value;
                //例句
                $json_arr["sentence"][]=$node->get("field_liju")->value;
                $json_arr["sentence"][]=$node->get("field_yi_wen")->value;
                //单词变体
                $json_arr["reference"]=$node->get("field_dancibianti")->value;
                //uuid
                $json_arr["uuid"]=$node->uuid();
                $json_arr["id"]=$ID;

                return new JsonResponse($json_arr);

            }else{
                return new JsonResponse("null");
            }
        }else{
            return new JsonResponse("null");
        }

    }

    /**
     * 返回一片文章所关联的所有的词汇数据
     * @param $AID 文章id
     * @return JsonResponse 这个文章所关联的所有词汇的json
     */


    public function word_list($AID){
        $node = Node::load($AID);
        if(!empty($node)){
            $type=$node->getType();
            $length=count($node->get("field_word"));
            if($type=="article" && $length!=0){
                $i=0;
                $all_json=array();
                for($i;$i<$length;$i++){
                    $id=$node->get("field_word")[$i]->target_id;
                    $all_json[]=$this->search_word($id);
                }
                return new JsonResponse($all_json);

            }else{
                return new JsonResponse("null");
            }
        }else{
            return new JsonResponse("null");
        }
    }


    /**
     * 回调函数
     * @param $id word id
     * @return array json data
     */


    public function search_word($id){
        $node = Node::load($id);
        $json_arr=array();
        //拼写
        $json_arr["spelling"]=$node->get("field_danci")->value;
        //音标
        $json_arr["pronunciation"]=$node->get("field_yinbiao")->value;
        //发音
        $audio_uri=$node->get("field_duyin")->entity->uri->value;
        $json_arr["audio"]=file_create_url($audio_uri);
        //意思
        $json_arr["meaning"][]=$node->get("field_yisi")->value;
        $json_arr["meaning"][]=$node->get("field_yingwenshiyi")->value;
        //词性
        $json_arr["partOfSpeech"]=$node->get("field_cixing")->value;
        //例句
        $json_arr["sentence"][]=$node->get("field_liju")->value;
        $json_arr["sentence"][]=$node->get("field_yi_wen")->value;
        //单词变体
        $json_arr["reference"]=$node->get("field_dancibianti")->value;
        //uuid
        $json_arr["uuid"]=$node->uuid();
        $json_arr["id"]=$id;

        return $json_arr;
    }


    /**
     * @param $AID 文章id
     * @return JsonResponse 带词汇uid的文章json
     */


    public function article_json($AID){
        $node = Node::load($AID);
        if(!empty($node)){
            $type=$node->getType();
            if($type=="article"){
                $body=$node->get("body")->value;
                $title=$node->get("title")->value;
                $length=count($node->get("field_word"));
                if($type=="article" && $length!=0){
                    $i=0;
                    for($i;$i<$length;$i++){
                        $id=$node->get("field_word")[$i]->target_id;
                        $word=$this->search_word($id)["spelling"];
                        if(preg_match_all('/(<a).+?(<\/a>)/im',$body,$matches)){
                            foreach($matches[0] as $l=>$w){
                                if(!strpos($w,"uid")){
                                    if(strpos($w,$word)){
                                        $index_pos=strpos($w,$word);
                                        $uid_str='  uid="'.$id.'"  ';
                                        $new_w=substr_replace($w,$uid_str,$index_pos-1,0);
                                        $in=strpos($body,$w);
                                        $len=strlen($w);
                                        $body=substr_replace($body,$new_w,$in,$len);
                                    }
                                }
                            }

                        }
                    }
                    $data_arr=array();
                    $data_arr["title"]=$title;
                    $data_arr["body"]=$body;
                    $data_arr["uuid"]=$node->uuid();
                    $data_arr["id"]=$AID;
                    return new JsonResponse($data_arr);
                }
            }else{
                return new JsonResponse("null");
            }
        }else{
            return new JsonResponse("null");
        }

    }







}

