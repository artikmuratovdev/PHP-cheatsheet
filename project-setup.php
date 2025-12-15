<?php
/**
 * Savitar Blog - Complete Yii2 Project Setup Script
 * Bu script Yii2 basic template'dan yangi blog project yaratadi va BARCHA fayllarni qo'shadi
 * 
 * Usage: php setup-savitar-blog-complete.php [options]
 * 
 * Options:
 *   --project-name=NAME    Project nomi (default: savitar-blog)
 *   --project-path=PATH    Project joylashuvi (default: .)
 *   --db-name=NAME         Database nomi (default: savitar_blog)
 *   --db-user=USER         Database user (default: root)
 *   --db-password=PASS     Database password (default: empty)
 */

// Parametrlarni o'qish
$options = getopt('', [
    'project-name:',
    'project-path:',
    'db-name:',
    'db-user:',
    'db-password:'
]);

$projectName = $options['project-name'] ?? 'savitar-blog';
$projectPath = $options['project-path'] ?? '.';
$dbName = $options['db-name'] ?? 'savitar_blog';
$dbUser = $options['db-user'] ?? 'root';
$dbPassword = $options['db-passaword'] ?? '';

echo "========================================\n";
echo "Savitar Blog - Complete Yii2 Setup\n";
echo "========================================\n\n";

// Database config'ni yangilash
echo "[3/15] Updating database configuration...\n";
$dbConfig = <<<PHP
<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname={$dbName}',
    'username' => '{$dbUser}',
    'password' => '{$dbPassword}',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
PHP;
file_put_contents('config/db.php', $dbConfig);
echo "✓ Database config updated!\n\n";

// Migrations directory yaratish
echo "[4/15] Creating directories...\n";
@mkdir('migrations', 0755, true);
@mkdir('web/images', 0755, true);
@mkdir('web/uploads', 0755, true);
@mkdir('views/post', 0755, true);
echo "✓ Directories created!\n\n";

// Schema SQL yaratish
echo "[5/15] Creating database schema...\n";
$schemaSQL = <<<SQL
-- Yii2 Blog Application Database Schema
-- Database: {$dbName}

-- Drop tables if they exist (in reverse order due to foreign keys)
DROP TABLE IF EXISTS `post`;
DROP TABLE IF EXISTS `user`;

-- Create user table
CREATE TABLE `user` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `auth_key` VARCHAR(32) DEFAULT NULL,
    `access_token` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create post table
CREATE TABLE `post` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `views` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
file_put_contents('migrations/schema.sql', $schemaSQL);
echo "✓ Schema SQL created!\n\n";

// Dummy data SQL yaratish
echo "[6/15] Creating dummy data SQL...\n";
$dummyDataSQL = <<<'SQL'
-- Dummy data for testing
-- Password for all users: password
INSERT INTO `user` (`username`, `password_hash`, `auth_key`, `created_at`) VALUES
('naruto', '$2y$10$2Xagz5d9GmySTMSUMrZoW.TI/EL5S7w8Ilrg68meOiWjnNBI23TMG', 'naruto_auth_key', NOW()),
('sasuke', '$2y$10$2Xagz5d9GmySTMSUMrZoW.TI/EL5S7w8Ilrg68meOiWjnNBI23TMG', 'sasuke_auth_key', NOW()),
('sakura', '$2y$10$2Xagz5d9GmySTMSUMrZoW.TI/EL5S7w8Ilrg68meOiWjnNBI23TMG', 'sakura_auth_key', NOW()),
('kakashi', '$2y$10$2Xagz5d9GmySTMSUMrZoW.TI/EL5S7w8Ilrg68meOiWjnNBI23TMG', 'kakashi_auth_key', NOW());

