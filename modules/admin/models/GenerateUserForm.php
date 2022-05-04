<?php

namespace app\modules\admin\models;

use app\models\ContestUser;
use Yii;
use yii\base\Model;
use app\models\User;

/**
 * UploadForm is the model behind the upload form.
 */
class GenerateUserForm extends Model
{
    public $prefix;
    public $team_number;
    public $contest_id;
    public $names;
    public $language=1;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['prefix', 'names'], 'string'],
            [['team_number', 'contest_id', 'language'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'prefix' => Yii::t('app', 'Prefix'),
            'team_number' => Yii::t('app', 'Number'),
            'names' => Yii::t('app', '')
        ];
    }

    public function generatePassword($length = 8)
    {
        $chars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
            'E', 'F', 'G', 'H', 'J', 'K', 'L','M', 'N',
            'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
            '2', '3', '4', '5', '6', '7', '8', '9'];
        $keys = array_rand($chars, $length);
        $password = '';
        for($i = 0; $i < $length; $i++) {
            $password .= $chars[$keys[$i]];
        }
        return $password;
    }

    public function save()
    {
        $pieces = explode("\n", trim($this->names));

        User::deleteAll("username LIKE '" . $this->prefix . "%'");
        ContestUser::deleteAll(['contest_id' => $this->contest_id]);

        set_time_limit(0);
        ob_end_clean();
        echo "生成帐号需要一定时间，在此期间请勿刷新或关闭该页面<br>";
        for ($i = 1; $i <= $this->team_number; ++$i) {

            if(isset($pieces[$i - 1]) && !empty($pieces[$i - 1]))
                $nick = $pieces[$i - 1];
            else
                $nick = $this->prefix . $i;

            $password = $this->generatePassword();
            $user = new User();
            $user->username = $this->prefix . $i;
            $user->nickname = $nick;
            $user->language = Yii::$app->setting->get('defaultLanguage');
            $user->email = $this->prefix . $i . '@jnoj.org';
            $user->role = User::ROLE_PLAYER;
            $user->is_verify_email = User::VERIFY_EMAIL_YES;
            $user->status = User::STATUS_ACTIVE;
            $user->setPassword($password);
            $user->generateAuthKey();
            $user->save(false);

            Yii::$app->db->createCommand()->insert('{{%contest_user}}', [
                'user_id' => $user->id,
                'contest_id' => $this->contest_id,
                'user_password' => $password
            ])->execute();
            echo "帐号 {$nick} 创建成功——帐号数{$i}/{$this->team_number}<br>";
            flush();
        }
        echo "帐号生成完毕";
        exit('<script>location.replace(location.href);</script>');
    }

    public function generateUsers()
    {
        $this->names = str_replace("\t"," ",$this->names);
        $this->names = str_replace("\r","",$this->names);
        $pieces = explode("\n", trim($this->names));
        $count = count($pieces);
        $savePassword = "";
        $saveAuthKey = "";
        $savePassHansh="";
        set_time_limit(0);
        ob_end_clean();
        echo "生成帐号需要一定时间，在此期间请勿刷新或关闭该页面<br>";
        $cnt = count($pieces);
        for ($i = 1; $i <= $cnt; ++$i) {
            if (empty($pieces[$i - 1]))
                continue;
            $u = explode(' ', trim($pieces[$i - 1]));
            $user = new User();
            $username = $u[0];       
            if(count($u)==3) {//3列含昵称的模式
                $user->nickname = $u[1];
                $password = $u[2];
            }else{
                $user->nickname = $username;
                $password = $u[1];
            }
            $user->username = $username;
            $user->email = $username . '@jnoj.org';
            $user->role = User::ROLE_USER;
            $user->language = $this->language;
            if($savePassword==$password){
                $user->auth_key = $saveAuthKey;
                $user->password_hash = $savePassHansh;
            }else {
                $user->setPassword($password);
                $user->generateAuthKey();
                $saveAuthKey = $user->auth_key;
                $savePassHansh = $user->password_hash;
                $savePassword = $password;
            }
            if ($user->save()) {
                echo "帐号数{$i}/{$count}：帐号 {$username} 创建成功";
                Yii::$app->db->createCommand()->insert('{{%user_profile}}', [
                'user_id' => $user->id,
                ])->execute();
            } else {
                $err = $user->getErrors();
                echo "帐号数{$i}/{$count}：帐号 {$username} 创建失败！！！！！ 失败原因：";
                print_r($err);
            }
            echo '<br>';
            flush();
        }
        echo "帐号生成完毕";
        die; //exit('<script>location.replace(location.href);</script>');
    }
}
