<?php

namespace AppBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;

trait PdfRenderTrait
{
    protected function renderPdf($view, array $viewParameters = [], array $pdfOptions = [])
    {
        $html = $this->renderView($view, $viewParameters);

        return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html, $pdfOptions), 200, [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * @param string $view
     * @param array  $parameters
     *
     * @return string
     */
    abstract public function renderView($view, array $parameters = []);

    /**
     * @param string $id
     *
     * @return object
     */
    abstract public function get($id);
}
