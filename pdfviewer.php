<?php

require_once("./auth.php");
require_once("./getcategory.php");
require_once("./getfoldercontents.php");

/** Logger */
require_once("./logger.php");
$log = Logger::getInstance();

/** get parameters */
$user = isset($_POST['user']) ? $_POST['user'] : '';
$password = isset($_POST['pass']) ? $_POST['pass'] : '';
$foldername = isset($_POST['pid']) ? $_POST['pid'] : '';

/** check user */
isValidUserOrDie($user, $password);
$log->info('Access log. user: ' . $user . ', pid: ' . $foldername);

$pdfFiles = getFolderContents($foldername, 'out');
$pdfFilesAdm = getFolderContents($foldername, 'adm');

/** file check */
if (count($pdfFiles) === 0 && count($pdfFilesAdm) === 0) {
  $log->error('No pdf file. pid: ' . $foldername);
  header("Location: errorpage/404.html");
  exit();
}

/**
 * Parse File info as array
 */
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

/** category directory structures */
foreach ($pdfFiles as $key => $pdfFile) {
  $parsed = parseFileName($pdfFile);
  $parsed['type'] = 'out';
  $category = $parsed['category'];
  if (!array_key_exists($category, $pdfDict)) {
    $pdfDict[$category] = array();
  }
  $pdfDict[$category][] = $parsed;
}

$pdfDictAdm = array();

/** category directory structures */
foreach ($pdfFilesAdm as $key => $pdfFile) {
  $parsed = parseFileName($pdfFile);
  $parsed['type'] = 'adm';
  $category = $parsed['category'];
  if (!array_key_exists($category, $pdfDictAdm)) {
    $pdfDictAdm[$category] = array();
  }
  $pdfDictAdm[$category][] = $parsed;
}

?>

<!DOCTYPE html>
<html lang="ja" translate="no" class="notranslate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate" />
    <title>PDF Viewer</title>
    <style>
        html, body {
            padding: 0;
            margin: 0;
            overflow: hidden;
            font-family: "Helvetica Neue", "Helvetica", "Hiragino Sans", "Hiragino Kaku Gothic ProN", "Arial", "Yu Gothic", "Meiryo", sans-serif;
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
            line-height: 1.5em;
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
        .category-header-bold {
            font-weight: bold;
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
      <div class="header-title">📄 カルテPDF</div>
    </div>
    <div class="header-shadow"></div>

<?php if (count($pdfFiles) !== 0) : ?>
    <div class="category-header-bold">🚕 外来</div>
    <div class="category-content">
      <ul>
        <?php foreach ($pdfDict as $category => $pdfs): ?>
          <li>
            <div class="category-header"><?php echo $category; ?></div>
            <div class="category-content">
              <ul>
                <?php foreach ($pdfs as $pdf): ?>
                  <li><a href="#" data-pid="<?php echo $foldername; ?>" data-pdf="<?php echo basename($pdf['file']); ?>" data-type="<?php echo $pdf['type']; ?>" onclick="showPDF(this); return false;" class="pdf-item"><?php echo $pdf['name']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
<?php endif; ?>

<?php if (count($pdfFilesAdm) !== 0) : ?>
    <div class="category-header-bold">🏥 入院</div>
    <div class="category-content">
      <ul>
        <?php foreach ($pdfDictAdm as $category => $pdfs): ?>
          <li>
            <div class="category-header"><?php echo $category; ?></div>
            <div class="category-content">
              <ul>
                <?php foreach ($pdfs as $pdf): ?>
                  <li><a href="#" data-pid="<?php echo $foldername; ?>" data-pdf="<?php echo basename($pdf['file']); ?>" data-type="<?php echo $pdf['type']; ?>" onclick="showPDF(this); return false;" class="pdf-item"><?php echo $pdf['name']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
<?php endif; ?>

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
      const typ = element.getAttribute('data-type');
      document.getElementById('viewer').src = 'getpdf.php?file=' + pdf + '&pid=' + pid + '&type=' + typ;
      resetActive();
      element.classList.add("pdf-item-active");
    }
  </script>
</body>
</html>
