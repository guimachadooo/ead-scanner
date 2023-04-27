<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>EAD Scanner</title>
  <link rel="stylesheet" href="./style.css?v=1">
  <link rel="icon" type="image/x-icon" href="https://eadpub.s3.amazonaws.com/assets/read-qrcode/favicon.png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    integrity="sha384-b6lVK+yci+bfDmaY1u0zE8YYJt0TZxLEAFyYSLHId4xoVvsrQu3INevFKo+Xir8e" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css"
    rel="stylsheet">
    <?php
      $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      if (strpos($url,'gabi') !== false):
        $manifest = 'gabi';
      elseif (strpos($url,'alisson') !== false):
        $manifest = 'alisson';
      elseif (strpos($url,'gui') !== false):
        $manifest = 'gui';
      elseif (strpos($url,'paulo') !== false):
        $manifest = 'paulo';
      elseif (strpos($url,'julia') !== false):
        $manifest = 'julia';
      elseif (strpos($url,'caio') !== false):
        $manifest = 'caio';
      elseif (strpos($url,'fer') !== false):
        $manifest = 'fer';
      elseif (strpos($url,'fabio') !== false):
          $manifest = 'fabio';
      else:
        $manifest = 'manifest';
      endif;
      
    ?>
    <link rel="manifest" href="./<?php echo $manifest; ?>.webmanifest" crossorigin="use-credentials">
    <link rel="canonical" href="<?php echo $url; ?>" />

</head>

<body>
  <div class="container text-center content">
    <row>
      <col class="col-md-12">
          <img alt="ead-scanner" class="logo"
            src="https://cdn.eadplataforma.app/assets/read-qrcode/logo-ead-scanner-branco.webp" />

          <div class="space"></div>

          <img class="logo-afiliados" alt="afiliados-brasil" src="https://cdn.eadplataforma.app/assets/read-qrcode/logo-afiliados.webp" />
      </col>
    </row>

    <row>
      <col class="col-md-12">
        <div id="video-container">
          <video id="qr-video"></video>
        </div>
      </col>
    </row>
    <div class="row">
        <div class="col-md-12">
          <div class="actions">
            <button id="start-button" class="text-center" style="line-height:100%;padding:0;margin:0">
              <i class="bi bi-qr-code-scan"></i>
            </button>
      
            <button id="stop-button" class="text-center" style="line-height:100%;padding:0;margin:0">
              <i class="bi bi-camera-video-off"></i>
            </button>
      
            <button type="button" id="send-button" class="text-center" data-bs-toggle="modal" data-bs-target="#modal-confirm" style="line-height:100%;padding:0;margin:0">
              <i class="bi bi-send-check"></i>
            </button>
      
            <button type="button" id="config-button" class="text-center" data-bs-toggle="modal" data-bs-target="#modal-config">
              <i class="bi bi-gear"></i>
            </button>
          </div>
      
          <span id="cam-qr-result">---</span>
        </div>
    </div>    
  </div>

  <!-- Modal Confirm -->
  <div class="modal fade" id="modal-confirm" tabindex="-1" aria-labelledby="modal-confirm" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="confirm-content">
            <span>Os dados serão enviados.</span>

            <div class="space"></div>

            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
              <label class="form-check-label" for="flexSwitchCheckDefault">Criar conta?</label>
            </div>
          </div>

          <div class="space"></div>

          <div class="modal-actions">
            <div class="row">
              <div class="col-md-6">
                <button id="cancel-data" type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
              </div>
              <div class="col-md-6">
                <button id="send-data" type="button" class="btn btn-success" data-bs-dismiss="modal">Enviar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Config -->
  <div class="modal fade" id="modal-config" tabindex="-1" aria-labelledby="modal-config" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="config-content">
            <div id="cam-box">
              <label>
                <span>Câmera:</span>
                <select id="cam-list">
                  <option value="environment" selected>Padrão</option>
                  <option value="user">Frontal</option>
                </select>
              </label>
      
              <span id="cam-has-flash"></span>
      
              <div>
                <button id="flash-toggle"><span id="flash-state">off</span></button>
              </div>
            </div>

            <div class="space"></div>

            <div id="style-box">
              <label>
                <span>Estilo do scan:</span>
                <select id="scan-region-highlight-style-select">
                  <option value="default-style">Amarelo</option>
                  <option value="example-style-1">Azul</option>
                  <option value="example-style-2">Com foco</option>
                </select>
              </label>
      
              <label>
                <input id="show-scan-region" type="checkbox">
                <span class="region-label">Exibir área do scan</span>
              </label>
            </div>

            <div class="space"></div>
  
            <div id="inversion-box">
              <span>Tipo do scan: </span>
              <select id="inversion-mode-select">
                <option value="original">QR Original</option>
                <option value="invert">QR Invertido</option>
                <option value="both">Ambos</option>
              </select>
            </div>
  
            <div class="space"></div>
          </div>

          <div class="modal-actions">
            <div class="row">
              <div class="col-md-12">
                <button id="close-config" type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div 
    role="alert" 
    id="toast-success" 
    aria-atomic="true"
    aria-live="assertive"
    class="toast text-white bg-primary border-0 toast-container position-absolute top-0 end-0 p-3" 
  >
    <div class="d-flex">
      <div class="toast-body">
        Feito!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto toast-button" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>

  <div 
    role="alert" 
    id="toast-danger" 
    aria-atomic="true"
    aria-live="assertive"
    class="toast text-white bg-primary border-0 toast-container position-absolute top-0 end-0 p-3" 
  >
    <div class="d-flex">
      <div class="toast-body">
        Erro
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto toast-button" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>

  <!--<script src="../qr-scanner.umd.min.js"></script>-->
  <!--<script src="../qr-scanner.legacy.min.js"></script>-->
  <script type="module" src="./functions.js?v=1.34"></script>
  <script type="module" src="./serviceWorker.js?v=1.4"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"
    type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

    <script>
      self.addEventListener("fetch", function(event) {
        console.log(`start server worker`)
      });
    </script>
</body>

</html>