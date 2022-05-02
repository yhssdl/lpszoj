<?php

namespace app\modules\polygon\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\polygon\models\Problem;
use app\models\User;
use app\modules\polygon\models\ProblemSearch;

/**
 * Default controller for the `polygon` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->redirect("/polygon/problem/index");
    }
}
