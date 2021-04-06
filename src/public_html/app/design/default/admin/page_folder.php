<?php

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

    PageEngine::html("header");
?>
  <main id="dropzone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);"ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" style="min-height: 100vh">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-database"></i><?=$params["bucket"] ?> <?=$params["path"] ?>
<?php
if (!is_writable("/originals/".$params["bucket"].$params["path"])) echo('<i class="fas fa-lock ml-3" style="color: #d32f2f;"></i>');

?>
        
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            This week
          </button>
        </div>
      </div>

      <table class="table table-striped">
      
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
      case "png":
        echo('<i class="far fa-image"></i>'); break;
      case "mp4":
      case "webm":
        echo('<i class="far fa-film"></i>'); break;
      default: echo($datei->extension); break;
    }
    if ($datei->is_video) {
      echo('</td><td><a href="/bucket/'.$params["bucket"].$params["path"].$datei->basename.'?download=original" TARGET="_blank">'.$file.'</a></td>');
      echo('<td>'.$datei->md5.'</td>');
      echo('<td>');
      echo('<a href="/bucket/'.$params["bucket"].'/'.$datei->md5.'.0.z1920x1080.jpg" TARGET="_blank">img</a>');
      echo('<a href="/bucket/'.$params["bucket"].'/'.$datei->md5.'.embed.html" TARGET="_blank">embed</a>');
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
      
      </table>

      <div class="progress">
        <div id="uploadProgress2" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
      </div>

  </main>
<script>
function dragOverHandler(ev) {
  //console.log('File(s) in drop zone');

  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();
}

function dropHandler(ev) {
  console.log('File(s) dropped');

  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();

  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to access the file(s)
    fd = new FormData()
    for (var i = 0; i < ev.dataTransfer.items.length; i++) {

      $.each(ev.dataTransfer.files,function(i,v) {
            fd.append("file",v,v.name)
      });
      // If dropped items aren't files, reject them
      /*if (ev.dataTransfer.items[i].kind === 'file') {
        var file = ev.dataTransfer.items[i].getAsFile();
        console.log('... file[' + i + '].name = ' + file.name);
      }*/
      fd.append("url","drop")
      upload(fd)
    }
  } else {
    // Use DataTransfer interface to access the file(s)
    for (var i = 0; i < ev.dataTransfer.files.length; i++) {
      console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
    }
  }
}

function upload(fd){
    return $.ajax({
        xhr: function() {
          var xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener("progress", function(evt) {
            if (evt.lengthComputable) {
              var percentComplete = (evt.loaded / evt.total) * 100;
              console.log("progress", percentComplete);
              $("#uploadProgress2").css("width",percentComplete+"%").text(percentComplete.toFixed(1)+"%");
              //Do something with upload progress here
              }
            }, false);
          return xhr;
        },
        url:"?act=uploadDD",
        type:"POST",
        processData:false,
        contentType: false,
        data: fd,
        success:function(){ console.log("Uploaded.",arguments); document.location.href=document.location.href; },
        error:function(){ console.log("Error Uploading.",arguments) },
    });
}
</script>
<?php
    PageEngine::html("footer");
?>