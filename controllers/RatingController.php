<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\User;
use yii\db\Query;
use yii\data\Pagination;

class RatingController extends BaseController
{
    public function actionIndex()
    {
        $query = User::find()->orderBy('rating DESC');
        $top3users = $query->limit(3)->all();
        $defaultPageSize = 50;
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'defaultPageSize' => $defaultPageSize
        ]);
        $users = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'top3users' => $top3users,
            'users' => $users,
            'pages' => $pages,
            'currentPage' => $pages->page,
            'defaultPageSize' => $defaultPageSize
        ]);
    }

    public function actionProblem()
    {
        $query = (new Query())->select('u.id, u.nickname, u.rating, s.solved')
            ->from('{{%user}} AS u')
            ->innerJoin(' (SELECT  COUNT(DISTINCT `solution`.problem_id) AS solved, `solution`.created_by FROM solution LEFT JOIN `contest` `c` ON `c`.`id`=`solution`.`contest_id` WHERE (`solution`.`contest_id` IS NULL OR (`solution`.`contest_id` IS NOT NULL AND NOW()>`c`.`end_time`)) AND result=4  GROUP BY `solution`.created_by) AS s',
            'u.id=s.created_by')
            ->orderBy('solved DESC, id');
        $top3users = $query->limit(3)->all();
        $defaultPageSize = 50;
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'defaultPageSize' => $defaultPageSize
        ]);
        $users = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('problem', [
            'top3users' => $top3users,
            'users' => $users,
            'pages' => $pages,
            'currentPage' => $pages->page,
            'defaultPageSize' => $defaultPageSize
        ]);
    }
}
