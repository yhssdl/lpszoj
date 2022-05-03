<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contest;
use yii\db\Expression;

/**
 * ContestSearch represents the model behind the search form of `app\models\Contest`.
 */
class ContestSearch extends Contest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status','type','status'], 'integer'],
            [['title'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Contest::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->FilterWhere([
            'type' => $this->type,
        ]);

       

        if($this->status!=null && $this->status==0){
            $where = new Expression('now() < start_time');
            $query->andWhere($where);
        }else if($this->status==1){
            $where = new Expression('now() >= start_time and now() < end_time');
            $query->andWhere($where);
           
        }else if($this->status==2){
            $where = new Expression('now() > end_time');
            $query->andWhere($where);
        }


        $query->andFilterWhere(['like', 'title', $this->title])
            ->andwhere([
                '<>', 'status', Contest::STATUS_HIDDEN
            ])->andWhere([
                'group_id' => 0
            ])->orderBy(['start_time' => SORT_DESC, 'end_time' => SORT_ASC, 'id' => SORT_DESC]);

            
       // exit($query->createCommand()->getRawSql());
        return $dataProvider;
    }

    
    public static function getTypeList()
    {
        $arr = [
            '' => Yii::t('app', 'Please select'),
            '1' => Yii::t('app', 'Single Ranked'),
            '2' => Yii::t('app', 'ACM/ICPC'),
            '3' => Yii::t('app', 'Homework'),
            '4' => Yii::t('app', 'OI'),  
            '5' => Yii::t('app', 'IOI'),
        ];
        return $arr;
    }

    public static function getRunStatusList()
    {
        $arr = [
            '' => Yii::t('app', 'Please select'),
            '0' => Yii::t('app', 'Not started yet'),
            '1' => Yii::t('app', 'Running'),
            '2' => Yii::t('app', 'Ended'),
        ];
        return $arr;
    }

}
