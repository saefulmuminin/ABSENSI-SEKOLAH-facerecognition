<!DOCTYPE html>
<html lang="en">

<?= $this->include('templates/head') ?>
<style>

.custom-img {
    transition: transform 0.3s ease-in-out;
  }

  .custom-img:hover {
    transform: scale(1.1); /* Membesar saat hover */
  }

  .custom-img:active {
    transform: scale(0.9); /* Mengecil saat diklik */
  }
  .image-box {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.image-container {
    text-align: center;
    position: relative;
}

.image-container img {
    max-width: 100px;
    max-height: 100px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.edit-icon {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.5);
    padding: 5px;
    border-radius: 50%;
    cursor: pointer;
}

.edit-icon i {
    color: white;
    font-size: 16px;
}
</style>
<body>
  <div>
    <?= $this->include('templates/sidebar') ?>
    <div class="main-panel">

      <?= $this->include('templates/navbar') ?>

      <?= $this->renderSection('content') ?>

      <?= $this->include('templates/footer') ?>

      <!-- komentar jika tidak dipakai -->
      <?php
      // echo $this->include('templates/fixed_plugin') 
      ?>

    </div>
  </div>

  <script>

    
    // Fungsi untuk membuka kamera dan mengganti gambar
    function openCamera(buttonId) {
        navigator.mediaDevices
            .getUserMedia({
                video: true
            })
            .then((stream) => {
                const video = document.createElement("video");
                video.srcObject = stream;
                document.body.appendChild(video);

                video.play();

                setTimeout(() => {
                    const capturedImage = captureImage(video);
                    stream.getTracks().forEach((track) => track.stop());
                    document.body.removeChild(video);

                    const imgElement = document.getElementById(
                        buttonId + "-captured-image"
                    );
                    imgElement.src = capturedImage;

                    const hiddenInput = document.getElementById(
                        buttonId + "-captured-image-input"
                    );
                    hiddenInput.value = capturedImage;
                }, 500);
            })
            .catch((error) => {
                console.error("Error accessing webcam:", error);
            });
    }

    // Fungsi untuk mengambil beberapa gambar
    const takeMultipleImages = async () => {
        document.getElementById("open_camera").style.display = "none";

        const images = document.getElementById("multiple-images");

        for (let i = 1; i <= 5; i++) {
            // Buat container untuk gambar
            const imageBox = document.createElement("div");
            imageBox.classList.add("image-box");

            const imgElement = document.createElement("img");
            imgElement.id = `image_${i}-captured-image`;

            const editIcon = document.createElement("div");
            editIcon.classList.add("edit-icon");

            const icon = document.createElement("i");
            icon.classList.add("fas", "fa-camera");
            icon.setAttribute("onclick", `openCamera("image_"+${i})`);

            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.id = `image_${i}-captured-image-input`;
            hiddenInput.name = `capturedImage${i}`;

            editIcon.appendChild(icon);
            imageBox.appendChild(imgElement);
            imageBox.appendChild(editIcon);
            imageBox.appendChild(hiddenInput);
            images.appendChild(imageBox);

            // Ambil gambar dengan jeda
            await captureImageWithDelay(i);
        }
    };

    // Fungsi untuk menangkap gambar dengan jeda
    const captureImageWithDelay = async (i) => {
        try {
            // Akses kamera
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });
            const video = document.createElement("video");
            video.srcObject = stream;
            document.body.appendChild(video);
            video.play();

            // Tunggu 500ms sebelum mengambil gambar
            await new Promise((resolve) => setTimeout(resolve, 500));

            // Ambil gambar
            const capturedImage = captureImage(video);

            // Hentikan stream dan hapus elemen video
            stream.getTracks().forEach((track) => track.stop());
            document.body.removeChild(video);

            // Perbarui gambar dan input hidden
            const imgElement = document.getElementById(`image_${i}-captured-image`);
            imgElement.src = capturedImage;

            const hiddenInput = document.getElementById(
                `image_${i}-captured-image-input`
            );
            hiddenInput.value = capturedImage;
        } catch (err) {
            console.error("Error accessing camera: ", err);
        }
    };

    // Fungsi untuk mengonversi gambar dari video ke base64
    function captureImage(video) {
        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext("2d");

        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        return canvas.toDataURL("image/png");
    }
</script>


</body>

</html>