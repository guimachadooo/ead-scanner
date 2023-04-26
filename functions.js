import QrScanner from "./qr-scanner.min.js";

const video = document.getElementById('qr-video');
const videoContainer = document.getElementById('video-container');
const camHasCamera = document.getElementById('cam-has-camera');
const camList = document.getElementById('cam-list');
const scanRegion = document.getElementById('scan-region-highlight-style-select');
const showScanRegion = document.getElementById('show-scan-region');
const startButton = document.getElementById('start-button');
const stopButton = document.getElementById('stop-button');
const sendButton = document.getElementById('send-button');
const configButton = document.getElementById('config-button');
const sendData = document.getElementById('send-data');
const switchTrial = document.getElementById('flexSwitchCheckDefault');
const cancelData = document.getElementById('cancel-data');
const camQrResult = document.getElementById('cam-qr-result');
const inversionMode = document.getElementById('inversion-mode-select');
const showToast = document.getElementById('toast-success');
const camQrResultTimestamp = document.getElementById('cam-qr-result-timestamp');
const fileSelector = document.getElementById('file-selector');
const camHasFlash = document.getElementById('cam-has-flash');
const flashToggle = document.getElementById('flash-toggle');
const flashState = document.getElementById('flash-state');
const fileQrResult = document.getElementById('file-qr-result');
let toast = new bootstrap.Toast(showToast);
let qrData;

window.onload = function() {
  scanner.stop();
  videoContainer.style.display = 'none';
  stopButton.style.display = 'none';
  sendButton.style.display = 'block';
  localStorage.setItem("trial", false);
  localStorage.setItem("user", window.location.hash);
};

function convertToJson(result){
  let jsonVcard = {};
  let vCardData = result.data.split("\n");

  vCardData.map((item, i) => { 
    let field = item.split(":");
    jsonVcard[field.shift()] = field.join(":");
  })

  return jsonVcard;
}

function setResult(label, result) {
  console.log(result.data);
  //label.textContent = convertToJson(result);
  //camQrResultTimestamp.textContent = new Date().toString();
  
  label.style.color = 'teal';
  clearTimeout(label.highlightTimeout);
  label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);
  
  sendButton.style.display = 'block';
  videoContainer.style.display = "none";
  stopButton.style.display = 'none';
  configButton.style.display = 'none';
  qrData = convertToJson(result);
  
  //label.textContent = "OK!"
  scanner.stop();
}

// ####### Web Cam Scanning #######

const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
  onDecodeError: error => {
    camQrResult.textContent = "---"; //error;
    camQrResult.style.color = 'inherit';
    camQrResult.style.fontSize = '40px';
  },
  highlightScanRegion: true,
  highlightCodeOutline: true,
});

scanner.start().then(() => {
  //updateFlashAvailability();
  // List cameras after the scanner started to avoid listCamera's stream and the scanner's stream being requested
  // at the same time which can result in listCamera's unconstrained stream also being offered to the scanner.
  // Note that we can also start the scanner after listCameras, we just have it this way around in the demo to
  // start the scanner earlier.
  QrScanner.listCameras(true).then(cameras => cameras.forEach(camera => {
    const option = document.createElement('option');
    option.value = camera.id;
    option.text = camera.label;
    camList.add(option);
  }));

  videoContainer.style.display = 'block';
});

QrScanner.hasCamera().then((hasCamera) => {
  camHasCamera.textContent = hasCamera;
});

// for debugging
window.scanner = scanner;

scanRegion.addEventListener('change', (e) => {
  videoContainer.className = e.target.value;
  scanner._updateOverlay(); // reposition the highlight because style 2 sets position: relative
});

showScanRegion.addEventListener('change', (e) => {
  const input = e.target;
  const label = input.parentNode;
  label.parentNode.insertBefore(scanner.$canvas, label.nextSibling);
  scanner.$canvas.style.display = input.checked ? 'block' : 'none';
});

startButton.addEventListener('click', () => {
  scanner.start();
  video.style.display = "block";
  videoContainer.style.display = "block";
  videoContainer.style.padding = "em";
  stopButton.style.display = 'block';
  startButton.style.display = 'none';
});

stopButton.addEventListener('click', () => {
  scanner.stop();
  video.style.display = "none";
  videoContainer.style.display = "none";
  startButton.style.display = 'block';
  stopButton.style.display = 'none';
});

sendButton.addEventListener('click', () => {
  startButton.style.display = 'block';
  sendButton.style.display = 'block';
}); 

switchTrial.addEventListener('click', (event, state) => {
  localStorage.setItem("trial", event.srcElement.checked);
});

sendData.addEventListener('click', () => {
  let token = "09d226908be059315316d6f66a99a4e4";
  let address = `https://eadmin.eadplataforma.com/eadScanner.php?token=${token}`;

  let form = new URLSearchParams();
  let trial = localStorage.getItem('trial');
  let user = localStorage.getItem('user');

  form.append('trial', trial);
  form.append('user', user);
  
  for(let i in qrData){
    form.append(i.toUpperCase(), qrData[i]);
  }

  //form.append("data", JSON.stringify(qrData));
  let xhr = new XMLHttpRequest();

  xhr.open("POST", address);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = () => {
    if (xhr.status >= 200 && xhr.status < 300) {
      console.log(xhr.response)
      toast.show();
    }
  }

  xhr.send(form);
});

cancelData.addEventListener('click', () => {
  /* setTimeout(() => {
    window.location.reload();
  }, 200); */
});

/* inversionMode.addEventListener('change', event => {
  scanner.setInversionMode(event.target.value);
}); */

/* const updateFlashAvailability = () => {
  scanner.hasFlash().then(hasFlash => {
    camHasFlash.textContent = hasFlash;
    flashToggle.style.display = hasFlash ? 'inline-block' : 'none';
  });
}; */

/* camList.addEventListener('change', event => {
  scanner.setCamera(event.target.value).then(updateFlashAvailability);
}); */

/* flashToggle.addEventListener('click', () => {
  scanner.toggleFlash().then(() => flashState.textContent = scanner.isFlashOn() ? 'on' : 'off');
}); */

// ####### File Scanning #######

/* fileSelector.addEventListener('change', event => {
  const file = fileSelector.files[0];
  if (!file) {
    return;
  }
  QrScanner.scanImage(file, { returnDetailedScanResult: true })
    .then(result => setResult(fileQrResult, result))
    .catch(e => setResult(fileQrResult, { data: e || 'No QR code found.' }));
}); */