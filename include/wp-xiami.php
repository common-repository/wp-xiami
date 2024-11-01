<div class="wrap" ng-app="WPXiaMiApp" ng-controller="WPXiaMiAppController">
    <div class="wrap-box">
        <h2>同步预览</h2>
        <div ng-controller="WPXiaMiAppAlertContronller">
            <div class="wp-xiami-alert" ng-repeat="alert in alerts" ng-class="alert.type && alert.type">
                <p ng-bind="alert.msg"></p>
                <span type="button" class="wp-xiami-close" ng-click="alert.close()"></span>
            </div>
        </div>
        <div class="theme-navigation">
            <span class="theme-count" ng-bind="(collects.data|WPXiaMiFilter:collects.type).length"></span>
            <ng-menu></ng-menu>
        </div>
        <div id="wp-xiami-main" ng-class="{loading: !collects.data.length}" ng-collect></div>
    </div>
    <div id="wp-xiami-sync-preview" ng-show="collects.selected!==null" ng-preview></div>
</div>