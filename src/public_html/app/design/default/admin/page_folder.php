<?php

  $default_converters = Converters::get_converters();
  $bucket = new Bucket("slug", $params["bucket"]);

  if (($_GET["act"] ?? "") == "uploadDD") {
    if (!is_writable("/originals/")) { die(json_encode(array("err" => 1, "msg" => "Ordner Originals nicht beschreibbar."))); }
    foreach ($_FILES as $file) {
      if (file_exists("/originals/demo/".$file["name"])) continue;
      //if (!is_writable("/var/www/originals/".$file["name"])) { die(json_encode(array("err" => 2, "msg" => "Datei in Originals nicht beschreibbar."))); }
      copy($file["tmp_name"], "/originals/demo/".$file["name"]);
      echo("done");
    }
      die(json_encode(array("err" => 0, "msg" => "Ok")));
    exit;
  }

  if (($_GET["act"] ?? "") == "uploadDropzone") {
    if (!is_writable("/originals/")) { die(json_encode(array("err" => 1, "msg" => "Ordner Originals nicht beschreibbar."))); }
    foreach ($_FILES as $file) {
      if (file_exists("/originals/demo/".$file["name"])) continue;
      //if (!is_writable("/var/www/originals/".$file["name"])) { die(json_encode(array("err" => 2, "msg" => "Datei in Originals nicht beschreibbar."))); }
      copy($file["tmp_name"], "/originals/demo/".$file["name"]);
      echo("done");
    }
      die(json_encode(array("err" => 0, "msg" => "Ok")));
    exit;
  }

  if (($_POST["act"] ?? "") == "saveconfig") {
    $json = @json_decode(@file_get_contents("/config/config.json"),true);
    if (!is_array($json)) $json = array();

    $i = -1;
    foreach ($default_converters as $k => $v) {
      $i++;
      $json["buckets"][$params["bucket"]]["conv"][$k] = trim($_POST["convert_".$i] ?? 0);
    }

    $json["buckets"][$params["bucket"]]["dlperm"] = trim($_POST["dlperm"] ?? "private");

    file_put_contents("/config/config.json", json_encode($json));
  }


  $jsonfiles = @json_decode(@file_get_contents("/config/files.json"), true);
  $json_config = @json_decode(@file_get_contents("/config/config.json"),true);

    PageEngine::html("header");
?>
  <main id="dropzone" class="dropzone" style="min-height: 100vh">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-database"></i><?=$params["bucket"] ?> <?=$params["path"] ?>
<?php
if (!is_writable("/originals/".$params["bucket"].$params["path"])) echo('<i class="fas fa-lock ml-3" style="color: #d32f2f;"></i>');

?>
        
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" id="clickable" class="btn btn-sm btn-outline-secondary"><i class="fad fa-folder-upload mr-1"></i>Upload</button>
            <!--<button type="button" class="btn btn-sm btn-outline-secondary">Export</button>-->
          </div>
          <!--<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            This week
          </button>-->
        </div>
      </div>


      <div id="table01o"><table id="table01" class="table table-striped">
      
