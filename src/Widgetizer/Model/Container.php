<?php
namespace Widgetizer\Model;

class Container extends \Poirot\Dataset\Entity
{
    const CONTAINER_ID      = 'container_id';      // table storage entity identifier (pk)
    const TEMPLATE          = 'template';          // template name
    const TEMPLATE_LAYOUT   = 'template_layout';   // layout name of template
    const TEMPLATE_AREA     = 'template_area';     // template area name, area is a region that template placed into
    const ROUTE_NAME        = 'route_name';        // name of page route
    const IDENTIFIER_PARAMS = 'identifier_params'; // this identifier help to mix four up tables with other params
                                                   // i suggest path/scheme/params as identifier value
    const WIDGET_UID        = 'widget_uid';        // identifier relation to widget table
    const ORDER             = 'order';             // order num.

    /**
     * @var array
     */
    protected $properties = array(
        self::CONTAINER_ID      => null,
        self::TEMPLATE          => null,
        self::TEMPLATE_LAYOUT   => null,
        self::TEMPLATE_AREA     => null,
        self::ROUTE_NAME        => null,
        self::IDENTIFIER_PARAMS => null,
        self::WIDGET_UID        => null,
        self::ORDER             => 5,
    );

    /**
     * Default value for empty entity value
     */
    protected static $defaultEmptyValue = '';

    /**
     * Strict from unwanted attributes
     *
     * @var bool Strict Mode
     */
     protected $strictMode = true;

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
