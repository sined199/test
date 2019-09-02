<?php

namespace app\controllers;

use app\models\Test;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{

    private  $colors = [
        'red' => "Красный",
        'yellow' => "Желтый",
        'black' => "Черный",
    ];
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Test();

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post())) {
                if($model->validate()){
                    $model->save(false);
                    if(!empty(Yii::$app->session->get("lastID"))){
                        $lastID = Yii::$app->session->get("lastID");
                        $_items = Test::find()
                            ->where(['>','id',$lastID])
                            ->all();
                    }
                    else{
                        $_items = Test::find()->all();
                    }
                    if(!empty($_items))  Yii::$app->session->set("lastID",$_items[count($_items)-1]->id);

                    $items = [];
                    $colors = $this->colors;

                    foreach($_items as $item){
                        $items[] = array(
                            'id' => $item->id,
                            'auto' => $item->auto,
                            'model' => $item->model,
                            'number' => $item->number,
                            'color' => $colors[$item->color],
                            'parking' => ($item->parking) ? "Да" : "Нет",
                            'comment' => $item->comment,
                        );
                    }

                    return [
                        'data' => [
                            'success' => true,
                            'model' => $model,
                            'items' => $items
                        ],
                        'code' => 0,
                    ];
                } else {
                    return [
                        'data' => [
                            'success' => false,
                            'model' => $model,
                            'error' => $model->errors,
                            'post' => Yii::$app->request->post(),
                            'message' => 'An error occured.',
                        ],
                        'code' => 1, // Some semantic codes that you know them for yourself
                    ];
                }
            }
        }

        $_items = Test::find()->all();
        if(!empty($_items))  Yii::$app->session->set("lastID",$_items[count($_items)-1]->id);
        else Yii::$app->session->remove("lastID");

        $items = [];
        $colors = $this->colors;

        foreach($_items as $item){
            $items[] = array(
                'id' => $item->id,
                'auto' => $item->auto,
                'model' => $item->model,
                'number' => $item->number,
                'color' => $colors[$item->color],
                'parking' => ($item->parking) ? "Да" : "Нет",
                'comment' => $item->comment,
            );
        }
        return $this->render('index', compact('model', 'items','colors'));
    }

    public function actionCars(){
        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if(!empty(Yii::$app->session->get("lastID"))){
                $lastID = Yii::$app->session->get("lastID");
                $_items = Test::find()
                    ->where(['>','id',$lastID])
                    ->all();
            }
            else{
                $_items = Test::find()->all();
            }

            $items = [];
            foreach($_items as $item){
                $items[] = [
                    'id' => $item->id,
                    'auto' => $item->auto,
                    'model' => $item->model,
                    'number' => $item->number,
                    'color' => $this->colors[$item->color],
                    'parking' => ($item->parking) ? "Да" : "Нет",
                    'comment' => $item->comment
                ];
            }

            return [
                'items' => $items,
                'code' => 0, // Some semantic codes that you know them for yourself
            ];

        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
