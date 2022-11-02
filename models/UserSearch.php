<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * SolutionSearch represents the model behind the search form of `app\models\Solution`.
 */
class UserSearch extends User
{
    public $username;
    public $pagesize;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','pagesize'], 'integer'],
            [['username', 'email', 'nickname'], 'string'],
            ['role', 'in', 'range' => [self::STATUS_DISABLE,self::ROLE_PLAYER, self::ROLE_USER, self::ROLE_VIP, self::ROLE_ADMIN]]
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        $query = User::find();

        $this->load($params);

        if($this->pagesize<50) $this->pagesize =50;
        if($this->pagesize>500) $this->pagesize =500;
 
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => $this->pagesize,
            ],
        ]);



        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if($this->role == self::STATUS_DISABLE) {
            $status = self::STATUS_DISABLE;
            $this ->role = null;
        }else {
            $status = null;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $status,
            'role' => $this->role,
        ]);

        $query->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
