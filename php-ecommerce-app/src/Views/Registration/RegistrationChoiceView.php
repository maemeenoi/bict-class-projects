<?php
namespace Agora\Views\Registration;

use Agora\Core\AbstractView;

class RegistrationChoiceView extends AbstractView
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/../templates/registration/choice.php');
    }

    public function prepare()
    {
        $this->setTemplateField('site', $this->getContext()->getURI()->getSite());
    }
}