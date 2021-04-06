<?php

    $default_converters = array(
      "jpg_thumbnail0" => "Thumbnail (middle)",
      "jpg_thumbnail1" => "Thumbnail (25 Previews)",
      "mp4_1080p" => "1080p mp4 (h264/aac)",
      "mp4_480p" => "480p mp4 (h264/aac)",
      "mp4_240p" => "240p mp4 (h264/aac)",
      "webm_1080p" => "1080p webm (VP9 Vorbis)",
      "webm_480p" => "480p webm (VP9 Vorbis)",
      "webm_240p" => "240p webm (VP9 Vorbis)",
      "torrent0" => "Torrent for download",
      "embedd01" => "Embeddable Videocontainer (Standard)"
    );

    if (($_POST["act"] ?? "") == "save") {
      $json = @json_decode(@file_get_contents("/config/config.json"),true);
      if (!is_array($json)) $json = array();

      foreach ($default_converters as $k => $v) {
        $json["convert"]["default"][$k] = (($_POST["conv_".$k] ?? 0) == 1);
      }


      file_put_contents("/config/config.json", json_encode($json));
    }


    $json = @json_decode(@file_get_contents("/config/config.json"),true);
    PageEngine::html("header");
?>
  <main id="dropzone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);"ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-cog"></i> Settings</h1>
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

    <form method="POST">
      <INPUT type="hidden" name="act" value="save"/>

      <div class="form-group row">
        <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="staticEmail" value="email@example.com"/>
        </div>
      </div>

      <h3>Default Convertings</h3>
      <table class="table table-striped">
<?php
foreach ($default_converters as $k => $v) {
  echo('<tr>
  <td><INPUT type="checkbox" class="form-control" name="conv_'.$k.'" '.((($json["convert"]["default"][$k] ?? false) == true)?'CHECKED="CHECKED"':'').' value="1"/></td>
  <td>'.$v.'</td>
</tr>');
}
?>
      </table>
      <button class="btn btn-primary">save</button>
    </form>


<?php
print_r($json);
?>




  </main>
<?php
    PageEngine::html("footer");
?>