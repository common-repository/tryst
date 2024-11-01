<?php

/* 
* Every mail from Tryst should accept the contract
*/

namespace Tryst\Contracts;

interface Mail{
    public function getEmailIntro();
    public function getEmailFooter();
    public function getEmailKey();
    public function setEmailKey($key);
    public function getEmailForm();
    public function getEmailFilePath();
    public function getEmailTitle();
}