INSERT INTO `post` (`user_id`, `title`, `content`, `image`, `views`, `created_at`) VALUES
(1, 'Uzumaki Naruto - Konohaning Yettiinchi Hokage', 'Uzumaki Naruto - Konoha qishlog\'ining eng kuchli shinobisi va Yettiinchi Hokage. U Nine-Tails jinchuriki bo\'lib, kuchli rasengan va shadow clone jutsularini ishlatadi. Naruto o\'zining qat\'iyati va do\'stlari uchun kurashish qobiliyati bilan mashhur. U doimo "Believe it!" degan so\'z bilan motivatsiya beradi va hech qachon taslim bo\'lmaydi.', 'https://i.pinimg.com/736x/f0/46/f1/f046f16506c98144475c3708ea99ba72.jpg', 0, NOW() - INTERVAL 5 DAY),
(1, 'Rasengan - Naruto\'ning Asosiy Jutsusi', 'Rasengan - Naruto\'ning eng kuchli jutsularidan biri. Bu jutsu to\'rtinchi Hokage Minato Namikaze tomonidan yaratilgan va Naruto tomonidan mukammallashtirilgan. Rasengan chakra spiral shaklida aylanadi va dushmanga kuchli zarba beradi. Naruto bu jutsuni o\'q otish yoki qo\'l bilan boshqarish orqali turli xil variantlarda ishlatadi.', 'default.jpg', 0, NOW() - INTERVAL 3 DAY),
(1, 'Nine-Tails (Kurama) - Kuchli Biju', 'Kurama yoki Nine-Tails - eng kuchli Tailed Beast. Naruto tug\'ilganidan keyin Kurama uning ichiga muhrlangan edi. Dastlab, Naruto va Kurama o\'rtasida munosabatlar yomon edi, lekin vaqt o\'tishi bilan ular do\'st bo\'lishdi. Kurama Naruto\'ga katta kuch beradi va ular birgalikda kuchliroq bo\'lishadi.', 'https://i.namu.wiki/i/4DPCwHiDu1KN0-LVq0jz870qdvOaEUeQI_mhlNCldBbnzXuSkn7Cfp2cR_lcSGejtzGjhdkq1p36coV9DMygVQ.webp', 0, NOW() - INTERVAL 1 DAY),
(2, 'Uchiha Sasuke - Sharingan\'ning Egasi', 'Uchiha Sasuke - Konoha\'ning eng kuchli uchiha qabilasining oxirgi a\'zosi. U Sharingan va keyinchalik Mangekyo Sharingan ko\'zlariga ega. Sasuke o\'zining akasi Itachi\'dan qasos olish uchun yo\'lga chiqdi va Orochimaru bilan shartnoma tuzdi. Keyinchalik u Naruto bilan do\'st bo\'ldi va Konohaga qaytdi.', 'https://i.pinimg.com/736x/35/df/1c/35df1cef6b596381b6bdcdd79b45bb0c.jpg', 0, NOW() - INTERVAL 4 DAY),
(2, 'Chidori - Sasuke\'ning Signature Jutsusi', 'Chidori - Sasuke\'ning eng mashhur jutsusi. Bu jutsu Kakashi Hatake tomonidan yaratilgan va Sasuke tomonidan o\'rgatilgan. Chidori elektr chakradan foydalanadi va dushmanga kuchli zarba beradi. Sasuke bu jutsuni turli xil variantlarda ishlatadi, jumladan Chidori Stream va Chidori Sharp Spear.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTLzzcCYWiVyazuTQXvFy_rFW7e5dt6VMrKg&s', 0, NOW() - INTERVAL 2 DAY),
(2, 'Mangekyo Sharingan - Kuchli Ko\'z Teknikasi', 'Mangekyo Sharingan - Uchiha qabilasining eng kuchli ko\'z teknikasi. Bu ko\'zlar faqat qandaydir kuchli hissiyotlar natijasida ochiladi. Sasuke o\'zining akasi Itachi\'dan ko\'z olgach, Mangekyo Sharingan\'ga ega bo\'ldi. Bu ko\'zlar Amaterasu, Susano\'o va boshqa kuchli jutsularni ishlatishga imkon beradi.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSiG-ufqbeWJS5GNk_aqthGbj2H7KC8NsYgIA&s', 0, NOW() - INTERVAL 1 DAY);
SQL;
file_put_contents('migrations/dummy-data.sql', $dummyDataSQL);
echo "✓ Dummy data SQL created!\n\n";

// Models yaratish
echo "[7/15] Creating models...\n";