<?php
  $path2 = "/originals/".$params["bucket"].$params["path"];

  if (!file_exists($path2)) echo('<div class="alert alert-danger alert-dismissible fade show" role="alert">
  Diesen Ordner gibt es leider nicht!
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>');
  elseif (!is_writable($path2)) echo('<div class="alert alert-warning alert-dismissible fade show" role="alert">
  Der Ordner ist nicht beschreibbar!
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>');
else {


  $files = scandir($path2);
  foreach ($files as $file) {
    if (substr($file,0,1) == ".") continue;
    if (is_dir($path2.$file)) echo('<tr><td><i class="far fa-folder"></i></td><td><i class="far fa-folder"></i> '.$file.'</td></tr>');
  }
  foreach ($files as $file) {
    if (substr($file,0,1) == ".") continue;
    if (is_dir($path2.$file)) continue;
    $datei = new Datei($path2.$file);
    echo('<tr><td>');
    switch ($datei->extension) {
      case "jpg":
      case "jpeg":
      case "png":
        echo('<i class="far fa-image"></i>'); break;
      case "mp4":
      case "webm":
        echo('<i class="far fa-film"></i>'); break;
      case "rar":
      case "zip":
        echo('<i class="far fa-file-archive"></i>'); break;
      default: echo($datei->extension); break;
    }
    if ($datei->is_video) {
      echo('</td><td><a href="/bucket/'.$params["bucket"].$params["path"].$datei->basename.'?download=original" TARGET="_blank">'.$file.'</a></td>');
      echo('<td>'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'</td>');
      echo('<td>');

      $convertings = $jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["conv"] ?? array();

      if (count($convertings) > 0) {
        echo('<div class="">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="far fa-eye"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">');
        foreach (($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["conv"] ?? array()) as $a) {
          switch ($a) {
            case "d.thumbmiddle":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.0.z1920x1080.jpg" class="dropdown-item" TARGET="_blank"><i class="far fa-image"></i> Thumbnail</a>');
              break;
            case "d.thumb25p":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.1.z1920x1080.jpg" class="dropdown-item" TARGET="_blank"><i class="far fa-image"></i> Preview-Thumbnail</a>');
              break;
            case "d.mp41080p":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.1080p.mp4" class="dropdown-item" TARGET="_blank"><i class="far fa-film"></i> mp4 (1080p, h264, aac)</a>');
              break;
            case "d.webm1080p":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.1080p.webm" class="dropdown-item" TARGET="_blank"><i class="far fa-film"></i> webm (1080p, vp9, Vobis)</a>');
              break;
            case "d.mp4480p":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.480p.mp4" class="dropdown-item" TARGET="_blank"><i class="far fa-film"></i> mp4 (480p, h264, aac)</a>');
              break;
            case "d.webm480p":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.480p.webm" class="dropdown-item" TARGET="_blank"><i class="far fa-film"></i> webm (480p, vp9, Vobis)</a>');
              break;
            case "d.mp4240p":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.240p.mp4" class="dropdown-item" TARGET="_blank"><i class="far fa-film"></i> mp4 (240p, h264, aac)</a>');
              break;
            case "d.webm240p":
              echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.240p.webm" class="dropdown-item" TARGET="_blank"><i class="far fa-film"></i> webm (240p, vp9, Vobis)</a>');
              break;
            default: 
              echo($a);
          }
        }
        echo('<a href="/bucket/'.$params["bucket"].'/'.($jsonfiles["bucket"][$params["bucket"]][$datei->bucketprekey]["md5"] ?? "").'.embed.html" class="dropdown-item" TARGET="_blank"><i class="far fa-code"></i> HTML-Video for IFRAME</a>');
        echo('</div>
      </div>');
    }
      //echo('<a href="/bucket/'.$params["bucket"].'/'.$datei->md5.'.0.z1920x1080.jpg" TARGET="_blank">img</a>');
      //echo('<a href="/bucket/'.$params["bucket"].'/'.$datei->md5.'.embed.html" TARGET="_blank">embed</a>');
      echo('</td>');
    } else {
      echo('</td><td><a href="/bucket/'.$params["bucket"].$params["path"].$datei->basename.'?download=original" TARGET="_blank">'.$file.'</a></td>');
      echo('<td>'.$datei->md5.'</td>');
      echo('<td>---</td>');
    }
    echo('</tr>');
  }

}
  ?>
      
      </table></div>

      <?php
//print_r($jsonfiles);
//echo('<pre>'.json_encode($json_config,JSON_PRETTY_PRINT).'</pre>');
      ?>

      <div class="progress">
        <div id="uploadProgress2" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
      </div>

      <div id="previews" class="dropzone-previews"></div>

<?php
      if ($params["path"] == "/") {
  echo('<form method="POST"><INPUT type="hidden" name="act" value="saveconfig"/>');
  echo('<div class="row my-2">');
  echo('<div class="col">');
  echo('<h3><i class="fad fa-conveyor-belt-alt"></i> Bucket Converters</h3>');
  echo('<table class="table table-striped">
  <thead><tr>
        <th></th>
        <th>default</th>
        <th class="text-center">disabled</th>
        <th class="text-center">default</th>
        <th class="text-center">enabled</th>
        <th>this</th>
  </tr></thead>
  <tbody>
  ');
  $i = -1;
  foreach ($default_converters as $k => $v) {
    $i++;
    echo('<tr>
    <td>'.$v->get_name().'</td>
    <td>');
    if (($json_config["convert"]["default"][$k] ?? false) == true) echo('<span style="color: #080;">enabled</span>'); else echo('<span style="color: #f00">disabled</span>');
    $sel = $json_config["buckets"][$params["bucket"]]["conv"][$k] ?? 0;
    echo('</td>');
    echo('<td><INPUT class="form-control" type="radio" name="convert_'.$i.'" '.(($sel == -1)?'CHECKED="CHECKED"':'').' value="-1"/></td>');
    echo('<td><INPUT class="form-control" type="radio" name="convert_'.$i.'" '.(($sel ==  0)?'CHECKED="CHECKED"':'').' value="0" /></td>');
    echo('<td><INPUT class="form-control" type="radio" name="convert_'.$i.'" '.(($sel ==  1)?'CHECKED="CHECKED"':'').' value="1" /></td>');
    echo('<td>');
    switch ($sel) {
      case 1: echo('<span style="color: #080;">enabled</span>'); break;
      case -1: echo('<span style="color: #f00;">disabled</span>'); break;
      case 0:
        if (($json_config["convert"]["default"][$k] ?? false) == true) echo('<span style="color: #080;">enabled</span>'); else echo('<span style="color: #f00">disabled</span>');
    }
    echo('</td>');
    echo('</tr>');
  }
  echo('</tbody></table>');
  echo('<BUTTON class="btn btn-primary" type="submit">save</button>');
  echo('</div>');
  echo('<div class="col">');
  echo('<h3><i class="fad fa-user"></i> Bucket Users</h3>');
  echo('<div><INPUT type="checkbox" CHECKED="CHECKED" onclick="return false;"/> Superadmin</div>');
  echo('</div>');
  echo('<div class="col">');
  echo('<h3><i class="fad fa-key"></i> Bucket Permissions</h3>');
  $sel = $json_config["buckets"][$params["bucket"]]["dlperm"] ?? "private";
  echo('<div><label><INPUT type="radio" name="dlperm" value="private" '.(($sel == "private")?'CHECKED="CHECKED"':'').'/> Private</label></div>');
  echo('<div><label><INPUT type="radio" name="dlperm" value="public" '.(($sel == "public")?'CHECKED="CHECKED"':'').'/> Public</label></div>');
  echo('<div><label><INPUT type="radio" name="dlperm" value="token" '.(($sel == "token")?'CHECKED="CHECKED"':'').'/> Token Salt:</label><INPUT class="form-control" type="text" name="dlperm_salt" PLACEHOLDER="salt"/></div>');
  echo('</div>');
  echo('</div>');
  echo('</form>');
}
?>


  </main>
<link rel="stylesheet" href="/skins/default/libs/dropzone/min/dropzone.min.css" />
<script src="/skins/default/libs/dropzone/min/dropzone.min.js"></script>
<script>
  var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
    url: "?act=uploadDropzone", // Set the url
    previewsContainer: "#previews", // Define the container to display the previews
    clickable: "#clickable" // Define the element that should be used as click trigger to select files.
  });

  myDropzone.on("complete", function(file) {
    $("#table01o").load(location.href + " #table01");
    /* Maybe display some more file information on your page */
  });
</script>
<?php
    PageEngine::html("footer");
?>