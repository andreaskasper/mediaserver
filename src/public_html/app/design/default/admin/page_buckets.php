<?php
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

  mkdir("/originals/demo/", 0777, true);

  if (!file_exists($dir)) die("Ordner gibt es nicht");
  if (!is_writable($dir)) die("Ordner nicht beschreibbar");


  $files = scandir($dir);
  print_r($files);
  foreach ($files as $file) {
    if (substr($file,0,1) == ".") continue;
    if (!is_dir($dir.$file)) { echo($file); continue; }
    echo('<tr><td><a href="/'.$_ENV["lang"].'/admin/bucket/'.$file.'/"><i class="far fa-folder"></i> '.$file.'</a></td></tr>');
  }
?>
</table>
  </main>
<?php
    PageEngine::html("footer");
?>