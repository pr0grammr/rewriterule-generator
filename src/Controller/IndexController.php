<?php

namespace App\Controller;

use App\Form\CsvType;
use App\Service\Csv;
use App\Service\RewriteRuleGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     *
     * @param Request $request
     * @param RewriteRuleGenerator $rewriteRuleGenerator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, RewriteRuleGenerator $rewriteRuleGenerator)
    {
        /**
         * initialize some variables
         */
        $form = $this->createForm(CsvType::class);
        $form->handleRequest($request);
        $errorMessages = $this->getParameter('errors');
        $error = false;
        $filename = false;

        /**
         * upload CSV
         * check if file is valid CSV file
         *
         * parse CSV, add rewrite rules to generator
         * export txt file
         */
        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * create new CSV instance from request
             * move CSV to tmp directory
             */
            $csv = new Csv($request->files->get('csv')['csv_file']);

            if ($csv->isValid()) {

                $csv->moveToTmpDir($this->getParameter('csv_tmp_directory'));

                /**
                 * add rewriterules to generator
                 * set statuscode
                 * set rewriteengine
                 */
                $rewriteRuleGenerator->setRewriteRules($csv->getRewriteRules());

                /**
                 * check if any statuscode is set
                 */
                $formOptions = $request->request->get('csv');

                if (isset($formOptions['options'])) {

                    if ($formOptions['options'] == 'custom_code') {
                        $rewriteRuleGenerator->setStatusCode((int) $formOptions['custom_status_code']);
                    } elseif ((int) $formOptions['options'] == 301) {
                        $rewriteRuleGenerator->setStatusCode(301);
                    }
                }


                /**
                 * check if RewriteEngine On shall be included
                 */
                if (isset($formOptions['rewrite_engine'])) {
                    $rewriteRuleGenerator->setRewriteEngineOn(true);
                }

                $rewriteRuleGenerator->setFileTemplate($this->renderView('rewrites.html.twig', ['csv' => $rewriteRuleGenerator->toArray()]));

                /**
                 * write txt file with given name and template
                 * returns download link
                 *
                 * finally remove CSV file from tmp folder
                 */
                $filename = $rewriteRuleGenerator->exportFile($this->getParameter('csv_upload_directory'), $this->buildNewFileName());

                $csv->removeTmpFile();

                /**
                 * clear from after submission
                 */
                unset($form);
                $form = $this->createForm(CsvType::class);

            } else {
                $error = $errorMessages['invalid_extension'];
            }

        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'filename' => $filename
        ]);

    }

    /**
     * @Route("/download/{filename}", name="app_download")
     *
     * @param string $filename
     *
     * @return Response
     */
    public function download(string $filename)
    {
        $path = $this->getParameter('csv_upload_directory');
        $content = file_get_contents($path . '/' . $filename);

        $response = new Response();

        $response->headers->set('Content-type', 'text/plain');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');

        $response->setContent($content);
        return $response;
    }

    private function buildNewFileName()
    {
        $fileNameFormat = $this->getParameter('file_name_options');
        return sprintf("%s_%s.%s", $fileNameFormat['prefix'], date($fileNameFormat['date_format']), $fileNameFormat['extension']);
    }
}
