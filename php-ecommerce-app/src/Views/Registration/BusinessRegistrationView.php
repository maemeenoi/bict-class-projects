<?php
namespace Agora\Views\Registration;

use Agora\Core\AbstractView;
use Agora\Models\Region;
use Agora\Models\Business;

class BusinessRegistrationView extends AbstractView
{
    private $step = 1;
    private $error = null;

    public function __construct($context)
    {
        parent::__construct($context);
        $this->setTemplate(__DIR__ . '/../templates/registration/business.php');
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
        } elseif ($this->step > 1) {
            $regionId = $this->getContext()->getURI()->getFilter('region_id');
            $this->setTemplateField('region_id', $regionId);

            if ($this->step == 3) {
                $businessId = $this->getContext()->getURI()->getFilter('business_id');
                $this->setTemplateField('business_id', $businessId);

                // Get business data for auto-fill
                $businessModel = new Business($this->getContext()->getDB());
                $business = $businessModel->getById($businessId);
                if ($business) {
                    $this->setTemplateField('business_address', htmlspecialchars($business['address']));
                    $this->setTemplateField('business_phone', htmlspecialchars($business['phone']));
                }
            }
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
}