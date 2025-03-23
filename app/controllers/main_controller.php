<?php

require_once 'models/photo_model.php';
require_once 'models/interest_model.php';
require_once 'models/test_model.php';
require_once 'models/blog_model.php';

class MainController extends Controller {
    // Основной метод, который будет вызываться по умолчанию
    public function index() {
        $this->view->render('pages/main_page.php', 'Главная страница');
    }

    // Методы для остальных страниц
    public function actionAboutMe() {
        $this->view->render('pages/about_me.php', 'Обо мне');
    }

    public function actionMyHobbies() {
        $interests = Interest::getInterests();
        $categories = Interest::getCategories();
        $hobby = Interest::getHobby();
        $this->view->render('pages/my_hobbies.php', 'Мои интересы', ['categories' => $categories, 'hobby' => $hobby, 'interests' => $interests]);
    }

    public function actionStudy() {
        $this->view->render('pages/study.php', 'Учеба');
    }

    public function actionPhotoAlbum() {
        $photos = Photo::getAllPhotos();
        $this->view->render('pages/photo_album.php', 'Фотоальбом', $photos);
    }

    public function actionContact() {
        $this->view->render('pages/contact.php', 'Контакты');
    }

    public function actionMyBlog() {
        $perPage = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1; // Защита от отрицательных значений

        $totalPages = BlogModel::getTotalPosts(); // Общее количество страниц
        $posts = BlogModel::getPostsWithPagination($page, $perPage);

        $model = [
            'page' => $page,
            'totalPages' => $totalPages,
            'posts' => $posts
        ];

        $this->view->render('pages/my_blog.php', 'Мой блог', $model);
    }

    public function actionDisciplineTest() {
        $results = TestModel::getAllTestResults();
    
        // Подготовка данных для передачи в представление
        $model = [
            'results' => $results // Добавляем результаты из БД
        ];
    
        // Рендеринг страницы
        $this->view->render('pages/discipline_test.php', 'Тест по дисциплине', $model);
    }

    public function actionHistory() {
        $this->view->render('pages/history.php', 'История просмотра');
    }

    public function actionGuestBook() {
        $this->view->render('pages/guestbook.php', 'Гостевая книга');
    }

    public function validateTest() {
        // Настройка правил валидации
        $this->model->results_validator->SetTestsRule('fullName', 'isValidFio');
        $this->model->results_validator->SetResultsRule('Ecosystem', 'CheckEcosystem');
        $this->model->results_validator->SetResultsRule('AbioticFactors', 'CheckAbioticFactors');
        $this->model->results_validator->SetResultsRule('Science', 'CheckScience');
    
        $message = '';
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Валидация данных формы
            $isValid = $this->model->results_validator->VerifyResults($_POST);
    
            // Извлечение данных из формы
            $fullName = $_POST['fullName'] ?? '';
            $answers = [
                'Ecosystem' => $_POST['Ecosystem'] ?? '',
                'AbioticFactors' => $_POST['AbioticFactors'] ?? '',
                'Science' => $_POST['Science'] ?? ''
            ];
    
            // Сохранение результата в базу данных
            TestModel::saveResult($fullName, $answers, $isValid ? 1 : 0);
    
            if ($isValid) {
                $message = 'Тест успешно пройден! Все ответы правильные!';
            } else {
                $fields = ['fullName', 'Ecosystem', 'AbioticFactors', 'Science'];
                foreach ($fields as $field) {
                    $errors[$field] = $this->model->results_validator->ShowErrors($field);
                }
            }
        }
    
        // Получение всех сохранённых результатов из базы данных
        $tests = TestModel::getAllTestResults();
        // Подготовка данных для передачи в представление
        $model = [
            'errors' => $errors,
            'form_data' => $_POST ?? [],
            'message' => $message,
            'results' => $tests // Добавляем результаты из БД
        ];
    
