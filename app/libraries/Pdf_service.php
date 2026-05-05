<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf_service
{
    public function __construct()
    {
        $autoload = ROOT_DIR . 'vendor/autoload.php';

        if (!class_exists(Dompdf::class) && is_file($autoload)) {
            require_once $autoload;
        }

    }

    public function download($view, array $data, $filename)
    {
        if (!class_exists(Dompdf::class)) {
            throw new RuntimeException('Dompdf is not installed. Run composer install before generating PDFs.');
        }

        $html = $this->renderView($view, array_merge([
            'brand_logo_data_uri' => $this->brandLogoDataUri(),
            'generated_at' => date('Y-m-d H:i:s'),
        ], $data));

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->setChroot(ROOT_DIR);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        $dompdf->stream($this->pdfFilename($filename), ['Attachment' => true]);
        exit;
    }

    private function renderView($view, array $data)
    {
        $view = trim((string) $view, '/');
        $view_path = APP_DIR . 'views/' . $view . '.php';

        if (!is_file($view_path)) {
            throw new RuntimeException('PDF template not found: ' . $view);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $view_path;
        return ob_get_clean();
    }

    private function brandLogoDataUri()
    {
        $path = ROOT_DIR . 'public/assets/images/branding/logo-primary.png';

        if (!is_file($path) || !is_readable($path)) {
            return '';
        }

        $contents = file_get_contents($path);

        if ($contents === false || $contents === '') {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode($contents);
    }

    private function pdfFilename($filename)
    {
        $filename = safe_download_filename($filename, 'document.pdf');
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '_', $filename);

        if (strtolower(substr($filename, -4)) !== '.pdf') {
            $filename .= '.pdf';
        }

        return $filename;
    }
}
