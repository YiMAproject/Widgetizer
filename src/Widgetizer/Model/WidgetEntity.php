<?php
namespace Widgetator\Model;

class WidgetEntity extends \Poirot\Dataset\Entity
{
    const WIDGET_ID  = 'widget_id';
    const UID        = 'uid';

    protected $properties = array(
        self::WIDGET_ID => null,      /* Widget Identity */
        self::UID       => null,      /* Widget App. Uniqe ID */
    );

    /**
     * Strict from unwanted attributes
     *
     * @var bool Strict Mode
     */
    // protected $strictMode = true;

    /**
     * Implement Entity as ResultSet
     *
     * @param array $data Data
     *
     * @return $this
     */
    public function exchangeArray($data)
    {
        $this->setProperties($data);

        return $this;
    }
}
