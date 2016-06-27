<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\NativeRequestHandler;

abstract class BaseController extends Controller
{
    /**
     * @param FormInterface $form
     * @param \Closure      $onValid
     * @param Request|null  $request
     *
     * @return array
     */
    protected function submit(FormInterface $form, \Closure $onValid, Request $request = null)
    {
        if (!$request && !$this->hasNativeRequestHandler($form)) {
            $request = $this->get('request_stack')->getCurrentRequest();
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $onValid($form->getData());
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @return bool
     */
    private function hasNativeRequestHandler(FormInterface $form)
    {
        return $form->getConfig()->getRequestHandler() instanceof NativeRequestHandler;
    }
}
