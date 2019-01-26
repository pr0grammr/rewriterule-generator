<?php

namespace App\Controller;

use App\Form\CsvType;
use App\Service\Csv;
use App\Service\RewriteRule;
use App\Service\RewriteRuleGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request, RewriteRuleGenerator $rewriteRuleGenerator, LoggerInterface $logger)
    {
        $form = $this->createForm(CsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * create new CSV instance from request
             * move CSV to tmp directory
             */
            $csv = new Csv($request->files->get('csv')['csv_file']);
            $csv->moveToTmpDir($this->getParameter('csv_tmp_directory'));

            /**
             * add rewriterules to generator
             * set statuscode
             * set rewriteengine
             */
            $rewriteRuleGenerator->setRewriteRules($csv->getRewriteRules());
            $rewriteRuleGenerator->setStatusCode(301); //TODO: get statuscode from form
            $rewriteRuleGenerator->setRewriteEngineOn(true); //TODO: get option from form

            $rewriteRuleGenerator->setFileTemplate($this->renderView('rewrites.html.twig', ['csv' => $rewriteRuleGenerator->toArray()]));

            /**
             * write txt file with given name and template
             * finally remove CSV file from tmp folder
             */
            $rewriteRuleGenerator->exportFile($this->getParameter('csv_upload_directory'), $this->buildNewFileName());

            $csv->removeTmpFile();
        }

        return $this->render('index/index.html.twig', ['form' => $form->createView()]);

    }

    private function buildNewFileName()
    {
        $fileNameFormat = $this->getParameter('file_name_options');
        return sprintf("%s_%s.%s", $fileNameFormat['prefix'], date($fileNameFormat['date_format']), $fileNameFormat['extension']);
    }
}
