<?php

class PostController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                //	'actions'=>array('index','view'),
                'users' => array('*'),
            ),
//			array('allow', // allow authenticated user to perform 'create' and 'update' actions
//				'actions'=>array('create','update'),
//				'users'=>array('@'),
//			),
//			array('allow', // allow admin user to perform 'admin' and 'delete' actions
//				'actions'=>array('admin','delete'),
//				'users'=>array('@'),
//			),
//			array('deny',  // deny all users
//				'users'=>array('*'),
//			),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        /* $this->render('view',array(
          'model'=>$this->loadModel($id),
          )); */
        $dataProvider = new CActiveDataProvider('comment', array(
            'criteria' => array(
                'condition' => 'post_id =' . $id,
            ),
        ));
        $model = $this->loadModel($id);
        $comment = new Comment;

        if (isset($_POST['Comment'])) {
            $comment->attributes = $_POST['Comment'];
            $comment->post_id=  $this->loadModel($id)->id;
            
              if(Yii::app()->user->isGuest){
                $comment->account_id=null;
            }else{
                $comment->account_id=Yii::app()->user->id;
            }
            if ($comment->save())
                $this->redirect(array('post/view/'.$this->loadModel($id)->id));
        }


        $comment->post_id = $id;
        $this->render('view', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
            'comment' => $comment,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
         if ($id = Yii::app()->user->isGuest) {
            throw new CHttpException(
                    403,
                    'Anda tidak memiliki akses untuk aksi ini.'
            );
        }
        $model = new Post;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Post'])) {
            $model->attributes = $_POST['Post'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Post'])) {
            $model->attributes = $_POST['Post'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
       if ($id = Yii::app()->user->isGuest) {
            throw new CHttpException(
                    403,
                    'Anda tidak memiliki akses untuk aksi ini.'
            );
        }
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Post');
        $model = new Post;
        if (isset($_POST['Post'])) {
            $model->attributes = $_POST['Post'];
            $idUser = Account::model()->findByAttributes(array('username' => Yii::app()->user->id));
            $model->account_id = $idUser->id;
            if ($model->save())
                $this->redirect(array('index'));
        }

        $this->render('index', array(
            'dataProvider' => $dataProvider, 'model' => $model,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Post('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Post']))
            $model->attributes = $_GET['Post'];
        
        $post=new Post;
        
        if (isset($_POST['Post'])) {
            $post->attributes = $_POST['Post'];
            
          $post->account_id=Yii::app()->user->id;
            if ($post->save())
                $this->redirect(array('admin'));
        }

        $this->render('admin', array(
            'model' => $model,
            'post'=>$post,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Post the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Post::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Post $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'post-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