        // Рендеринг страницы
        $this->view->render('pages/discipline_test.php', 'Тест по дисциплине', $model);
    }

    public function actionUploadGuestBook() {
        $this->view->render('pages/upload_guestbook.php', 'Загрузка гостевой книги');
    }

    public function validateContact() {
        $this->model->validator->SetRule('fullName', 'isNotEmpty');
        $this->model->validator->SetRule('Email', 'isEmail');
        $this->model->validator->SetRule('age', 'isGreater', 18);
        $this->model->validator->SetRule('msg', 'isNotEmpty');
        $this->model->validator->SetRule('mobilePhone', 'isNotEmpty');

        $message = '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->validator->Validate($_POST)) {
                $message = 'Форма успешно отправлена!';
            } else {
                $fields = ['fullName', 'Email', 'age', 'msg', 'mobilePhone'];

                foreach ($fields as $field) {
                    $errors[$field] = $this->model->validator->ShowErrors($field);
                }
            }

            $model = [
                'errors' => $errors,
                'form_data' => $_POST,
                'message' => $message !== '' ? $message : ''
            ];

            $this->view->render('pages/contact.php', 'Контакты', $model);
        } else {
            $this->view->render('pages/contact.php', 'Контакты');
        }
    }

    public function validateGuestBook() {
        $this->model->validator->setRule('second_name', 'isNotEmpty');
        $this->model->validator->setRule('first_name', 'isNotEmpty');
        $this->model->validator->setRule('middle_name', 'isNotEmpty');
        $this->model->validator->setRule('email', 'isEmail');
        $this->model->validator->setRule('message', 'isNotEmpty');

        $success_message = '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->validator->Validate($_POST)) {
                $second_name = trim($_POST['second_name']);
                $first_name = trim($_POST['first_name']);
                $middle_name = trim($_POST['middle_name']);
                $email = trim($_POST['email']);
                $message = trim($_POST['message']);

                $date = date('d.m.y');
                $fio = "$second_name $first_name $middle_name";
                $data = "$date;$fio;$email;$message\n";

                file_put_contents("messages.inc", $data, FILE_APPEND);
            
                $success_message = 'Сообщение успешно добавлено!';
            } else {
                $fields = ['second_name', 'first_name', 'middle_name','email', 'message'];

                foreach ($fields as $field) {
                    $errors[$field] = $this->model->validator->showErrors($field);
                }
            }
        }

        $messages = [];

        if (file_exists('messages.inc')) {
            $lines = file('messages.inc', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $messages[] = explode(";", $line);
            }
            usort($messages, function($a, $b) {
                return strtotime($b[0]) - strtotime($a[0]);
            });
        }

        $model = [
            'errors' => $errors,
            'form_data' => $_POST ?? [],
            'message' => $success_message !== '' ? $success_message : '',
            'messages' => $messages
        ];

        $this->view->render('pages/guestbook.php', 'Гостевая книга', $model);
    }

    public function uploadMyBlog() {
        $message = '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['messages_file'];

            if (isset($_FILES['csv_file']) && $file['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['csv_file']['tmp_name'];
                $fileType = mime_content_type($file);

                if ($fileType !== 'text/csv') {
                    $errors['csv_file'] = "Файл должен быть в формате CSV";
                } else {
                    $handle = fopen($file, 'r');
                    $row = 0;
                    $successCount = 0;

                    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                        $row++;
                        if ($row === 1) continue; // Пропускаем заголовок, если он есть

                        // Проверка количества полей
                        if (count($data) !== 4) {
                            $errors[] = "Ошибка в строке $row: неверное количество полей";
                            continue;
                        }

                        [$title, $message, $author, $created_at] = $data;
                    }
                }
            } else {
                $error = 'Ошибка загрузки CSV-файла: ' . $this->getUploadErrorMessage($file['error']);
            }
        }

        $model = [
            'message' => $message,
            'error' => $error
        ];

        $this->view->render('pages/upload_my_blog.php', 'Загрузка сообщений блога', $model);
    }

    public function uploadGuestBookFile() {
        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['messages_file'])) {
            $file = $_FILES['messages_file'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $fileName = $file['name'];
                $fileTmpPath = $file['tmp_name'];

                if ($fileName === 'messages.inc') {
                    $destination = '../uploads/messages.inc';
                    if (move_uploaded_file($fileTmpPath, $destination)) {
                        $message = 'Файл успешно загружен';
                    } else {
                        $error = 'Ошибка загрузки файла на сервер';
                    }
                } else {
                    $error = 'Поменяйте пожалуйста имя на messages.inc';
                }
            } else {
                $error = 'Ошибка загрузки файла: ' . $this->getUploadErrorMessage($file['error']);
            }
        }

        $model = [
            'message' => $message,
            'error' => $error
        ];

        $this->view->render('pages/upload_guestbook.php', 'Загрузка сообщений гостевой книги', $model);
    }

    public function getUploadErrorMessage($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Размер файла превышает доступный в php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Размер файла превышает допустимый лимит формы.';
            case UPLOAD_ERR_PARTIAL:
                return 'Файл был загружен частично.';
            case UPLOAD_ERR_NO_FILE:
                return 'Файл не был загружен.';
            default:
                return 'Неизвестная ошибка загрузки.';
        }
    }

    public function actionRedactorBloga() {
        $this->model->validator->setRule('msg_theme', 'isNotEmpty');
        $this->model->validator->setRule('message', 'isNotEmpty');

        $success_message = '';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->validator->Validate($_POST)) {
                $msg_theme = trim($_POST['msg_theme']);
                $image = trim($_POST['image']);
                $message = trim($_POST['message']);

                BlogModel::createPost($msg_theme, $message, $image);
            
                $success_message = 'Пост успешно создан!';
            } else {
                $fields = ['msg_theme', 'message'];

                foreach ($fields as $field) {
                    $errors[$field] = $this->model->validator->showErrors($field);
                }
            }
        }

        $perPage = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1; // Защита от отрицательных значений

        $totalPages = BlogModel::getTotalPosts(); // Общее количество страниц
        $posts = BlogModel::getPostsWithPagination($page, $perPage);

        $model = [
            'page' => $page,
            'totalPages' => $totalPages,
            'posts' => $posts
        ];

        $this->view->render('pages/redactor_bloga.php', 'Редактор блога', $model);
    }
}