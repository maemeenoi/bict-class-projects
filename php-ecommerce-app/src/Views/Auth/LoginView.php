<?php
namespace Agora\Views\Auth;

use Agora\Core\AbstractView;

class LoginView extends AbstractView
{
    private $error = null;

    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/../templates/auth/login.php');
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function prepare()
    {
        // Set error if exists
        if ($this->error) {
            $this->setTemplateField('error', $this->error);
        }

        // Set feedback if exists
        $session = $this->getContext()->getSession();
        if ($session->isKeySet('feedback')) {
            $feedback = $session->get('feedback');
            $this->setTemplateField(
                'feedback',
                '<div class="alert alert-info">' . $feedback . '</div>'
            );
            $session->unsetKey('feedback');
        }

        // Set site URL
        $this->setTemplateField('site', $this->getContext()->getURI()->getSite());
    }
}