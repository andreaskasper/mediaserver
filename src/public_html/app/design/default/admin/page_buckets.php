<?php
  if (($_POST["act"] ?? "") == "createbucket") {
    if (!preg_match("@^[a-zA-Z0-9]+$@", trim($_POST["new_bucket_name"]))) die("ungültiger Name");
    if (file_exists("/originals/".trim($_POST["new_bucket_name"]))) die("Ding existiert bereits");
    mkdir("/originals/".trim($_POST["new_bucket_name"]),0777);
    header("Location: ?");
  }


    PageEngine::html("header");
?>
  <main id="dropzone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);"ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-database"></i> Buckets</h1>
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

  $dir = "/originals/";

  //mkdir("/originals/demo/", 0777, true);

  if (!file_exists($dir)) die("Ordner gibt es nicht");
  if (!is_writable($dir)) die("Ordner nicht beschreibbar");


  $files = scandir($dir);
  foreach ($files as $file) {
    if (substr($file,0,1) == ".") continue;
    if (!is_dir($dir.$file)) { echo($file); continue; }
    echo('<tr><td><i class="far fa-folder"></i></td><td><a href="/'.$_ENV["lang"].'/admin/bucket/'.$file.'/">'.$file.'</a></td><td><button class="btn btn-link"><i class="fas fa-ellipsis-v"></i></button></td></tr>');
  }
?>
  <tr><td><i class="far fa-plus-circle"></i></td><td>
  <form method="POST">
  <INPUT type="hidden" name="act" value="createbucket"/>
  <div class="input-group">
  <input type="text" class="form-control" name="new_bucket_name" placeholder="Bucket" aria-label="Bucket name" aria-describedby="basic-addon2"/>
  <div class="input-group-append">
    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-folder-plus"></i> Create</button>
  </div>
</div></form>

</table>
  </main>
<?php
    PageEngine::html("footer");
?>