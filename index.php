<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "gradesdata";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

if (isset($_POST['submitA'])) {
    $sql = "INSERT INTO user (math, physics, chemistry, adabi, arabi, dini, english , fieldOfStudy) VALUES (:math, :physics, :chemistry, :adabi, :arabi, :dini, :english , :fieldOfStudy)";
    $stmt = $db->prepare($sql);

    $math = !empty($_POST['mathA']) ? $_POST['mathA'] : 0;
    $physics = !empty($_POST['physicsA']) ? $_POST['physicsA'] : 0;
    $chemistry = !empty($_POST['chemistryA']) ? $_POST['chemistryA'] : 0;
    $adabi = !empty($_POST['adabiA']) ? $_POST['adabiA'] : 0;
    $arabi = !empty($_POST['arabiA']) ? $_POST['arabiA'] : 0;
    $dini = !empty($_POST['diniA']) ? $_POST['diniA'] : 0;
    $english = !empty($_POST['englishA']) ? $_POST['englishA'] : 0;
    $fieldOfStudy = !empty($_POST['fieldOfStudyA']) ? $_POST['fieldOfStudyA'] : 0;

    $stmt->bindParam(':math', $math);
    $stmt->bindParam(':physics', $physics);
    $stmt->bindParam(':chemistry', $chemistry);
    $stmt->bindParam(':adabi', $adabi);
    $stmt->bindParam(':arabi', $arabi);
    $stmt->bindParam(':dini', $dini);
    $stmt->bindParam(':english', $english);
    $stmt->bindParam(':fieldOfStudy', $fieldOfStudy);

    if ($stmt->execute()) {
?>
        <script>
            alert("ثبت شد")
        </script>
    <?php
    } else { ?>
        <script>
            alert("ثبت نشد")
        </script>
<?php
    }
}
if (isset($_POST['submitB'])) {
    $sql = "SELECT math, physics, chemistry, adabi, arabi, dini, english, fieldOfStudy FROM user";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $features = [];
    $labels = [];
    foreach ($results as $row) {
        $features[] = [
            $row['math'],
            $row['physics'],
            $row['chemistry'],
            $row['adabi'],
            $row['arabi'],
            $row['dini'],
            $row['english']
        ];
        $labels[] = $row['fieldOfStudy'];
    }

    $new_grades = [
        $_POST['math'],
        $_POST['physics'],
        $_POST['chemistry'],
        $_POST['adabi'],
        $_POST['arabi'],
        $_POST['dini'],
        $_POST['english']
    ];

    $data = [
        'features' => $features,
        'labels' => $labels,
        'new_grades' => $new_grades
    ];
    file_put_contents('data.json', json_encode($data));

    $command = escapeshellcmd('python predict.py');
    $output = shell_exec($command);
    $output = trim($output); 

    if (!empty($output)) {
       // echo "رشته تحصیلی پیش‌بینی شده: " . htmlspecialchars($output);
    } else {
        echo "خطایی رخ داده است و رشته تحصیلی پیش‌بینی نشد.";
    }
}

?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Galaxy</title>
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="./css/bootstrap-icons.css" />
        <!-- styles css -->
        <link rel="stylesheet" href="./styles/style.css" />
    </head>
    <body>
        <nav class="navbar">
            <div class="navbar-center">
                <div class="nav-header">
                    <h3>myWeb.io</h3>
                    <button class="nav-toggle" id="nav-toggle" type="button">
                        <i class="bi bi-list-nested"></i>
                    </button>
                </div>

                <ul class="nav-links" id="nav-links">
                    <li>
                        <a class="nav-link scroll-link" href="#home">صفحه اصلی</a>
                    </li>
                    <li>
                        <a class="nav-link scroll-link" href="#about">پیش بینی</a>
                    </li>
                    <li>
                        <a class="nav-link scroll-link" href="#services">توضیحات</a>
                    </li>
                </ul>

                <ul class="nav-icons">
                    <li>
                        <a class="nav-icon" href="#">
                            <i class="bi bi-twitter"></i>
                        </a>
                    </li>
                    <li>
                        <a class="nav-icon" href="#">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </li>
                    <li>
                        <a class="nav-icon" href="#">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

       <section class="body_2">
        <div class="container">
            <h1 style="margin-top: 15rem;">پیش بینی رشته تحصیلی</h1>
            <div class="switch-buttons">
                <button style="font-family:vazir" id="section1Btn" onclick="showSection('section1')">پیش بینی رشته</button>
                <button style="font-family:vazir" id="section2Btn" onclick="showSection('section2')">وارد کردن داده</button>
            </div>

            <div id="section1" class="section">
                <h2>درصد های کنکور خود را وارد کنید</h2>
                <form id="section1Form">
                    <label>نمره ریاضی:</label>
                    <input type="number" id="math" name="math" required>

                    <label>نمره فیزیک:</label>
                    <input type="number" id="physics" name="physics" required>

                    <label>نمره شیمی:</label>
                    <input type="number" id="chemistry" name="chemistry" required>

                    <label>ادبیات</label>
                    <input type="number" id="biology" name="biology" required>

                    <label>عربی</label>
                    <input type="number" id="history" name="history" required>

                    <label>دینی</label>
                    <input type="number" id="geography" name="geography" required>

                    <label>انگلیسی</label>
                    <input type="number" id="english" name="english" required>

                    <button type="button">پیش بینی</button>
                </form>
                <div id="averageResult" class="result-box">
                    <p> رشته: <span id="average"></span></p>
                </div>
            </div>

            <div id="section2" class="section" style="display:none;">
                <h2>وارد کردن داده های جدید به دیتابیس</h2>
                <form id="section2Form">
                    <label>نمره ریاضی:</label>
                    <input type="number" id="math" name="math" required>

                    <label>نمره فیزیک:</label>
                    <input type="number" id="physics" name="physics" required>

                    <label>نمره شیمی:</label>
                    <input type="number" id="chemistry" name="chemistry" required>

                    <label>ادبیات</label>
                    <input type="number" id="biology" name="biology" required>

                    <label>عربی</label>
                    <input type="number" id="history" name="history" required>

                    <label>دینی</label>
                    <input type="number" id="geography" name="geography" required>

                    <label>انگلیسی</label>
                    <input type="number" id="english" name="english" required>

                    <button type="button">اضافه کردن</button>

                </form>
            </div>
        </div>
       </section>
        </section>
        <script src="Js/Js.js"></script>
    </body>
</html>