// User.php
$userModel = <<<'PHP'
<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string|null $auth_key
 * @property string|null $access_token
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['password_hash'], 'required', 'on' => ['insert', 'default']],
            [['username'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['password_hash', 'access_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (!$this->hasAttribute('password_hash') || empty($this->password_hash)) {
            return false;
        }
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Before save event
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
            }
            return true;
        }
        return false;
    }

    /**
     * Get posts relation
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['user_id' => 'id']);
    }
}
PHP;
file_put_contents('models/User.php', $userModel);

// Post.php
$postModel = <<<'PHP'
<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property string|null $image
 * @property int|null $views
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    
    /**
     * @var string Image URL (alternative to file upload)
     */
    public $imageUrl;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['views'], 'default', 'value' => 0],
            [['views'], 'integer', 'min' => 0],
            [['title', 'content'], 'required'],
            [['user_id', 'views'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'image'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 5 * 1024 * 1024], // 5MB max
            [['imageUrl'], 'url', 'skipOnEmpty' => true, 'defaultScheme' => 'http'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'content' => 'Content',
            'image' => 'Image',
            'imageFile' => 'Image File',
            'imageUrl' => 'Image URL',
            'views' => 'Views',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get user relation
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Upload image file
     * @return bool
     */
    public function upload()
    {
        if ($this->imageFile) {
            $uploadPath = Yii::getAlias('@webroot/uploads');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $fileName = uniqid() . '_' . time() . '.' . $this->imageFile->extension;
            $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
            
            if ($this->imageFile->saveAs($filePath)) {
                // Delete old image if exists and not default
                if (!empty($this->image) && $this->image !== 'default.jpg' && strpos($this->image, 'http') !== 0) {
                    $oldFilePath = $uploadPath . DIRECTORY_SEPARATOR . $this->image;
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
                $this->image = $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * Process image input (file or URL)
     * @return bool
     */
    public function processImage()
    {
        // Priority: File upload > URL > default
        if ($this->imageFile) {
            // File upload has priority
            return $this->upload();
        } elseif (!empty($this->imageUrl)) {
            // Use URL if provided
            $this->image = $this->imageUrl;
            return true;
        } elseif (empty($this->image)) {
            // Set default if nothing provided
            $this->image = 'default.jpg';
            return true;
        }
        return true; // Keep existing image
    }

    /**
     * Before save event
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Process image (file or URL)
            $this->processImage();
            return true;
        }
        return false;
    }
}
PHP;
file_put_contents('models/Post.php', $postModel);

// SignupForm.php
$signupForm = <<<'PHP'
<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SignupForm is the model behind the signup form.
 */
class SignupForm extends Model
{
    public $username;
    public $password;
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password', 'password_repeat'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'password_repeat' => 'Repeat Password',
        ];
    }

    /**
     * Signs up a user using the provided username and password.
     * @return bool whether the user is signed up successfully
     */
    public function signup()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if ($user->save()) {
            return Yii::$app->user->login($user, 3600 * 24 * 30); // Auto-login for 30 days
        }

        return false;
    }
}
PHP;
file_put_contents('models/SignupForm.php', $signupForm);

// PostSearch.php
$postSearch = <<<'PHP'
<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Post;

/**
 * PostSearch represents the model behind the search form of `app\models\Post`.
 */
class PostSearch extends Post
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'views'], 'integer'],
            [['title', 'content', 'image', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Post::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'views' => $this->views,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }
}
PHP;
file_put_contents('models/PostSearch.php', $postSearch);
echo "✓ Models created!\n\n";

// Controllers yaratish
echo "[8/15] Creating controllers...\n";

// SiteController.php
$siteController = <<<'PHP'
<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\ContactForm;
use app\models\Post;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
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
        $posts = Post::find()->with('user')->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('index', ['posts' => $posts]);
    }

    /**
     * Signup action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->session->setFlash('success', 'Thank you for registration. You are now logged in.');
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
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
PHP;
file_put_contents('controllers/SiteController.php', $siteController);

// PostController.php
$postController = <<<'PHP'
<?php

namespace app\controllers;

use Yii;
use app\models\Post;
use app\models\PostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // authenticated users only
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Check if current user is the author of the post
     * @param Post $model
     * @return bool
     */
    protected function isAuthor($model)
    {
        return !Yii::$app->user->isGuest && $model->user_id == Yii::$app->user->id;
    }

    /**
     * Lists all Post models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Load user relation
        $model->user;
        
        // Increment views count using direct SQL update to avoid triggering behaviors
        $model->updateCounters(['views' => 1]);
        $model->refresh(); // Reload model to get updated views count
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Post();
        
        if ($model->load(Yii::$app->request->post())) {
            // Automatically assign user_id to current authenticated user
            $model->user_id = Yii::$app->user->id;
            
            // Handle file upload
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->validate()) {
                // Process image (file upload has priority over URL)
                $model->processImage();
                
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }
        
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        // Check if user is the author of this post
        if (!$this->isAuthor($model)) {
            throw new \yii\web\ForbiddenHttpException('You can only update your own posts.');
        }
        
        if ($model->load(Yii::$app->request->post())) {
            // Handle file upload
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->validate()) {
                // Process image (file upload has priority over URL)
                $model->processImage();
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Post has been updated successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }
        
        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if user is the author of this post
        if (!$this->isAuthor($model)) {
            throw new \yii\web\ForbiddenHttpException('You can only delete your own posts.');
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Post has been deleted successfully.');
        return $this->redirect(['site/index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
PHP;
file_put_contents('controllers/PostController.php', $postController);
echo "✓ Controllers created!\n\n";

// Views yaratish
echo "[9/15] Creating views...\n";

// views/site/index.php
$siteIndex = <<<'PHP'
<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="site-index">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center py-3">
            <h1 class="text-center">Savitar Blog</h1>
            <?php if (!Yii::$app->user->isGuest): ?>
                <p><?= Html::a('Create New Post', ['post/create'], ['class' => 'btn btn-success']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <?php if (empty($posts)): ?>
            <div class="col-md-12">
                <p class="text-muted">No posts available yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php
                        if (empty($post->image) || $post->image === 'default.jpg') {
                            $imageUrl = Yii::getAlias('@web/images/default.jpg');
                        } elseif (strpos($post->image, 'http') === 0) {
                            $imageUrl = $post->image;
                        } else {
                            $imageUrl = Yii::getAlias('@web/uploads/' . $post->image);
                        }
                        ?>
                        <img src="<?= $imageUrl ?>" class="card-img-top" alt="<?= Html::encode($post->title) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= Html::encode($post->title) ?></h5>
                            <p class="card-text flex-grow-1"><?= \yii\helpers\StringHelper::truncateWords(strip_tags($post->content), 20, '...') ?></p>
                        </div>
                        <div class="card-footer text-muted">
                            <small class="d-block mb-2">
                                <strong>Author:</strong> <?= Html::encode($post->user->username ?? 'Unknown') ?> | 
                                <strong>Views:</strong> <?= $post->views ?> | 
                                <strong>Created:</strong> <?= Yii::$app->formatter->asDatetime($post->created_at) ?>
                            </small>
                            <div class="btn-group" role="group">
                                <?= Html::a('Read More', ['post/view', 'id' => $post->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id === $post->user_id): ?>
                                    <?= Html::a('Edit', ['post/update', 'id' => $post->id], ['class' => 'btn btn-secondary btn-sm']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
PHP;
file_put_contents('views/site/index.php', $siteIndex);

// views/site/login.php
$siteLogin = <<<'PHP'
<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>
<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4"><?= Html::encode($this->title) ?></h1>
                    <p class="text-center text-muted mb-4">Please fill out the following fields to login</p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Enter your username']) ?>

                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Enter your password']) ?>

                    <?= $form->field($model, 'rememberMe')->checkbox() ?>

                    <div class="form-group mb-3">
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="text-center">
                        <p class="mb-0">Don't have an account? <?= Html::a('Sign up here', ['site/signup'], ['class' => 'text-primary text-decoration-none']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
PHP;
file_put_contents('views/site/login.php', $siteLogin);

// views/site/signup.php
$siteSignup = <<<'PHP'
<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Signup';
?>
<div class="site-signup">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4"><?= Html::encode($this->title) ?></h1>
                    <p class="text-center text-muted mb-4">Please fill out the following fields to signup</p>

                    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Choose a username']) ?>

                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Enter your password']) ?>

                    <?= $form->field($model, 'password_repeat')->passwordInput(['placeholder' => 'Repeat your password']) ?>

                    <div class="form-group mb-3">
                        <?= Html::submitButton('Signup', ['class' => 'btn btn-primary w-100', 'name' => 'signup-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? <?= Html::a('Login', ['site/login'], ['class' => 'text-primary text-decoration-none']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
PHP;
file_put_contents('views/site/signup.php', $siteSignup);

// views/post/view.php
$postView = <<<'PHP'
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Post $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['site/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-view">

    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>
            
            <?php if (Yii::$app->user->id === $model->user_id): ?>
                <p>
                <?= Html::a('← Back to Home', ['site/index'], ['class' => 'btn btn-secondary']) ?>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this post?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
            <?php endif; ?>
            
            <div class="card mb-4">
                <?php
                if (empty($model->image) || $model->image === 'default.jpg') {
                    $imageUrl = Yii::getAlias('@web/images/default.jpg');
                } elseif (strpos($model->image, 'http') === 0) {
                    $imageUrl = $model->image;
                } else {
                    $imageUrl = Yii::getAlias('@web/uploads/' . $model->image);
                }
                ?>
                <img src="<?= $imageUrl ?>" class="card-img-top" alt="<?= Html::encode($model->title) ?>" style="max-height: 400px; object-fit: contain;">
                <div class="card-body">
                    <div class="text-muted mb-3">
                        <small>
                            <strong>Author:</strong> <?= Html::encode($model->user->username ?? 'Unknown') ?> | 
                            <strong>Views:</strong> <?= $model->views ?> | 
                            <strong>Created:</strong> <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                            <?php if ($model->updated_at !== $model->created_at): ?>
                                | <strong>Updated:</strong> <?= Yii::$app->formatter->asDatetime($model->updated_at) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="card-text">
                        <?= nl2br(Html::encode($model->content)) ?>
                    </div>
                </div>
            </div>
            
            
        </div>
    </div>

</div>
PHP;
file_put_contents('views/post/view.php', $postView);

// views/post/create.php
$postCreate = <<<'PHP'
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Post $model */

