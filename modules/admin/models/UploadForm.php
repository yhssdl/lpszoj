<?php

namespace app\modules\admin\models;

use Yii;
use app\models\Problem;
use yii\base\Model;
use yii\db\Query;
use yii\web\UploadedFile;
use yii\base\ErrorException;

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
                        if($fileSize==0) continue;
                        $fp = fopen($tempFile,"w");
                        while($fileSize>0){
                            $fileContent = zip_entry_read($dirResource,$fileSize>1024?1024:$fileSize);
                            fwrite($fp,$fileContent);
                            $fileSize -= 1024;
                        }
                        fclose($fp);
                        $ret = $ret . self::importFPS($fileName,$tempFile);
                    }
                    zip_entry_close($dirResource);
                }
                zip_close($resource);
            } else {
                $ret = $ret . self::importFPS("XML题库文件",$tempFile);
            }
            return $ret;
        } else {
            return $ret;
        }
    }

    public static function getImageValue($Node, $TagName) {
        $value=mb_ereg_replace("<div[a-z -=\"]*>","",$Node->$TagName);
        $value=mb_ereg_replace("</div>","",$value);
        return $value;
      }  
      
    public static function image_save_file($filepath ,$base64_encoded_img) {
        $dirpath=dirname($filepath);
        if (!file_exists($dirpath)) {
             mkdir($dirpath,0755,true);
        }
          $fp = fopen($filepath ,"wb");
          fwrite($fp,base64_decode($base64_encoded_img));
          fclose($fp);
    }   

    public static function importImages($images){
        $did = array();
        foreach ($images as $img) {
            $src = self::getImageValue($img,"src");
            if (!in_array($src,$did)) {
            $base64 = self::getImageValue($img,"base64");
            $ext = pathinfo($src);
            $ext = strtolower($ext['extension']);
            if (!stristr(",jpeg,jpg,svg,png,gif,bmp",$ext)) {
                continue ;
            }
            $paths = parse_url($src);
            if(!$paths) continue;
            $path = strtolower($paths['path']);
            self::image_save_file("../web".$path,$base64);
            array_push($did,$src);
            }
        }
    }


    public static function importFPS($realName,$tempFile)
    {
        try{
            $xmlDoc = simplexml_load_file($tempFile, 'SimpleXMLElement', LIBXML_PARSEHUGE);
        }catch(ErrorException  $e){
            $msg = "<font color=blue>解析错误：</font><font color=red>".$realName."<br>".$e->getMessage()."</font><br>";
            return $msg;
        }
        if(!$xmlDoc) return "<font color=blue>解析错误：</font><font color=red>".$realName."</font><br>"; 
        $searchNodes = $xmlDoc->xpath("/fps/item");
        set_time_limit(0);
        ob_end_clean();
        $msg = "<font color=blue>正在解析：".$realName."</font><br>";
        foreach ($searchNodes as $searchNode) {
            $title = (string)$searchNode->title;
            if (!self::hasProblem($title)) {
                $spjCode = self::getValue($searchNode, 'spj');
                $spj = trim($spjCode) ? 1 : 0;
                $time_limit = intval(self::getValue($searchNode, 'time_limit'));
                $unit = self::getAttribute($searchNode,'time_limit','unit');
                if ($unit == 'ms') $time_limit /= 1000;
                if($time_limit<1) $time_limit = 1;
                               
                
                $memory_limit =  intval(self::getValue($searchNode, 'memory_limit'));
                $unit = self::getAttribute($searchNode,'memory_limit','unit');
                if ($unit == 'kb')
                    $memory_limit  /= 1024;
                $newProblem = new Problem();
                $newProblem->title = $title;
                self::importImages($searchNode->children()->img);
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

    function printTestCases($OJ_DATA_PID) {
   
        if(!file_exists($OJ_DATA_PID)) return;
        $files = scandir($OJ_DATA_PID); //sorting file names by ascending order with default scandir function
        foreach ($files as $file) {
            $pinfo = pathinfo($file);
            
            if (isset($pinfo['extension']) && $pinfo['extension']=="in" && $pinfo['basename']!="sample.in") {
                $ret = basename($pinfo['basename'], ".".$pinfo['extension']);
                $outfile = $OJ_DATA_PID.$ret.".out";
                $infile = $OJ_DATA_PID.$ret.".in";
                if (file_exists($infile)) {
                    echo "<test_input name=\"".$ret."\"><![CDATA[".self::fixcdata(file_get_contents($infile))."]]></test_input>\n";
                }
                if (file_exists($outfile)) {
                    echo "<test_output name=\"".$ret."\"><![CDATA[".self::fixcdata(file_get_contents($outfile))."]]></test_output>\n";
                }
            }
        }
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
      


    function fixImageURL(&$html,&$did) {
        $images = self::getImages($html);
        $imgs = array_unique($images[1]);
    
        foreach ($imgs as $img) {
        if(substr($img,0,4)=="data") continue;                      // skip image from paste clips
        $html = str_replace($img,self::fixurl($img),$html); 
        //print_r($did);
    
        if (!in_array($img,$did)) {
            $base64 = self::image_base64_encode($img);
            if ($base64) {
                echo "<img><src><![CDATA[";
                echo self::fixurl($img);
                echo "]]></src><base64><![CDATA[";
                echo $base64;
                echo "]]></base64></img>";   
            }
            array_push($did,$img);
        }
        }     
    }
        
    public function exportFpsXml($keys,$basename,$ext)
    {
        header("Content-Type: application/octet-stream");
        if (preg_match("/MSIE/",$_SERVER['HTTP_USER_AGENT'])){
            header('Content-Disposition:attachment;filename="'.$basename.'."'.$ext.'');
        }elseif(preg_match("/Firefox/",$_SERVER['HTTP_USER_AGENT'])){
                header('Content-Disposition:attachment;filename*="'.$basename.'.'.$ext.'"');
        }else{
                header('Content-Disposition:attachment;filename="'.$basename.'.'.$ext.'"');
        }
        set_time_limit(0);
        ob_end_clean();
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <fps version=\"1.5\" url=\"https://github.com/zhblue/freeproblemset/\">
        <generator name=\"HUSTOJ\" url=\"https://github.com/zhblue/hustoj/\" />\n";
        foreach ($keys as $key) {
            echo "<item>";
            $problem = Problem::findOne($key);
            $did = array();
            self::fixImageURL($problem->description,$did);
            self::fixImageURL($problem->input,$did);
            self::fixImageURL($problem->output,$did);
            self::fixImageURL($problem->hint,$did);
            $sample_input = unserialize($problem->sample_input);
            $sample_output = unserialize($problem->sample_output);
            echo "<title><![CDATA[$problem->title]]></title>\n";
            echo "<time_limit unit=\"s\"><![CDATA[$problem->time_limit]]></time_limit>\n";
            echo "<memory_limit unit=\"mb\"><![CDATA[$problem->memory_limit]]></memory_limit>\n";
            echo "<description><![CDATA[$problem->description]]></description>\n";
            echo "<input><![CDATA[$problem->input]]></input>\n";
            echo "<output><![CDATA[$problem->output]]></output>\n";
            self::printTestCases(Yii::$app->params['judgeProblemDataPath'].$problem->id."/");
            echo "<hint><![CDATA[$problem->hint]]></hint>\n";
            echo "<source><![CDATA[$problem->source]]></source>\n";
            if($problem->solution) {
                $solution = str_replace("<br>","\n",html_entity_decode($problem->solution));
                echo "<solution><![CDATA[$solution]]></solution>\n";
            }
            echo "<tags><![CDATA[$problem->tags]]></tags>\n";
            if($sample_input){
                echo "<sample_input><![CDATA[$sample_input[0]]]></sample_input>\n";
                echo "<sample_input1><![CDATA[$sample_input[1]]]></sample_input1>\n";
                echo "<sample_input2><![CDATA[$sample_input[2]]]></sample_input2>\n";
            }
            if($sample_output){
                echo "<sample_output><![CDATA[$sample_output[0]]]></sample_output>\n";
                echo "<sample_output1><![CDATA[$sample_output[1]]]></sample_output1>\n";
                echo "<sample_output2><![CDATA[$sample_output[2]]]></sample_output2>\n";
            }
            echo "</item>";
        }
        echo "</fps>";
    }
}
