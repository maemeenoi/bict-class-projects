<?php
namespace Agora\Views\Registration;

use Agora\Core\AbstractView;
use Agora\Models\Region;
use Agora\Models\Business;

class CustomerRegistrationView extends AbstractView
{
    private $step = 1;
    private $error = null;

    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/../templates/registration/customer.php');
    }

    public function setStep($step)
    {
        $this->step = $step;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function prepare()
    {
        $this->setTemplateField('step', $this->step);
        $this->setTemplateField('error', $this->error);

        if ($this->step == 1) {
            $regionModel = new Region($this->getContext()->getDB());
            $regions = $regionModel->getAll();
            $this->setTemplateField('regions', $this->generateRegionsHtml($regions));
        } elseif ($this->step == 2) {
            $regionId = $this->getContext()->getURI()->getFilter('region_id');
            $businessModel = new Business($this->getContext()->getDB());
            $businesses = $businessModel->getByRegion($regionId);
            $this->setTemplateField('businesses', $this->generateBusinessesHtml($businesses));

            $role = $this->getContext()->getURI()->getFilter('role');
            $this->setTemplateField('role', $role);
        }
    }

    private function generateRegionsHtml($regions)
    {
        $html = '<select name="region_id" class="form-control" required>';
        $html .= '<option value="">Select Region</option>';
        foreach ($regions as $region) {
            $html .= '<option value="' . $region['region_id'] . '">'
                . htmlspecialchars($region['region_name']) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    private function generateBusinessesHtml($businesses)
    {
        $html = '<select name="business_id" class="form-control" required>';
        $html .= '<option value="">Select Business</option>';
        foreach ($businesses as $business) {
            $html .= '<option value="' . $business['business_id'] . '">'
                . htmlspecialchars($business['business_name']) . ' - '
                . htmlspecialchars($business['location_name']) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}