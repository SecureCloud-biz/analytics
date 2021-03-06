<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace dukt\analytics\widgets;

use Craft;
use craft\helpers\Json;
use dukt\analytics\Plugin as Analytics;
use dukt\analytics\web\assets\realtimereportwidget\RealtimeReportWidgetAsset;

class Realtime extends \craft\base\Widget
{
    // Properties
    // =========================================================================

    public $viewId;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function isSelectable(): bool
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('analytics');
        $settings = $plugin->getSettings();

        if (empty($settings['enableRealtime'])) {
            return false;
        }

        return parent::isSelectable();
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('analytics', 'Active users');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias('@dukt/analytics/icons/realtime-report.svg');
    }

    /**
     * @inheritDoc IWidget::getBodyHtml()
     *
     * @return string|false
     */
    public function getBodyHtml()
    {
        $view = Craft::$app->getView();

        if (Analytics::$plugin->getAnalytics()->checkPluginRequirements()) {
            if (Analytics::$plugin->getSettings()->enableWidgets) {
                $reportingViews = Analytics::$plugin->getViews()->getViews();

                if (count($reportingViews) > 0) {
                    $widgetSettings = $this->settings;

                    $reportingView = Analytics::$plugin->getViews()->getViewById($widgetSettings['viewId']);

                    if ($reportingView) {
                        $plugin = Craft::$app->getPlugins()->getPlugin('analytics');
                        $pluginSettings = $plugin->getSettings();

                        if (!empty($pluginSettings['enableRealtime'])) {
                            $realtimeRefreshInterval = Analytics::$plugin->getAnalytics()->getRealtimeRefreshInterval();

                            $widgetId = $this->id;
                            $widgetOptions = [
                                'viewId' => $widgetSettings['viewId'],
                                'refreshInterval' => $realtimeRefreshInterval,
                            ];

                            $view->registerTranslations('analytics', [
                                'Minutes ago',
                                'Pageviews',
                                '{count} minute ago',
                                '{count} minutes ago',
                            ]);

                            $view->registerJsFile('//www.gstatic.com/charts/loader.js');
                            $view->registerAssetBundle(RealtimeReportWidgetAsset::class);
                            $view->registerJs('var AnalyticsChartLanguage = "'.Craft::$app->language.'";', true);
                            $view->registerJs('new Analytics.Realtime("widget'.$widgetId.'", '.Json::encode($widgetOptions).');');

                            return $view->renderTemplate('analytics/_components/widgets/Realtime/body', [
                                'reportingView' => $reportingView
                            ]);
                        }

                        return $view->renderTemplate('analytics/_components/widgets/Realtime/disabled');
                    }

                    return $view->renderTemplate('analytics/_special/view-not-configured');
                }

                return $view->renderTemplate('analytics/_special/no-views');
            }

            return $view->renderTemplate('analytics/_components/widgets/Realtime/disabled');
        }

        return $view->renderTemplate('analytics/_special/not-connected');
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan()
    {
        return 1;
    }

    /**
     * @inheritDoc ISavableComponentType::getSettingsHtml()
     *
     * @return string
     */
    public function getSettingsHtml()
    {
        $settings = $this->getSettings();
        $reportingViews = Analytics::$plugin->getViews()->getViews();

        if (count($reportingViews) > 0) {
            return Craft::$app->getView()->renderTemplate('analytics/_components/widgets/Realtime/settings', [
                'settings' => $settings,
                'reportingViews' => $reportingViews,
            ]);
        }

        return null;
    }
}
