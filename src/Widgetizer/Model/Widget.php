<?php
namespace Widgetizer\Model;

class Widget extends \Poirot\Dataset\Entity
{
    const WIDGET_ID  = 'widget_id';  // Entity Storage ID
    const WIDGET     = 'widget';     // Widget Name
    const UID        = 'uid';        // Unique Application ID for widget

    /**
     * note: Other entities filled from dms feature
     *
     * @var array
     */
    protected $properties = array(
        self::WIDGET_ID => null,
        self::WIDGET    => null,
        self::UID       => null,

        // etc. Dms Feature
        // ...
    );

    /**
     * Strict from unwanted attributes
     *
     * @var bool Strict Mode
     */
     # protected $strictMode = true;

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
