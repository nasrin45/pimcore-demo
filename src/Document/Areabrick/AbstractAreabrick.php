<?php

namespace App\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;

abstract class AbstractAreabrick extends AbstractTemplateAreabrick
{
    /**
     * @inheritdoc
     */
    public function getTemplateLocation(): string
    {
        return static::TEMPLATE_LOCATION_GLOBAL;
    }

    /**
     * @inheritdoc
     */
    public function getTemplateSuffix(): string
    {
        return static::getTemplateSuffix();
    }
}
