<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\LeadBundle\Event\LeadListFilteringEvent;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Event\LeadListQueryBuilderGeneratedEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SegmentFiltersSubscriber extends CommonSubscriber
{
    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var RecommenderClientModel
     */
    private $recommenderClientModel;

    /**
     * SegmentFiltersSubscriber constructor.
     *
     * @param ListModel              $listModel
     * @param RecommenderClientModel $recommenderClientModel
     */
    public function __construct(ListModel $listModel, RecommenderClientModel $recommenderClientModel)
    {

        $this->listModel = $listModel;
        $this->recommenderClientModel = $recommenderClientModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LIST_FILTERS_CHOICES_ON_GENERATE => [
                ['onListFiltersGenerate', 0],
            ],

            LeadEvents::LIST_FILTERS_ON_FILTERING => [
                ['onListFiltersFiltering', 0],
            ],
        ];
    }

    public function onListFiltersFiltering(LeadListFilteringEvent $event)
    {
        $qb =$event->getQueryBuilder();
        $details = $event->getDetails();
        if ($details['field'] == 'customfilter') {
        /*
         * (
    [glue] => and
    [field] => customfilter
    [object] => lead
    [type] => text
    [filter] => something
    [display] =>
    [operator] => =
)

         * */
        }
    }

    /**
     * @param BuildJsEvent $event
     */
    public function onListFiltersGenerate(LeadListFiltersChoicesEvent $event)
    {
        $properties = $this->recommenderClientModel->getItemPropertyValueRepository()->getItemValueProperties();

        foreach ($properties as $property) {
            $type = RecommenderHelper::typeToTypeTranslator($property['type']);
            $config = [
                'label'      => $property['name'],
                'properties' => [
                    'type' => $type,
                ],
                'operators' => $this->listModel->getOperatorsForFieldType($type),
            ];
            $event->addChoice('lead', $property['id'], $config);
        }
    }


}