
// Inisialisasi variabel global
let videoStream;
const video = document.getElementById('video');
const startButton = document.getElementById('startButton');
const endAttendanceButton = document.getElementById('endAttendance');
const messageDiv = document.getElementById('messageDiv');

// Load model face-api.js
Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri('/assets/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/assets/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/assets/models'),
]).then(() => {
    console.log('Model loaded successfully');
}).catch((error) => {
    console.error('Error loading models:', error);
});

// Mulai webcam
startButton.addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        videoStream = stream;
        startButton.style.display = 'none';
        endAttendanceButton.style.display = 'block';
    } catch (error) {
        console.error('Error accessing webcam:', error);
        showMessage('Tidak dapat mengakses webcam. Pastikan Anda mengizinkan akses kamera.');
    }
});

// Deteksi wajah
video.addEventListener('play', async () => {
    const labeledFaceDescriptors = await getLabeledFaceDescriptions();
    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

    const canvas = faceapi.createCanvasFromMedia(video);
    document.querySelector('.video-container').appendChild(canvas);
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    setInterval(async () => {
        const detections = await faceapi.detectAllFaces(video)
            .withFaceLandmarks()
            .withFaceDescriptors();
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

        const results = resizedDetections.map((d) => faceMatcher.findBestMatch(d.descriptor));
        results.forEach((result, i) => {
            const box = resizedDetections[i].detection.box;
            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
            drawBox.draw(canvas);
        });
    }, 100);
});

// Ambil deskripsi wajah dari gambar yang sudah di-label
async function getLabeledFaceDescriptions() {
    const labels = ['111']; // Ganti dengan NIS siswa yang sesuai
    const labeledDescriptors = [];

    for (const label of labels) {
        const descriptions = [];
        for (let i = 1; i <= 5; i++) {
            try {
                const img = await faceapi.fetchImage(`/uploads/siswa_image/${label}/${i}.png`);
                const detections = await faceapi.detectSingleFace(img)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                descriptions.push(detections.descriptor);
            } catch (error) {
                console.error(`Error processing ${label}/${i}.png:`, error);
            }
        }
        labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(label, descriptions));
    }

    return labeledDescriptors;
}

// Tampilkan pesan
function showMessage(message) {
    messageDiv.style.display = 'block';
    messageDiv.innerHTML = message;
    setTimeout(() => {
        messageDiv.style.opacity = '0';
    }, 5000);
}

// Hentikan webcam
endAttendanceButton.addEventListener('click', () => {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        videoStream = null;
        endAttendanceButton.style.display = 'none';
        startButton.style.display = 'block';
        showMessage('Absensi selesai.');
    }
});
