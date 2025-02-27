<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QRGenerator extends BaseController
{
    protected QrCode $qrCode;
    protected PngWriter $writer;
    protected Logo $logo;
    protected Label $label;
    protected Font $labelFont;
    protected Color $foregroundColor;
    protected Color $backgroundColor;

    protected string $relativePath;
    protected string $qrCodeFilePath;

    public function __construct()
    {
        $this->relativePath = ROOTPATH;
        $this->qrCodeFilePath = 'public/uploads/';

        if (!file_exists($this->relativePath . $this->qrCodeFilePath)) {
            mkdir($this->relativePath . $this->qrCodeFilePath, 0777, true);
        }

        $this->writer = new PngWriter();
        $this->labelFont = new Font($this->relativePath . 'public/assets/fonts/Roboto-Medium.ttf', 14);

        $this->foregroundColor = new Color(44, 73, 162);
        $this->backgroundColor = new Color(255, 255, 255);

        // Create logo
        $logoPath = $this->relativePath . 'public/assets/img/logo_sekolah.png';
        if (file_exists($logoPath)) {
            $this->logo = Logo::create($logoPath)->setResizeToWidth(75);
        } else {
            $this->logo = null;
        }

        $this->label = Label::create('')
            ->setFont($this->labelFont)
            ->setTextColor($this->foregroundColor);

        $this->qrCode = QrCode::create('')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor($this->foregroundColor)
            ->setBackgroundColor($this->backgroundColor);
    }

    public function generateQrSiswa()
    {
        $kelas = url_title($this->request->getVar('kelas'), '-', true);
        $this->qrCodeFilePath .= 'qr-siswa/' . $kelas . '/';

        if (!file_exists($this->relativePath . $this->qrCodeFilePath)) {
            mkdir($this->relativePath . $this->qrCodeFilePath, 0777, true);
        }

        $nama = $this->request->getVar('nama');
        $nomor = $this->request->getVar('nomor');
        $unique_code = $this->request->getVar('unique_code');

        $filename = $this->generate($nama, $nomor, $unique_code);

        return $this->response->setJSON([
            'success' => true,
            'qr_code_url' => base_url($this->qrCodeFilePath . $filename)
        ]);
    }

    public function generateQrGuru()
    {
        $this->qrCode->setForegroundColor(new Color(28, 101, 90));
        $this->label->setTextColor(new Color(28, 101, 90));

        $this->qrCodeFilePath .= 'qr-guru/';

        if (!file_exists($this->relativePath . $this->qrCodeFilePath)) {
            mkdir($this->relativePath . $this->qrCodeFilePath, 0777, true);
        }

        $nama = $this->request->getVar('nama');
        $nomor = $this->request->getVar('nomor');
        $unique_code = $this->request->getVar('unique_code');

        $filename = $this->generate($nama, $nomor, $unique_code);

        return $this->response->setJSON([
            'success' => true,
            'qr_code_url' => base_url($this->qrCodeFilePath . $filename)
        ]);
    }

    protected function generate($nama, $nomor, $unique_code)
    {
        $filename = url_title($nama, '-', true) . "_" . url_title($nomor, '-', true) . '.png';

        // Set QR code data
        $this->qrCode->setData($unique_code);
        $this->label->setText($nama);

        // Write QR code to file
        $result = $this->writer->write($this->qrCode, $this->logo, $this->label);
        $result->saveToFile($this->relativePath . $this->qrCodeFilePath . $filename);

        return $filename;
    }
}
