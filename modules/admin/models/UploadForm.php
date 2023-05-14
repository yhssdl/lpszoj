<?php

namespace app\modules\admin\models;

use Yii;
use app\models\Problem;
use yii\base\Model;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * UploadForm 用来导入题目
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $problemFile;

    public function rules()
    {
        return [
            [['problemFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip, xml'],
        ];
    }

    public function upload()
    {
        $ret = "";
        if ($this->validate()) {
            $tempFile = $this->problemFile->tempName;
            if ($this->problemFile->extension == "zip") {
                $resource = zip_open($tempFile);
                $tempFile = tempnam("/tmp", "fps");
                while ($dirResource = zip_read($resource)) {
                    $fileName = zip_entry_name($dirResource);
                    if (!is_dir($fileName)) {
                        $fileSize = zip_entry_filesize($dirResource);
                        $fileContent = zip_entry_read($dirResource, $fileSize);
                        file_put_contents($tempFile, $fileContent);
                        $ret = self::importFPS($tempFile);
                    }
                    zip_entry_close($dirResource);
                }
                zip_close($resource);
            } else {
                $ret = self::importFPS($tempFile);
            }
            return $ret;
        } else {
            return $ret;
        }
    }

    public static function importFPS($tempFile)
    {
        $xmlDoc = simplexml_load_file($tempFile, 'SimpleXMLElement', LIBXML_PARSEHUGE);
        $searchNodes = $xmlDoc->xpath("/fps/item");
        set_time_limit(0);
        ob_end_clean();
        $msg = "";
        foreach ($searchNodes as $searchNode) {
            $title = (string)$searchNode->title;
            if (!self::hasProblem($title)) {
                $spjCode = self::getValue($searchNode, 'spj');
                $spj = trim($spjCode) ? 1 : 0;
                $time_limit = $searchNode->time_limit;
                $unit = self::getAttribute($searchNode,'time_limit','unit');
                if ($unit == 'ms')
                    $time_limit /= 1000;
                $memory_limit = self::getValue($searchNode, 'memory_limit');
                $unit = self::getAttribute($searchNode,'memory_limit','unit');
                if ($unit == 'kb')
                    $memory_limit  /= 1024;
                $newProblem = new Problem();
                $newProblem->title = $title;
                $newProblem->description = self::getValue($searchNode, 'description');
                $newProblem->time_limit = $time_limit;
                $newProblem->memory_limit = $memory_limit;
                $newProblem->input = self::getValue($searchNode, 'input');
                $newProblem->output = self::getValue($searchNode, 'output');
                $newProblem->hint = self::getValue($searchNode, 'hint');
                $newProblem->source = self::getValue($searchNode, 'source');
                $newProblem->tags = self::getValue($searchNode, 'tags');

                $newProblem->sample_input = serialize([self::getValue($searchNode, 'sample_input'), self::getValue($searchNode, 'sample_input1'), self::getValue($searchNode, 'sample_input2')]);
                $newProblem->sample_output = serialize([self::getValue($searchNode, 'sample_output'), self::getValue($searchNode, 'sample_output1'), self::getValue($searchNode, 'sample_output2')]);


                $newProblem->solution = str_replace("\n","<br>",htmlentities(self::getValue($searchNode, 'solution')));
                $newProblem->spj = $spj;
                $newProblem->created_by = Yii::$app->user->id;
                $newProblem->save();
                $pid = $newProblem->id;

                //创建输入文件
                $testInputs = $searchNode->children()->test_input;
                $testCnt = 0;
                foreach($testInputs as $testNode){
                    self::importTestData($pid, $testCnt++ . ".in", $testNode);
                }
                
                //创建输出文件
                $testOutputs = $searchNode->children()->test_output;
                $testCnt = 0;
                foreach($testOutputs as $testNode){
                    self::importTestData($pid, $testCnt++ . ".out", $testNode);
                }

                if($testCnt == 0) $msg = $msg ."$title <font color=red>没有测试数据!</font><br>";

                //SPJ 特判程序
                if ($spj) {
                    $basedir = Yii::$app->params['judgeProblemDataPath'] . $pid;
                    $fp = fopen("$basedir/spj.cc","w");
                    fputs($fp, $spjCode);
                    fclose($fp);
                    system( " g++ -o $basedir/spj $basedir/spj.cc  ");
                    if(!file_exists("$basedir/spj") ){
                        $fp = fopen("$basedir/spj.c","w");
                        fputs($fp, $spjCode);
                        fclose($fp);
                        system( " gcc -o $basedir/spj $basedir/spj.c  ");
                        if(!file_exists("$basedir/spj")){
                            $msg = $msg ."$title 导入成功<br>";
                            $msg = $msg . "你需要手动编译SPJ特殊判决 $basedir/spj.cc[  g++ -o $basedir/spj $basedir/spj.cc   ]<br>";
                            continue;
                        } else {
                            unlink("$basedir/spj.cc");
                        }
                    }
                }
                $msg = $msg ."$title 导入成功<br>";
                //return "$title 导入成功<br>";
            } else {
                $msg = $msg . "$title 已经存在<br>";
                //return "$title 已经存在<br>";
            }
            flush();
        }
        return $msg;
        //exit;
    }

    public static function hasProblem($title)
    {
        return (new Query())->select('1')
            ->from('{{%problem}}')
            ->where('md5(title)=:title', [':title' => md5($title)])
            ->count();
    }

    public static function getAttribute($Node, $TagName,$attribute)
    {
        return $Node->children()->$TagName->attributes()->$attribute;
    }

    public static function getValue($Node, $TagName)
    {
        return (string)$Node->$TagName;
    }

    public static function importTestData($pid, $filename, $fileContent)
    {
        $basedir = Yii::$app->params['judgeProblemDataPath'] . $pid;
        @mkdir($basedir);
        $fp = @fopen($basedir . "/$filename", "w");
        if ($fp) {
            fputs($fp, preg_replace("(\r\n)", "\n", $fileContent));
            fclose($fp);
        } else {
            echo "Error while opening ".$basedir . "/$filename.";
        }
    }

    
    function fixurl($img_url) {
        if(substr($img_url,0,4)=="data") return $img_url;
        $img_url = html_entity_decode($img_url,ENT_QUOTES,"UTF-8");
    
        if (substr($img_url,0,4)!="http") {
        if (substr($img_url,0,1)=="/") {
            $ret = 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER["SERVER_PORT"].$img_url;
        }
        else {
            $path = dirname($_SERVER['PHP_SELF']);
            $ret = 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER["SERVER_PORT"].$path."/../".$img_url;
        }
    
        }
        else {
        $ret = $img_url;
        }
    
        return  $ret;
    }


    function fixcdata($content) {
        $content = str_replace("\x1a","",$content);   // remove some strange \x1a [SUB] char from datafile
        return str_replace("]]>","]]]]><![CDATA[>",$content);
      }

    function printTestCases($fp,$pid,$OJ_DATA) {
        $ret = "";
        //$pdir = opendir("$OJ_DATA/$pid/");
        $files = scandir("$OJ_DATA$pid/"); //sorting file names by ascending order with default scandir function
    
        //while ($file=readdir($pdir)) {
        foreach ($files as $file) {
        $pinfo = pathinfo($file);
        
        if (isset($pinfo['extension']) && $pinfo['extension']=="in" && $pinfo['basename']!="sample.in") {
            $ret = basename($pinfo['basename'], ".".$pinfo['extension']);
    
            $outfile = "$OJ_DATA$pid/".$ret.".out";
            $infile = "$OJ_DATA$pid/".$ret.".in";
    
            if (file_exists($infile)) {
                fputs($fp,"<test_input name=\"".$ret."\"><![CDATA[".self::fixcdata(file_get_contents($infile))."]]></test_input>\n");
            }
    
            if (file_exists($outfile)) {
                fputs($fp,"<test_output name=\"".$ret."\"><![CDATA[".self::fixcdata(file_get_contents($outfile))."]]></test_output>\n");
            }
            //break;
        }
        }
        
        //closedir($pdir);
        return $ret;
    
      }

        
    function getImages($content) {
        preg_match_all("<[iI][mM][gG][^<>]+[sS][rR][cC]=\"?([^ \"\>]+)/?>",$content,$images);
        return $images;
    }
    

    function image_base64_encode($img_url) {
        $img_url = self::fixurl($img_url);
      
        if (substr($img_url,0,4)!="http")
          return false;
      
        $handle = @fopen($img_url, "rb");
      
        if ($handle) {
          $contents = stream_get_contents($handle);
          $encoded_img = base64_encode($contents);
          fclose($handle);
          return $encoded_img;
        }
        else
          return false;
      }
      


    function fixImageURL($fp,&$html,&$did) {
        $images = self::getImages($html);
        $imgs = array_unique($images[1]);
    
        foreach ($imgs as $img) {
        if(substr($img,0,4)=="data") continue;                      // skip image from paste clips
        $html = str_replace($img,self::fixurl($img),$html); 
        //print_r($did);
    
        if (!in_array($img,$did)) {
            $base64 = self::image_base64_encode($img);
            if ($base64) {
                fputs($fp,"<img><src><![CDATA[");
                fputs($fp,self::fixurl($img));
                fputs($fp,"]]></src><base64><![CDATA[");
                fputs($fp,$base64);
                fputs($fp,"]]></base64></img>");   
            }
            array_push($did,$img);
        }
        }     
    }
        
    public function exportxml($keys,$export_file)
    {
        $fp = @fopen($export_file, "w");
        if ($fp) {
            set_time_limit(0);
            ob_end_clean();
            fputs($fp,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <fps version=\"1.5\" url=\"https://github.com/zhblue/freeproblemset/\">
            <generator name=\"HUSTOJ\" url=\"https://github.com/zhblue/hustoj/\" />\n");

            foreach ($keys as $key) {
                fputs($fp,"<item>");
                $problem = Problem::findOne($key);

                $did = array();
                self::fixImageURL($fp,$problem->description,$did);
                self::fixImageURL($fp,$problem->input,$did);
                self::fixImageURL($fp,$problem->output,$did);
                self::fixImageURL($fp,$problem->hint,$did);

                $sample_input = unserialize($problem->sample_input);
                $sample_output = unserialize($problem->sample_output);


                fputs($fp,"<title><![CDATA[$problem->title]]></title>\n");
                fputs($fp,"<time_limit unit=\"s\"><![CDATA[$problem->time_limit]]></time_limit>\n");
                fputs($fp,"<memory_limit unit=\"mb\"><![CDATA[$problem->memory_limit]]></memory_limit>\n");

                fputs($fp,"<description><![CDATA[$problem->description]]></description>\n");
                fputs($fp,"<input><![CDATA[$problem->input]]></input>\n");
                fputs($fp,"<output><![CDATA[$problem->output]]></output>\n");
                self::printTestCases($fp,$problem->id,Yii::$app->params['judgeProblemDataPath']);
                fputs($fp,"<hint><![CDATA[$problem->hint]]></hint>\n");
                fputs($fp,"<source><![CDATA[$problem->source]]></source>\n");
                $solution = str_replace("<br>","\n",html_entity_decode($problem->solution));
                fputs($fp,"<solution><![CDATA[$solution]]></solution>\n");
                fputs($fp,"<tags><![CDATA[$problem->tags]]></tags>\n");

                fputs($fp,"<sample_input><![CDATA[$sample_input[0]]]></sample_input>\n");
                fputs($fp,"<sample_output><![CDATA[$sample_output[0]]]></sample_output>\n");
                fputs($fp,"<sample_input1><![CDATA[$sample_input[1]]]></sample_input1>\n");
                fputs($fp,"<sample_output1><![CDATA[$sample_output[1]]]></sample_output1>\n");
                fputs($fp,"<sample_input2><![CDATA[$sample_input[2]]]></sample_input2>\n");
                fputs($fp,"<sample_output2><![CDATA[$sample_output[2]]]></sample_output2>\n");
                fputs($fp,"</item>");
            }
            fputs($fp,"</fps>");
            fclose($fp);
            return true;

        } else {
            echo "Error while opening ".$export_file;
            return false;
        };
    }

}