$this->title = 'Create Post';
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['site/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
PHP;
file_put_contents('views/post/create.php', $postCreate);

// views/post/update.php
$postUpdate = <<<'PHP'
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Post $model */

$this->title = 'Update Post';
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['site/index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="post-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
PHP;
file_put_contents('views/post/update.php', $postUpdate);

// views/post/_form.php
$postForm = <<<'PHP'
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Post $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 10]) ?>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Image</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold mb-3">Image (Upload File or Enter URL)</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Upload Image File</label>
                        <div class="input-group">
                            <?= $form->field($model, 'imageFile', [
                                'options' => ['class' => 'form-control-wrapper flex-grow-1'], 
                                'template' => '{input}{error}'
                            ])->fileInput([
                                'class' => 'form-control', 
                                'accept' => 'image/*', 
                                'id' => 'imageFileInput'
                            ])->label(false) ?>
                            <label class="input-group-text bg-primary text-white" for="imageFileInput">
                                Upload
                            </label>
                        </div>
                        <small class="form-text text-muted d-block mt-1">
                            JPG, PNG, GIF (max 5MB)
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Or Enter Image URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary text-white">
                                URL
                            </span>
                            <?= $form->field($model, 'imageUrl', [
                                'options' => ['class' => 'form-control-wrapper flex-grow-1'], 
                                'template' => '{input}{error}'
                            ])->textInput([
                                'class' => 'form-control text-dark', 
                                'placeholder' => 'https://example.com/image.jpg',
                                'type' => 'url'
                            ])->label(false) ?>
                        </div>
                        <small class="form-text text-muted d-block mt-1">
                            Enter full image URL
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mb-0 mt-3">
                <strong>Note:</strong> File upload has priority. If both are provided, the uploaded file will be used. Leave both empty to use default image or keep current image.
            </div>
        </div>
    </div>
    
    <?php if (!$model->isNewRecord && !empty($model->image) && $model->image !== 'default.jpg'): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">Current Image</h6>
            </div>
            <div class="card-body text-center">
                <?php
                if (strpos($model->image, 'http') === 0) {
                    $imageUrl = $model->image;
                } else {
                    $imageUrl = Yii::getAlias('@web/uploads/' . $model->image);
                }
                ?>
                <img src="<?= $imageUrl ?>" alt="Current image" class="img-thumbnail" style="max-width: 300px; max-height: 300px; object-fit: contain;">
            </div>
        </div>
    <?php endif; ?>
    
    <?= $form->field($model, 'image')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['site/index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
PHP;
file_put_contents('views/post/_form.php', $postForm);

// views/post/index.php
$postIndex = <<<'PHP'
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'user_id',
            'views',
            'created_at:datetime',
            'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
PHP;
file_put_contents('views/post/index.php', $postIndex);
echo "✓ Views created!\n\n";

// Layout yangilash
echo "[10/15] Updating layout...\n";
$mainLayout = <<<'PHP'
<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => 'Savitar blog',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => [
            Yii::$app->user->isGuest
                ? ['label' => 'Login', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'],
                'homeLink' => ['label' => 'Home', 'url' => Yii::$app->homeUrl],
                'options' => ['class' => 'breadcrumb'],
                'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
                'activeItemTemplate' => "<li class=\"breadcrumb-item active\" aria-current=\"page\">{link}</li>\n",
            ]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
PHP;
file_put_contents('views/layouts/main.php', $mainLayout);
echo "✓ Layout updated!\n\n";

// Helper scripts
echo "[11/15] Creating helper scripts...\n";
$passwordHashScript = <<<'PHP'
<?php
/**
 * Script to generate password hash
 * Usage: php generate-password-hash.php your_password
 */

$password = isset($argv[1]) ? $argv[1] : 'password';

// Use PHP's built-in password hashing (same algorithm Yii2 uses)
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "\nPassword hash for '$password':\n";
echo $hash . "\n\n";
echo "SQL to update user:\n";
echo "UPDATE `user` SET `password_hash` = '$hash' WHERE `username` = 'your_username';\n\n";
PHP;
file_put_contents('generate-password-hash.php', $passwordHashScript);
echo "✓ Helper scripts created!\n\n";

// README yaratish
echo "[12/15] Creating README...\n";
$readmeContent = <<<MD
# Savitar Blog - Yii2 Blog Application

## Quick Setup

1. Create database:
   ```sql
   CREATE DATABASE {$dbName};
   ```

2. Run migrations:
   ```bash
   mysql -u {$dbUser} -p {$dbName} < migrations/schema.sql
   mysql -u {$dbUser} -p {$dbName} < migrations/dummy-data.sql
   ```

3. Start server:
   ```bash
   php yii serve
   ```

## Default Users (if dummy-data.sql imported)

- naruto / password
- sasuke / password
- sakura / password
- kakashi / password

## Features

- User authentication (Login/Signup)
- Post CRUD operations
- Author-based access control
- View counter
- Responsive Bootstrap 5 design
- Naruto-themed dummy data
MD;
file_put_contents('README-SETUP.md', $readmeContent);
echo "✓ README created!\n\n";

echo "[13/15] Finalizing...\n";
echo "✓ Setup completed!\n\n";

echo "========================================\n";
echo "Setup completed successfully!\n";
echo "========================================\n\n";
echo "Project location: $fullPath\n\n";
echo "Next steps:\n";
echo "1. Create database: CREATE DATABASE {$dbName};\n";
echo "2. Run migrations: mysql -u {$dbUser} -p {$dbName} < migrations/schema.sql\n";
echo "3. Import dummy data: mysql -u {$dbUser} -p {$dbName} < migrations/dummy-data.sql\n";
echo "4. Start server: php yii serve\n\n";
echo "For detailed instructions, see README-SETUP.md\n";

/**
 * Recursively delete directory
 */
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    return rmdir($dir);
}

