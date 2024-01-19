<?php

$users_json = file_get_contents('config/users.json');
$users = json_decode($users_json, true);

$categories_json = file_get_contents('config/categories.json');
$categories = json_decode($categories_json, true);

function getCategory($categoryNum) {
  global $categories;
  if ($categoryNum >= count($categories)) {
    return '不明';
  }
  return $categories[$categoryNum];
}

$pdfRootFolder = fgets(fopen('config/pdfRootFolder.txt', 'r'));

$user = isset($_POST['user']) ? $_POST['user'] : '';
$password = isset($_POST['pass']) ? $_POST['pass'] : '';
$foldername = isset($_POST['pid']) ? $_POST['pid'] : '';

if (!isset($users[$user]) || $users[$user] !== $password) {
  header("Location: errorpage/401.html");
  exit();
}

$pdfFolder = $pdfRootFolder . DIRECTORY_SEPARATOR . $foldername;

if (!is_dir($pdfFolder)) {
  header("Location: errorpage/404.html");
  exit();
}

$pdfFiles = glob($pdfFolder . DIRECTORY_SEPARATOR . "*.pdf");

if (count($pdfFiles) === 0) {
  header("Location: errorpage/404.html");
  exit();
}

function parseFileName($pdfFile) {
  $name = basename($pdfFile);
  if (preg_match('/^(\d+)-(\d{4})(\d{2})(\d{2})-(\d+)-(\d+)\.pdf$/', $name, $matches)) {
    $serial = intval($matches[6]);
    $name_serial = ($serial > 1) ? ' (' . $serial . ')' : '';
    $date = "{$matches[2]}/{$matches[3]}/{$matches[4]}";
    return array(
      'id' => intval($matches[1]),
      'date' => $date,
      'category' => getCategory(intval($matches[5])),
      'serial' => $serial,
      'file' => $pdfFile,
      'name' => $date . $name_serial
    );
  }
}

$pdfDict = array();

foreach ($pdfFiles as $key => $pdfFile) {
  $parsed = parseFileName($pdfFile);
  $category = $parsed['category'];
  if (!array_key_exists($category, $pdfDict)) {
    $pdfDict[$category] = array();
  }
  $pdfDict[$category][] = $parsed;
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="sjis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer</title>
    <style>
        html, body {
            padding: 0;
            margin: 0;
        }
        ul, li {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        a {
            text-decoration: none;
            color: white;
        }
        a:hover {
            color: #45bd90;
        }
        #pdfList {
            width: 200px;
            float: left;
            height: 100vh;
            background-color: #323639;
        }
        #pdfViewer {
            float: left;
            width: calc(100% - 200px);
            height: 100vh;
        }
        iframe {
            width: 100%;
            height: 100%;
        }
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 56px;
            background-color: #323639;
        }
        .header-title {
            font-size: 1.5em;
            color: white;
        }
        .header-shadow {
            height: 4px;
            background: linear-gradient(#222629, #323639);
        }
        .category-header {
            border-bottom: solid gray 1px;
            color: white;
            margin-top: 16px;
            margin-bottom: 8px;
            padding-left: 8px;
        }
        .category-content {
            color: white;
            padding-left: 8px;
            font-size: 0.9em;
        }
        .pdf-item-active {
          font-weight: bold;
          color: tomato;
        }
    </style>
</head>

<body>
  <div id="pdfList">
    <div class="header">
      <div class="header-title">カルテPDF</div>
    </div>
    <div class="header-shadow"></div>
    <div class="category-header">外来</div>
    <div class="category-content">
      <ul>
        <?php foreach ($pdfDict as $category => $pdfs): ?>
          <li>
            <div class="category-header"><?php echo $category; ?></div>
            <div class="category-content">
              <ul>
                <?php foreach ($pdfs as $pdf): ?>
                  <li><a href="#" data-pid="<?php echo $foldername; ?>" data-pdf="<?php echo basename($pdf['file']); ?>" onclick="showPDF(this); return false;" class="pdf-item"><?php echo $pdf['name']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="category-header">入院</div>
    <div class="category-content">
    </div>
  </div>
  
  <div id="pdfViewer">
    <iframe id="viewer" src="" frameborder="0"></iframe>
  </div>
  
  <script>
    const doc = document.getElementById('viewer').contentWindow.document;
    doc.open();
    doc.write('<html><body style="margin: 0;"><div style="height: 56px; display: flex; align-items: center; justify-content: center; color:white; background-color: #323639;">カルテを選択してください</div><html>');
    doc.close();
    function resetActive() {
      const items = document.getElementsByClassName('pdf-item-active');
      if (items.length > 0) {
        for (let i = 0; i < items.length; ++i) {
          items[i].classList.remove('pdf-item-active');
        }
      }
    }
    function showPDF(element) {
      const pdf = element.getAttribute('data-pdf');
      const pid = element.getAttribute('data-pid');
      document.getElementById('viewer').src = 'getpdf.php?file=' + pdf + '&pid=' + pid;
      resetActive();
      element.classList.add("pdf-item-active");
    }
  </script>
</body>
</html>
