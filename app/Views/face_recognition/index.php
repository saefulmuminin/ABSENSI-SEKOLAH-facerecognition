<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <script src="<?= base_url('public/assets/js/face-api.min.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        /* Background Gradient */
        body {
            background-image: url('public/assets/siswa.svg');
            min-height: 100vh;
        }

        /* Animasi fade-in dan fade-out */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .fade-out {
            animation: fadeOut 0.5s ease-in-out;
        }

        /* Video Container */
        .video-container {
            position: relative;
            width: 640px;
            height: 480px;
            margin: 0 auto;
            border: 4px solid #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        #video,
        #overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        #overlay {
            z-index: 10;
            /* Pastikan canvas di atas video */
        }
    </style>
</head>

<body>
<div class="container flex justify-center items-center min-h-screen ">
    
    <div class="grid grid-cols-12 px-4">
        <!-- Video Container -->
        <div class="col-span-12 flex justify-center mx-auto">
            <div class="video-container text-center">
                <video id="video" width="640" height="480" autoplay></video>
                <canvas id="overlay"></canvas>
                <div id="messageDiv" class="mt-4 text-lg font-semibold hidden fade-in"></div>
                <div id="hasilScan" class="mt-4 text-lg"></div>
                <div id="infoCard" class="mt-6 hidden">
                    <div class="info-card max-w-sm mx-auto p-6 bg-white rounded-lg shadow-lg">
                        <h2 class="text-2xl font-bold" id="infoNama"></h2>
                        <p class="text-gray-700" id="infoNUPTK"></p>
                        <p class="text-gray-700" id="infoStatus"></p>
                    </div>
                </div>
                <div id="loadingIndicator" class="mt-6 text-center hidden">
                    <div class="inline-flex items-center">
                        <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="ml-2 text-white">Memproses...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


 


    <!-- Tambahkan elemen audio untuk suara saat absen berhasil -->
    <audio id="successSound" src="<?= base_url('public/assets/audio/beep.mp3') ?>"></audio>

    <script>
        let videoStream;
        const video = document.getElementById('video');
        const overlay = document.getElementById('overlay');
        const messageDiv = document.getElementById('messageDiv');
        const hasilScan = document.getElementById('hasilScan');
        const infoCard = document.getElementById('infoCard');
        const infoNama = document.getElementById('infoNama');
        const infoNIS = document.getElementById('infoNIS');
        const infoStatus = document.getElementById('infoStatus');
        const loadingIndicator = document.getElementById('loadingIndicator');
        let processedNIS = new Set(); // Untuk menyimpan NIS yang sudah diproses

        const siswaList = <?= json_encode($siswaList) ?>;
        console.log('Daftar Siswa:', siswaList);

        async function loadModels() {
            try {
                loadingIndicator.classList.remove('hidden'); // Tampilkan loading indicator
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri("public/assets/models"),
                    faceapi.nets.faceRecognitionNet.loadFromUri("public/assets/models"),
                    faceapi.nets.faceLandmark68Net.loadFromUri("public/assets/models"),
                    faceapi.nets.ageGenderNet.loadFromUri("public/assets/models"),
                    faceapi.nets.faceExpressionNet.loadFromUri("public/assets/models"),
                ]);
                console.log("Models loaded successfully");
            } catch (error) {
                console.error("Error loading models:", error);
                showMessage('Gagal memuat model. Periksa konsol untuk detailnya.');
            } finally {
                loadingIndicator.classList.add('hidden'); // Sembunyikan loading indicator
            }
        }

        async function startWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                videoStream = stream;
            } catch (error) {
                console.error('Error accessing webcam:', error);
                showMessage('Tidak dapat mengakses webcam. Pastikan Anda mengizinkan akses kamera.');
            }
        }

        async function detectFaces() {
            try {
                const labeledFaceDescriptors = await getLabeledFaceDescriptions();
                const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

                const displaySize = { width: video.width, height: video.height };
                faceapi.matchDimensions(overlay, displaySize);

                setInterval(async () => {
                    const detections = await faceapi.detectAllFaces(video)
                        .withFaceLandmarks()
                        .withFaceExpressions()
                        .withAgeAndGender()
                        .withFaceDescriptors();
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);

                    // Bersihkan canvas sebelum menggambar ulang
                    const context = overlay.getContext('2d');
                    context.clearRect(0, 0, overlay.width, overlay.height);

                    // Gambar landmark wajah
                    faceapi.draw.drawFaceLandmarks(overlay, resizedDetections);

                    const results = resizedDetections.map((d) => faceMatcher.findBestMatch(d.descriptor));

                    results.forEach((result, i) => {
                        const box = resizedDetections[i].detection.box;
                        const label = `${result.label} | Ekspresi: ${resizedDetections[i].expressions.asSortedArray()[0].expression} | Usia: ${Math.round(resizedDetections[i].age)} tahun`;
                        const drawBox = new faceapi.draw.DrawBox(box, { label });
                        drawBox.draw(overlay);
                    });

                    // Ambil NIS dari hasil deteksi wajah
                    if (results.length > 0) {
                        const recognizedNIS = results.map((result) => result.label.split(' - ')[0]);
                        console.log('NIS yang dikenali:', recognizedNIS[0]);

                        // Cek apakah NIS sudah diproses
                        if (!processedNIS.has(recognizedNIS[0])) {
                            processedNIS.add(recognizedNIS[0]); // Tandai NIS sebagai sudah diproses
                            cekData(recognizedNIS[0]); // Kirim data ke server
                        } else {
                            // Jika NIS sudah diproses, tampilkan pesan "Anda sudah absen hari ini"
                            showMessage('Anda sudah absen hari ini');
                        }
                    }
                }, 100);
            } catch (error) {
                console.error('Error in detectFaces:', error);
                showMessage('Gagal memproses wajah. Pastikan data siswa dan model sudah benar.');
            }
        }

        async function getLabeledFaceDescriptions() {
            const labeledDescriptors = [];

            for (const siswa of siswaList) { // Gunakan daftar siswa dari PHP
                const descriptions = [];
                for (let i = 1; i <= 5; i++) {
                    try {
                        const img = await faceapi.fetchImage(`uploads/siswa_images/${siswa.nis}/${i}.png`);
                        const detections = await faceapi.detectSingleFace(img)
                            .withFaceLandmarks()
                            .withFaceDescriptor();
                        if (detections) {
                            descriptions.push(detections.descriptor);
                        }
                    } catch (error) {
                        console.error(`Error processing ${siswa.nis}/${i}.png:`, error);
                    }
                }
                if (descriptions.length > 0) {
                    // Gabungkan NIS, nama, dan usia siswa sebagai label
                    const label = `${siswa.nis} - ${siswa.nama_siswa} (${siswa.usia} tahun)`;
                    labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(label, descriptions));
                }
            }

            if (labeledDescriptors.length === 0) {
                throw new Error("Tidak ada deskripsi wajah yang valid.");
            }

            return labeledDescriptors;
        }

        async function cekData(nis) {
            try {
                loadingIndicator.classList.remove('hidden'); // Tampilkan loading indicator
                const response = await jQuery.ajax({
                    url: "<?= base_url('face-recognition/cekWajah'); ?>",
                    type: 'post',
                    data: { 'nis': nis, 'waktu': '<?= strtolower($waktu); ?>' }
                });

                console.log(response);

                if (response.error) {
                    // Tampilkan pesan error dari server
                    $('#hasilScan').html(response.error);
                    showMessage(response.error);
                } else {
                    // Tampilkan pesan sukses
                    $('#hasilScan').html(response.message);
                    showMessage('Absen berhasil!');

                    // Mainkan suara saat absen berhasil
                    const successSound = document.getElementById('successSound');
                    successSound.play(); // Memainkan suara
                }
            } catch (error) {
                console.error('Error in cekData:', error);
                $('#hasilScan').html('Terjadi kesalahan: ' + error.message);
            } finally {
                loadingIndicator.classList.add('hidden'); // Sembunyikan loading indicator
            }
        }

        function showMessage(message) {
            messageDiv.textContent = message;
            messageDiv.classList.remove('hidden', 'fade-out');
            messageDiv.classList.add('fade-in');
            setTimeout(() => {
                messageDiv.classList.remove('fade-in');
                messageDiv.classList.add('fade-out');
                setTimeout(() => {
                    messageDiv.classList.add('hidden');
                }, 500);
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadModels();
            await startWebcam();
            detectFaces();
        });
    </script>
</body>

</html>