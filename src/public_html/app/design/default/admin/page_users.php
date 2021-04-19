<?php

    $json_config = @json_decode(@file_get_contents("/config/config.json"),true);


    PageEngine::html("header");
?>
  <main id="dropzone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);"ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-database"></i> Users</h1>
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
      foreach (($json_config["users"] ?? array()) as $row) {
        echo('<tr>');
        echo('<td><i class="fas fa-user" style="font-size:1.5rem"></i></td>');
        echo('<td>'.($row["id"] ?? "").'</td>');
        echo('<td><INPUT type="password" class="form-control"/></td>');
        echo('</tr>');
      }
  echo('<tr>');
  echo('<td><i class="fas fa-user-plus" style="font-size:1.5rem"></i></td>');
  echo('<td><INPUT type="text" class="form-control"/></td>');
  echo('<td><INPUT type="password" class="form-control"/></td>');
  echo('</tr>');
?>
      </table>
  </main>
<?php
    PageEngine::html("footer");
?>