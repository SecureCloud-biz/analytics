<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2015, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace Craft;

class Analytics_MetadataController extends BaseController
{
    public function actionIndex(array $variables = array())
    {
        $variables['dimensions'] = craft()->analytics_metadata->getDimensions();
        $variables['metrics'] = craft()->analytics_metadata->getMetrics();

        $variables['metadataFileExists'] = craft()->analytics_metadata->metadataFileExists();

        $this->renderTemplate('analytics/metadata/_index', $variables);
    }

    public function actionSearch()
    {
        $q = craft()->request->getParam('q');
        $columns = craft()->analytics_metadata->searchColumns($q);

        // Send the source back to the template
        craft()->urlManager->setRouteVariables(array(
            'q' => $q,
            'columns' => $columns,
        ));
    }

    public function actionloadMetadata()
    {
        $this->deleteMetadata();
        $this->importMetadata();

        craft()->userSession->setNotice(Craft::t("Metadata loaded."));

        $referer = craft()->request->getUrlReferrer();
        $this->redirect($referer);
    }

    private function deleteMetadata()
    {
        $path = craft()->analytics_metadata->getMetadataFilePath();

        IOHelper::deleteFile($path);
    }

    private function importMetadata()
    {
        $columns = [];

        $metadataColumns = craft()->analytics_api->getMetadataColumns();

        if($metadataColumns)
        {
            $items = $metadataColumns->listMetadataColumns('ga');

            if($items)
            {
                foreach($items as $item)
                {
                    if($item->attributes['status'] == 'DEPRECATED')
                    {
                        continue;
                    }

                    if(isset($item->attributes['minTemplateIndex']))
                    {
                        for($i = $item->attributes['minTemplateIndex']; $i <= $item->attributes['maxTemplateIndex']; $i++)
                        {
                            $column = [];
                            $column['id'] = str_replace('XX', $i, $item->id);
                            $column['type'] = $item->attributes['type'];
                            $column['group'] = $item->attributes['group'];
                            $column['status'] = $item->attributes['status'];
                            $column['uiName'] = str_replace('XX', $i, $item->attributes['uiName']);
                            $column['description'] = str_replace('XX', $i, $item->attributes['description']);

                            if(isset($item->attributes['allowInSegments']))
                            {
                                $column['allowInSegments'] = $item->attributes['allowInSegments'];
                            }

                            $columns[$column['id']] = $column;
                        }
                    }
                    else
                    {
                        $column = [];
                        $column['id'] = $item->id;
                        $column['type'] = $item->attributes['type'];
                        $column['group'] = $item->attributes['group'];
                        $column['status'] = $item->attributes['status'];
                        $column['uiName'] = $item->attributes['uiName'];
                        $column['description'] = $item->attributes['description'];

                        if(isset($item->attributes['allowInSegments']))
                        {
                            $column['allowInSegments'] = $item->attributes['allowInSegments'];
                        }

                        $columns[$column['id']] = $column;
                    }
                }
            }
        }

        $contents = json_encode($columns);

        $path = craft()->analytics_metadata->getMetadataFilePath();

        $res = IOHelper::writeToFile($path, $contents);
    }
}